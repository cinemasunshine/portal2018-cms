<?php

/**
 * EditorController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller\API;

use App\Controller\Traits\AzureBlobStorage;
use App\Form;
use App\Form\API as ApiForm;

/**
 * Editor API controller
 */
class EditorController extends BaseController
{
    use AzureBlobStorage;

    /**
     * Blob Container name
     *
     * @var string
     */
    protected $blobContainer = 'editor';

    /**
     * upload action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeUpload($request, $response, $args)
    {
        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new ApiForm\EditorUploadForm();
        $form->setData($params);

        if (! $form->isValid()) {
            $errors   = [];
            $messages = $form->getMessages()['file'];

            foreach ($messages as $message) {
                $errors[] = ['title' => $message];
            }

            $this->data->set('errors', $errors);
            return;
        }

        $cleanData = $form->getData();

        $file = $cleanData['file'];

        // rename
        $info     = pathinfo($file['name']);
        $blobName = md5(uniqid('', true)) . '.' . $info['extension'];

        // upload storage
        $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
        $options->setContentType($file['type']);
        $this->bc->createBlockBlob(
            $this->blobContainer,
            $blobName,
            fopen($file['tmp_name'], 'r'),
            $options
        );

        $url = $this->getBlobUrl($this->blobContainer, $blobName);

        $data = [
            'name' => $blobName,
            'url'  => $url,
        ];

        $this->data->set('data', $data);
    }
}
