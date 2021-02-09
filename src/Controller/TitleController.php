<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\ImageResize;
use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use App\Pagination\DoctrinePaginator;
use DateTime;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use Psr\Http\Message\StreamInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Title controller
 */
class TitleController extends BaseController
{
    use ImageResize;

    protected function preExecute(Request $request, Response $response): void
    {
        $this->authorization();

        parent::preExecute($request, $response);
    }

    /**
     * @throws ForbiddenException
     */
    protected function authorization(): void
    {
        $user = $this->auth->getUser();

        if ($user->isTheater()) {
            throw new ForbiddenException();
        }
    }

    /**
     * list action
     *
     * @param array<string, mixed> $args
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $page = (int) $request->getParam('p', 1);

        $form = new Form\TitleFindForm();
        $form->setData($request->getParams());

        $cleanValues = [];
        $errors      = [];

        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values      = $cleanValues;
        } else {
            $values = $request->getParams();
            $errors = $form->getMessages();
        }

        /** @var DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\Title::class)->findForList($cleanValues, $page);

        return $this->render($response, 'title/list.html.twig', [
            'page' => $page,
            'values' => $values,
            'errors' => $errors,
            'params' => $cleanValues,
            'pagenater' => $pagenater,
        ]);
    }

    /**
     * new action
     *
     * @param array<string, mixed> $args
     */
    public function executeNew(Request $request, Response $response, array $args): Response
    {
        $form = new Form\TitleForm();

        return $this->renderNew($response, ['form' => $form]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderNew(Response $response, array $data = []): Response
    {
        return $this->render($response, 'title/new.html.twig', $data);
    }

    /**
     * create action
     *
     * @param array<string, mixed> $args
     */
    public function executeCreate(Request $request, Response $response, array $args): Response
    {
        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\TitleForm();
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderNew($response, [
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $title = $this->doCreate($cleanData);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を追加しました。', $title->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('title_edit', ['id' => $title->getId()]),
            303
        );
    }

    /**
     * do create
     *
     * @param array<string, mixed> $data
     */
    protected function doCreate(array $data): Entity\Title
    {
        $image = $data['image'];
        $file  = null;

        if ($image['name']) {
            $file = $this->uploadImage($image);

            $this->em->persist($file);
        }

        $title = new Entity\Title();
        $title->setImage($file);
        $title->setName($data['name']);
        $title->setNameKana($data['name_kana']);
        $title->setNameOriginal($data['name_original']);
        $title->setCredit($data['credit']);
        $title->setCatchcopy($data['catchcopy']);
        $title->setIntroduction($data['introduction']);
        $title->setDirector($data['director']);
        $title->setCast($data['cast']);
        $title->setPublishingExpectedDate($data['publishing_expected_date']);
        $title->setOfficialSite($data['official_site']);
        $title->setRating((int) $data['rating']);
        $title->setUniversal($data['universal'] ?? []);
        $title->setCreatedUser($this->auth->getUser());
        $title->setUpdatedUser($this->auth->getUser());

        $this->em->persist($title);
        $this->em->flush();

        $this->logger->info('Created Title "{id}"', [
            'id' => $title->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        return $title;
    }

    protected function resizeTitleImage(string $path): StreamInterface
    {
        // SASAKI-245
        return $this->resizeImage($path, null, 1920);
    }

    /**
     * upload image
     *
     * @param array<string, mixed> $image
     */
    protected function uploadImage(array $image): Entity\File
    {
        $newName = Entity\File::createName($image['name']);

        $imageStream = $this->resizeTitleImage($image['tmp_name']);

        $options = new CreateBlockBlobOptions();
        $options->setContentType($image['type']);
        $createBlobResult = $this->bc->createBlockBlob(
            Entity\File::getBlobContainer(),
            $newName,
            $imageStream,
            $options
        );

        $this->logger->info('Created Blob {name}', [
            'name' => $newName,
            'e_tag' => $createBlobResult->getETag(),
            'last_modified' => $createBlobResult->getLastModified(),
            'content_md5' => $createBlobResult->getContentMD5(),
            'request_server_encrypted' => $createBlobResult->getRequestServerEncrypted(),
        ]);

        $file = new Entity\File();
        $file->setName($newName);
        $file->setOriginalName($image['name']);
        $file->setMimeType($image['type']);
        $file->setSize($imageStream->getSize());

        return $file;
    }

    /**
     * edit action
     *
     * @param array<string, mixed> $args
     */
    public function executeEdit(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\Title|null $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById((int) $args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        $form = new Form\TitleForm();

        $values = [
            'id'            => $title->getId(),
            'name'          => $title->getName(),
            'name_kana'     => $title->getNameKana(),
            'name_original' => $title->getNameOriginal(),
            'credit'        => $title->getCredit(),
            'catchcopy'     => $title->getCatchcopy(),
            'introduction'  => $title->getIntroduction(),
            'director'      => $title->getDirector(),
            'cast'          => $title->getCast(),
            'official_site' => $title->getOfficialSite(),
            'rating'        => $title->getRating(),
            'universal'     => $title->getUniversal(),
        ];

        $publishingExpectedDate = $title->getPublishingExpectedDate();

        if ($publishingExpectedDate instanceof DateTime) {
            $values['publishing_expected_date']           = $publishingExpectedDate->format('Y/m/d');
            $values['not_exist_publishing_expected_date'] = null;
        } else {
            $values['publishing_expected_date']           = null;
            $values['not_exist_publishing_expected_date'] = '1';
        }

        return $this->renderEdit($response, [
            'title' => $title,
            'form' => $form,
            'values' => $values,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderEdit(Response $response, array $data = []): Response
    {
        return $this->render($response, 'title/edit.html.twig', $data);
    }

    /**
     * update action
     *
     * @param array<string, mixed> $args
     */
    public function executeUpdate(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\Title|null $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById((int) $args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\TitleForm();
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'title' => $title,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $this->doUpdate($title, $cleanData);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を編集しました。', $title->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('title_edit', ['id' => $title->getId()]),
            303
        );
    }

    /**
     * do update
     *
     * @param array<string, mixed> $data
     */
    protected function doUpdate(Entity\Title $title, array $data): void
    {
        $image         = $data['image'];
        $isDeleteImage = $data['delete_image'] || $image['name'];
        $oldImage      = null;

        if ($isDeleteImage && $title->getImage()) {
            /**
             * ストレージのファイル削除はDBトランザクション後に行う。
             * 先に削除してしまうとロールバックした時にファイルは戻せないので。
             */
            $oldImage = $title->getImage();
            $title->setImage(null);
            $this->em->remove($oldImage);

            $this->logger->info('Delete title image "{id}"', [
                'id' => $oldImage->getId(),
                'name' => $oldImage->getName(),
            ]);
        }

        if ($image['name']) {
            $file = $this->uploadImage($image);
            $this->em->persist($file);
            $title->setImage($file);
        }

        $title->setName($data['name']);
        $title->setNameKana($data['name_kana']);
        $title->setNameOriginal($data['name_original']);
        $title->setCredit($data['credit']);
        $title->setCatchcopy($data['catchcopy']);
        $title->setIntroduction($data['introduction']);
        $title->setDirector($data['director']);
        $title->setCast($data['cast']);
        $title->setPublishingExpectedDate($data['publishing_expected_date']);
        $title->setOfficialSite($data['official_site']);
        $title->setRating((int) $data['rating']);
        $title->setUniversal($data['universal'] ?? []);
        $title->setUpdatedUser($this->auth->getUser());

        $this->em->flush();

        $this->logger->info('Updated Title "{id}"', [
            'id' => $title->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        if ($oldImage) {
            $this->deleteImage($oldImage);
        }
    }

    /**
     * delete image
     *
     * 現状、ストレージのロールバックは出来ないのでDBを優先。
     * 削除エラーは要注意として、ひとまず正常処理に戻す。
     */
    protected function deleteImage(Entity\File $image): void
    {
        try {
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $image->getName());

            $this->logger->info('Deleted Blob {name}', [
                'name' => $image->getName(),
            ]);
        } catch (ServiceException $e) {
            $this->logger->warning($e->getErrorText(), [
                'blob' => $image->getName(),
                'code' => $e->getCode(),
                'message' => $e->getErrorMessage(),
                'request_id' => $e->getRequestID(),
            ]);
        }
    }

    /**
     * delete action
     *
     * @param array<string, mixed> $args
     */
    public function executeDelete(Request $request, Response $response, array $args): void
    {
        /** @var Entity\Title|null $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById((int) $args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        $title->setIsDeleted(true);
        $title->setUpdatedUser($this->auth->getUser());

        // 関連データの処理はイベントで対応する

        $this->em->persist($title);
        $this->em->flush();

        $this->logger->info('Deleted Title "{id}"', [
            'id' => $title->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を削除しました。', $title->getName()),
        ]);

        $this->redirect($this->router->pathFor('title_list'), 303);
    }
}
