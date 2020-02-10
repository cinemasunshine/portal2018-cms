<?php
/**
 * TitleController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Cinemasunshine\PortalAdmin\Exception\ForbiddenException;
use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * Title controller
 */
class TitleController extends BaseController
{
    use ImageManagerTrait;

    /**
     * {@inheritDoc}
     */
    protected function preExecute($request, $response): void
    {
        $user = $this->auth->getUser();

        if ($user->isTheater()) {
            throw new ForbiddenException();
        }

        parent::preExecute($request, $response);
    }

    /**
     * list action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeList($request, $response, $args)
    {
        $page = (int) $request->getParam('p', 1);
        $this->data->set('page', $page);

        $form = new Form\TitleFindForm();
        $form->setData($request->getParams());
        $cleanValues = [];

        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values = $cleanValues;
        } else {
            $values = $request->getParams();
            $this->data->set('errors', $form->getMessages());
        }

        $this->data->set('values', $values);
        $this->data->set('params', $cleanValues);

        /** @var \Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\Title::class)->findForList($cleanValues, $page);

        $this->data->set('pagenater', $pagenater);
    }

    /**
     * new action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeNew($request, $response, $args)
    {
        $form = new Form\TitleForm();
        $this->data->set('form', $form);
    }

    /**
     * create action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeCreate($request, $response, $args)
    {
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\TitleForm();
        $form->setData($params);

        if (!$form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'new';
        }

        $cleanData = $form->getData();

        $image = $cleanData['image'];
        $file = null;

        if ($image['name']) {
            // rename
            $newName = Entity\File::createName($image['name']);

            // SASAKI-245
            $imageStream = $this->resizeImage($image['tmp_name'], null, 1920);

            // upload storage
            // @todo storageと同期するような仕組みをFileへ
            $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
            $options->setContentType($image['type']);
            $this->bc->createBlockBlob(
                Entity\File::getBlobContainer(),
                $newName,
                $imageStream,
                $options
            );

            $file = new Entity\File();
            $file->setName($newName);
            $file->setOriginalName($image['name']);
            $file->setMimeType($image['type']);
            $file->setSize($imageStream->getSize());

            $this->em->persist($file);
        }

        $title = new Entity\Title();
        $title->setImage($file);
        $title->setName($cleanData['name']);
        $title->setNameKana($cleanData['name_kana']);
        $title->setNameOriginal($cleanData['name_original']);
        $title->setCredit($cleanData['credit']);
        $title->setCatchcopy($cleanData['catchcopy']);
        $title->setIntroduction($cleanData['introduction']);
        $title->setDirector($cleanData['director']);
        $title->setCast($cleanData['cast']);
        $title->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $title->setOfficialSite($cleanData['official_site']);
        $title->setRating((int) $cleanData['rating']);
        $title->setUniversal($cleanData['universal'] ?? []);
        $title->setCreatedUser($this->auth->getUser());
        $title->setUpdatedUser($this->auth->getUser());

        $this->em->persist($title);
        $this->em->flush();

        $this->logger->info('Created Title "{id}"', [
            'id' => $title->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を追加しました。', $title->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('title_edit', [ 'id' => $title->getId() ]),
            303
        );
    }

    /**
     * edit action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeEdit($request, $response, $args)
    {
        $title = $this->em->getRepository(Entity\Title::class)->findOneById($args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Title $title */

        $this->data->set('title', $title);

        $form = new Form\TitleForm();
        $this->data->set('form', $form);

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

        if ($publishingExpectedDate instanceof \DateTime) {
            $values['publishing_expected_date'] = $publishingExpectedDate->format('Y/m/d');
            $values['not_exist_publishing_expected_date'] = null;
        } else {
            $values['publishing_expected_date'] = null;
            $values['not_exist_publishing_expected_date'] = '1';
        }

        $this->data->set('values', $values);
    }

    /**
     * update action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeUpdate($request, $response, $args)
    {
        $title = $this->em->getRepository(Entity\Title::class)->findOneById($args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Title $title */

        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\TitleForm();
        $form->setData($params);

        if (!$form->isValid()) {
            $this->data->set('title', $title);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'edit';
        }

        $cleanData = $form->getData();

        $image = $cleanData['image'];
        $isDeleteImage = $cleanData['delete_image'] || $image['name'];

        if ($isDeleteImage && $title->getImage()) {
            // @todo preUpdateで出来ないか？ hasChangedField()
            $oldImage = $title->getImage();
            $this->em->remove($oldImage);

            // @todo postRemoveイベントへ
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());

            $title->setImage(null);
        }

        if ($image['name']) {
            // rename
            $newName = Entity\File::createName($image['name']);

            // SASAKI-245
            $imageStream = $this->resizeImage($image['tmp_name'], null, 1920);

            // upload storage
            // @todo storageと同期するような仕組みをFileへ
            $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
            $options->setContentType($image['type']);
            $this->bc->createBlockBlob(
                Entity\File::getBlobContainer(),
                $newName,
                $imageStream,
                $options
            );

            $file = new Entity\File();
            $file->setName($newName);
            $file->setOriginalName($image['name']);
            $file->setMimeType($image['type']);
            $file->setSize($imageStream->getSize());

            $this->em->persist($file);

            $title->setImage($file);
        }

        $title->setName($cleanData['name']);
        $title->setNameKana($cleanData['name_kana']);
        $title->setNameOriginal($cleanData['name_original']);
        $title->setCredit($cleanData['credit']);
        $title->setCatchcopy($cleanData['catchcopy']);
        $title->setIntroduction($cleanData['introduction']);
        $title->setDirector($cleanData['director']);
        $title->setCast($cleanData['cast']);
        $title->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $title->setOfficialSite($cleanData['official_site']);
        $title->setRating((int) $cleanData['rating']);
        $title->setUniversal($cleanData['universal'] ?? []);
        $title->setUpdatedUser($this->auth->getUser());
        $title->setIsDeleted(false);

        $this->em->persist($title);
        $this->em->flush();

        $this->logger->info('Updated Title "{id}"', [
            'id' => $title->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を編集しました。', $title->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('title_edit', [ 'id' => $title->getId() ]),
            303
        );
    }

    /**
     * delete action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeDelete($request, $response, $args)
    {
        $title = $this->em->getRepository(Entity\Title::class)->findOneById($args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Title $title */

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
