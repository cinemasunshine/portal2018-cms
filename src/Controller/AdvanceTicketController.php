<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\AdvanceSaleForm;
use App\Form\AdvanceTicketFindForm;
use App\ORM\Entity\AdvanceSale;
use App\ORM\Entity\AdvanceTicket;
use App\ORM\Entity\File;
use App\ORM\Entity\Theater;
use App\ORM\Entity\Title;
use App\ORM\Repository\AdvanceSaleRepository;
use App\ORM\Repository\AdvanceTicketRepository;
use App\ORM\Repository\TheaterRepository;
use App\ORM\Repository\TitleRepository;
use DateTime;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use RuntimeException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class AdvanceTicketController extends BaseController
{
    protected function getAdvanceTicketRepository(): AdvanceTicketRepository
    {
        return $this->em->getRepository(AdvanceTicket::class);
    }

    protected function getAdvanceSaleRepository(): AdvanceSaleRepository
    {
        return $this->em->getRepository(AdvanceSale::class);
    }

    protected function getTheaterRepository(): TheaterRepository
    {
        return $this->em->getRepository(Theater::class);
    }

    protected function getTitleRepository(): TitleRepository
    {
        return $this->em->getRepository(Title::class);
    }

    /**
     * list action
     *
     * @param array<string, mixed> $args
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $page = (int) $request->getParam('p', 1);

        $form = new AdvanceTicketFindForm();
        $form->setData($request->getParams());
        $cleanValues = [];

        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values      = $cleanValues;
        } else {
            $values = $request->getParams();
        }

        $user = $this->auth->getUser();

        if ($user->isTheater()) {
            // ひとまず検索のパラメータとして扱う
            $cleanValues['theater'] = [$user->getTheater()->getId()];
        }

        $pagenater = $this->getAdvanceTicketRepository()
            ->findForList($cleanValues, $page);

        return $this->render($response, 'advance_ticket/list.html.twig', [
            'form' => $form,
            'errors' => $form->getMessages(),
            'values' => $values,
            'params' => $cleanValues,
            'pagenater' => $pagenater,
        ]);
    }

    protected function getForm(int $type): AdvanceSaleForm
    {
        return new AdvanceSaleForm($type, $this->em, $this->auth->getUser());
    }

    /**
     * new action
     *
     * @param array<string, mixed> $args
     */
    public function executeNew(Request $request, Response $response, array $args): Response
    {
        $form = $this->getForm(AdvanceSaleForm::TYPE_NEW);

        return $this->renderNew($response, ['form' => $form]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderNew(Response $response, array $data): Response
    {
        return $this->render($response, 'advance_ticket/new.html.twig', $data);
    }

    /**
     * create action
     *
     * @param array<string, mixed> $args
     */
    public function executeCreate(Request $request, Response $response, array $args): Response
    {
        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = AdvanceSaleForm::buildData($request->getParams(), $_FILES);

        $form = $this->getForm(AdvanceSaleForm::TYPE_NEW);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderNew($response, [
                'form' => $form,
                'values' => $params,
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $advanceSale = new AdvanceSale();

        $theater = $this->getTheaterRepository()
            ->findOneById((int) $cleanData['theater']);
        $advanceSale->setTheater($theater);

        $title = $this->getTitleRepository()
            ->findOneById((int) $cleanData['title_id']);
        $advanceSale->setTitle($title);

        $advanceSale->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $advanceSale->setPublishingExpectedDateText($cleanData['publishing_expected_date_text']);
        $advanceSale->setCreatedUser($this->auth->getUser());
        $advanceSale->setUpdatedUser($this->auth->getUser());

        $this->em->persist($advanceSale);

        foreach ($cleanData['tickets'] as $ticket) {
            $advanceTicket = new AdvanceTicket();
            $advanceTicket->setAdvanceSale($advanceSale);
            $advanceTicket->setPublishingStartDt($ticket['publishing_start_dt']);
            $advanceTicket->setReleaseDt($ticket['release_dt']);
            $advanceTicket->setReleaseDtText($ticket['release_dt_text']);
            $advanceTicket->setIsSalesEnd($ticket['is_sales_end'] === '1');
            $advanceTicket->setType((int) $ticket['type']);
            $advanceTicket->setPriceText($ticket['price_text']);
            $advanceTicket->setSpecialGift($ticket['special_gift']);
            $advanceTicket->setSpecialGiftStock((int) $ticket['special_gift_stock'] ?: null);

            $image = $ticket['special_gift_image'];
            $file  = null;

            if ($image['name']) {
                // rename
                $newName = File::createName($image['name']);

                // upload storage
                // @todo storageと同期するような仕組みをFileへ
                $options = new CreateBlockBlobOptions();
                $options->setContentType($image['type']);
                $this->bc->createBlockBlob(
                    File::getBlobContainer(),
                    $newName,
                    fopen($image['tmp_name'], 'r'),
                    $options
                );

                $file = new File();
                $file->setName($newName);
                $file->setOriginalName($image['name']);
                $file->setMimeType($image['type']);
                $file->setSize((int) $image['size']);

                $this->em->persist($file);
            }

            $advanceTicket->setSpecialGiftImage($file);

            $this->em->persist($advanceTicket);
        }

        $this->em->flush();

        $this->logger->info('Created AdvanceSale "{id}"', [
            'id' => $advanceSale->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を追加しました。',
        ]);

        $this->redirect(
            $this->router->pathFor('advance_ticket_edit', ['id' => $advanceSale->getId()]),
            303
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderEdit(Response $response, array $data): Response
    {
        return $this->render($response, 'advance_ticket/edit.html.twig', $data);
    }

    /**
     * edit action
     *
     * @param array<string, mixed> $args
     */
    public function executeEdit(Request $request, Response $response, array $args): Response
    {
        $advanceSale = $this->getAdvanceSaleRepository()
            ->findOneById((int) $args['id']);

        if (is_null($advanceSale)) {
            throw new NotFoundException($request, $response);
        }

        $form = $this->getForm(AdvanceSaleForm::TYPE_EDIT);

        $values = [
            'id'         => $advanceSale->getId(),
            'theater'    => $advanceSale->getTheater()->getId(),
            'title_id'   => $advanceSale->getTitle()->getId(),
            'title_name' => $advanceSale->getTitle()->getName(),
            'publishing_expected_date_text' => $advanceSale->getPublishingExpectedDateText(),
            'tickets'    => [],
        ];

        $publishingExpectedDate = $advanceSale->getPublishingExpectedDate();

        if ($publishingExpectedDate instanceof DateTime) {
            $values['publishing_expected_date']           = $publishingExpectedDate->format('Y/m/d');
            $values['not_exist_publishing_expected_date'] = null;
        } else {
            $values['publishing_expected_date']           = null;
            $values['not_exist_publishing_expected_date'] = '1';
        }

        foreach ($advanceSale->getActiveAdvanceTickets() as $advanceTicket) {
            /** @var AdvanceTicket $advanceTicket */
            $ticket = [
                'id'                  => $advanceTicket->getId(),
                'publishing_start_dt' => $advanceTicket->getPublishingStartDt()->format('Y/m/d H:i'),
                'release_dt'          => $advanceTicket->getReleaseDt()->format('Y/m/d H:i'),
                'release_dt_text'     => $advanceTicket->getReleaseDtText(),
                'is_sales_end'        => $advanceTicket->getIsSalesEnd() ? '1' : '0',
                'type'                => $advanceTicket->getType(),
                'price_text'          => $advanceTicket->getPriceText(),
                'special_gift'        => $advanceTicket->getSpecialGift(),
                'special_gift_stock'  => $advanceTicket->getSpecialGiftStock(),
            ];

            $values['tickets'][] = $ticket;
        }

        return $this->renderEdit($response, [
            'advanceSale' => $advanceSale,
            'form' => $form,
            'values' => $values,
        ]);
    }

    /**
     * update action
     *
     * @param array<string, mixed> $args
     */
    public function executeUpdate(Request $request, Response $response, array $args): Response
    {
        $advanceSale = $this->getAdvanceSaleRepository()
            ->findOneById((int) $args['id']);

        if (is_null($advanceSale)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = AdvanceSaleForm::buildData($request->getParams(), $_FILES);

        $form = $this->getForm(AdvanceSaleForm::TYPE_EDIT);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'advanceSale' => $advanceSale,
                'form' => $form,
                'values' => $params,
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $theater = $this->getTheaterRepository()
            ->findOneById((int) $cleanData['theater']);
        $advanceSale->setTheater($theater);

        $title = $this->getTitleRepository()
            ->findOneById((int) $cleanData['title_id']);
        $advanceSale->setTitle($title);

        $advanceSale->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $advanceSale->setPublishingExpectedDateText($cleanData['publishing_expected_date_text']);
        $advanceSale->setUpdatedUser($this->auth->getUser());

        $advanceTickets = $advanceSale->getActiveAdvanceTickets();

        if (is_array($cleanData['delete_tickets'])) {
            // 前売券削除

            foreach ($cleanData['delete_tickets'] as $advanceTicketId) {
                /**
                 * indexByでidをindexにしている
                 *
                 * @var AdvanceTicket|null $advanceTicket
                 */
                $advanceTicket = $advanceTickets->get($advanceTicketId);

                if (
                    ! $advanceTicket
                    || $advanceTicket->getId() !== (int) $advanceTicketId // 念のため確認
                ) {
                    throw new RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $advanceTicketId));
                }

                $advanceTicket->setIsDeleted(true);
            }
        }

        foreach ($cleanData['tickets'] as $ticket) {
            if ($ticket['id']) {
                // 前売券編集

                /**
                 * indexByでidをindexにしている
                 *
                 * @var AdvanceTicket|null $advanceTicket
                 */
                $advanceTicket = $advanceTickets->get($ticket['id']);

                if (
                    ! $advanceTicket
                    || $advanceTicket->getId() !== (int) $ticket['id'] // 念のため確認
                ) {
                    throw new RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $ticket['id']));
                }
            } else {
                // 前売券登録

                $advanceTicket = new AdvanceTicket();
                $this->em->persist($advanceTicket);

                $advanceTicket->setAdvanceSale($advanceSale);
            }

            $advanceTicket->setPublishingStartDt($ticket['publishing_start_dt']);
            $advanceTicket->setReleaseDt($ticket['release_dt']);
            $advanceTicket->setReleaseDtText($ticket['release_dt_text']);
            $advanceTicket->setIsSalesEnd($ticket['is_sales_end'] === '1');
            $advanceTicket->setType((int) $ticket['type']);
            $advanceTicket->setPriceText($ticket['price_text']);
            $advanceTicket->setSpecialGift($ticket['special_gift']);
            $advanceTicket->setSpecialGiftStock((int) $ticket['special_gift_stock'] ?: null);

            $image         = $ticket['special_gift_image'];
            $isDeleteImage = ($ticket['delete_special_gift_image'] === '1') || $image['name'];

            if ($isDeleteImage && $advanceTicket->getSpecialGiftImage()) {
                // @todo preUpdateで出来ないか？ hasChangedField()
                $oldImage = $advanceTicket->getSpecialGiftImage();
                $this->em->remove($oldImage);

                // @todo postRemoveイベントへ
                $this->bc->deleteBlob(File::getBlobContainer(), $oldImage->getName());

                $advanceTicket->setSpecialGiftImage(null);
            }

            if (! $image['name']) {
                continue;
            }

            // rename
            $newName = File::createName($image['name']);

            // upload storage
            // @todo storageと同期するような仕組みをFileへ
            $options = new CreateBlockBlobOptions();
            $options->setContentType($image['type']);
            $this->bc->createBlockBlob(
                File::getBlobContainer(),
                $newName,
                fopen($image['tmp_name'], 'r'),
                $options
            );

            $file = new File();
            $file->setName($newName);
            $file->setOriginalName($image['name']);
            $file->setMimeType($image['type']);
            $file->setSize((int) $image['size']);

            $this->em->persist($file);

            $advanceTicket->setSpecialGiftImage($file);
        }

        $this->em->flush();

        $this->logger->info('Updated AdvanceSale "{id}"', [
            'id' => $advanceSale->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を編集しました。',
        ]);

        $this->redirect(
            $this->router->pathFor('advance_ticket_edit', ['id' => $advanceSale->getId()]),
            303
        );
    }

    /**
     * delete action
     *
     * @param array<string, mixed> $args
     */
    public function executeDelete(Request $request, Response $response, array $args): void
    {
        $advanceTicket = $this->getAdvanceTicketRepository()
            ->findOneById((int) $args['id']);

        if (is_null($advanceTicket)) {
            throw new NotFoundException($request, $response);
        }

        // 関連データの処理はイベントで対応する
        $advanceTicket->setIsDeleted(true);

        $advanceSale = $advanceTicket->getAdvanceSale();
        $advanceSale->setUpdatedUser($this->auth->getUser());

        // 有効なAdvanceTicketの件数確認
        if ($advanceSale->getActiveAdvanceTickets()->count() === 1) {
            // この処理で有効なAdvanceTicketが無くなるのでAdvanceSaleも削除する
            $advanceSale->setIsDeleted(true);
        }

        $this->em->flush();

        $this->logger->info('Deleted AdvanceSale "{id}"', [
            'id' => $advanceSale->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を削除しました。',
        ]);

        $this->redirect($this->router->pathFor('advance_ticket_list'), 303);
    }
}
