<?php

/**
 * OpeningHourFieldset.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Form;

use Cinemasunshine\ORM\Entities\TheaterOpeningHour;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * OpeningHour fieldset class
 */
class OpeningHourFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var array */
    protected $typeChoices = [
        TheaterOpeningHour::TYPE_DATE => '日付',
        TheaterOpeningHour::TYPE_TERM => '期間',
    ];

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct('opening_hour');

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
            'name' => 'type',
            'type' => 'Radio',
            'options' => [
                'value_options' => $this->typeChoices,
            ],
        ]);

        $this->add([
            'name' => 'from_date',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'to_date',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'time',
            'type' => 'Text',
        ]);
    }

    /**
     * return inpu filter specification
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'type' => [
                'required' => true,
            ],
            'from_date' => [
                'required' => true,
            ],
            'to_date' => [
                'required' => false, // @todo typeが期間の時はtrue
            ],
            'time' => [
                'required' => true,
            ],
        ];
    }

    /**
     * return type choices
     *
     * @return array
     */
    public function getTypeChoices()
    {
        return $this->typeChoices;
    }
}
