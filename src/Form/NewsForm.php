<?php

/**
 * NewsForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Form;

use App\ORM\Entity\News;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * News form class
 */
class NewsForm extends BaseForm
{
    public const TYPE_NEW  = 1;
    public const TYPE_EDIT = 2;

    /** @var int */
    protected $type;

    /** @var array */
    protected $categoryChoices;

    /**
     * construct
     *
     * @param int $type
     */
    public function __construct(int $type)
    {
        $this->type            = $type;
        $this->categoryChoices = News::$categories;

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
        if ($this->type === self::TYPE_EDIT) {
            $this->add([
                'name' => 'id',
                'type' => 'Hidden',
            ]);
        }

        $this->add([
            'name' => 'category',
            'type' => 'Radio',
            'options' => [
                'value_options' => $this->categoryChoices,
            ],
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
            'name' => 'headline',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'body',
            'type' => 'Textarea',
        ]);

        $this->add([
            'name' => 'image',
            'type' => 'File',
        ]);

        // @todo 編集かつ画像があるとき
        $this->add([
            'name' => 'delete_image',
            'type' => 'Checkbox',
        ]);


        $inputFilter = new InputFilter();

        if ($this->type === self::TYPE_EDIT) {
            $inputFilter->add([
                'name' => 'id',
                'required' => true,
            ]);
        }

        $inputFilter->add([
            'name' => 'category',
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
            'name' => 'headline',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'body',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'image',
            'required' => false,
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

        // @todo 編集かつ画像があるとき
        $inputFilter->add([
            'name' => 'delete_image',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }

    /**
     * return category choices
     *
     * @return array
     */
    public function getCategoryChoices()
    {
        return $this->categoryChoices;
    }
}
