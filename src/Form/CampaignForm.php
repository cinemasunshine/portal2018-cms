<?php

namespace App\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * Campaign form class
 */
class CampaignForm extends BaseForm
{
    public const TYPE_NEW  = 1;
    public const TYPE_EDIT = 2;

    /** @var int */
    protected $type;

    /**
     * construct
     *
     * @param int $type
     */
    public function __construct($type)
    {
        $this->type = $type;

        parent::__construct();

        $this->setup();
    }

    /**
     * setup
     *
     * @return void
     */
    protected function setup()
    {
        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'title_id',
            'type' => 'Hidden',
        ]);

        // 作品名を表示するため
        $this->add([
            'name' => 'title_name',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'start_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'end_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'url',
            'type' => 'Url',
        ]);

        $this->add([
            'name' => 'image',
            'type' => 'File',
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'title_id',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'title_name',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'start_dt',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'end_dt',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'url',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'image',
            'required' => ($this->type === self::TYPE_NEW),
            'validators' => [
                [
                    'name' => Validator\File\Size::class,
                    'options' => ['max' => '10MB'], // SASAKI-245
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
