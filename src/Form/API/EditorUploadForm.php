<?php

declare(strict_types=1);

namespace App\Form\API;

use App\Form\BaseForm;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * EditorUpload form class
 */
class EditorUploadForm extends BaseForm
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->setup();
    }

    /**
     * setup
     */
    protected function setup(): void
    {
        $this->add([
            'name' => 'file',
            'type' => 'File',
        ]);

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'file',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\File\Size::class,
                    'options' => ['max' => '10MB'],
                ],
                [
                    'name' => Validator\File\MimeType::class,
                    'options' => [
                        'mimeType' => self::$imageMimeTypes,
                    ],
                ],
            ],
        ]);

        $this->setInputFilter($inputFilter);
    }
}
