<?php

/**
 * OyakoCinemaScheduleFieldset.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Form;

use App\ORM\Entity\Theater;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

/**
 * OyakoCinemaSchedule fieldset class
 */
class OyakoCinemaScheduleFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $theaterChoices;

    /**
     * construct
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->theaterChoices = [];

        parent::__construct('schedules');

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
            'name' => 'date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $theaters = $this->em->getRepository(Theater::class)->findActive();

        foreach ($theaters as $theater) {
            /** @var Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }

        $this->add([
            'name' => 'theaters',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);
    }

    /**
     * return inpu filter specification
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $specification = [
            'date' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\Date::class,
                        'options' => [
                            'format' => 'Y/m/d',
                        ],
                    ],
                ],
            ],
            'theaters' => [
                'required' => true,
            ],
        ];

        return $specification;
    }

    /**
     * return theater choices
     *
     * @return array
     */
    public function getTheaterChoices()
    {
        return $this->theaterChoices;
    }
}
