<?php

namespace App\Form;

use App\ORM\Entity\ShowingFormat;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * Schedule find form class
 */
class ScheduleFindForm extends BaseForm
{
    public const STATUS_SHOWING = 1;
    public const STATUS_BEFORE  = 2;
    public const STATUS_END     = 3;

    /** @var array */
    protected $statusChoices = [
        self::STATUS_SHOWING => '上映中',
        self::STATUS_BEFORE  => '上映予定',
        self::STATUS_END     => '上映終了',
    ];

    /** @var array */
    protected $formatSystemChoices;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->formatSystemChoices = ShowingFormat::getSystemList();

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
            'name' => 'title_name',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'status',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->statusChoices,
            ],
        ]);

        $this->add([
            'name' => 'format_system',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->formatSystemChoices,
            ],
        ]);

        $this->add([
            'name' => 'public_start_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'public_end_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'title_name',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'status',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'format_system',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'public_start_dt',
            'required' => false,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'public_end_dt',
            'required' => false,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $this->setInputFilter($inputFilter);
    }

    /**
     * return status choices
     *
     * @return array
     */
    public function getStatusChoices()
    {
        return $this->statusChoices;
    }

    /**
     * return format system choices
     *
     * @return array
     */
    public function getFormatSystemChoices()
    {
        return $this->formatSystemChoices;
    }
}
