<?php

/**
 * OyakoCinemaForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Doctrine\ORM\EntityManager;
use Laminas\InputFilter\InputFilter;

/**
 * OyakoCinema form class
 */
class OyakoCinemaForm extends BaseForm
{
    public const TYPE_NEW = 1;
    public const TYPE_EDIT = 2;

    /** @var int */
    protected $type;

    /** @var OyakoCinemaScheduleFieldset */
    protected $scheduleFieldset;

    /**
     * construct
     *
     * @param int           $type
     * @param EntityManager $em
     */
    public function __construct(int $type, EntityManager $em)
    {
        $this->type = $type;
        $this->scheduleFieldset = new OyakoCinemaScheduleFieldset($em);

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
            'name' => 'title_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'title_url',
            'type' => 'Url',
        ]);

        $this->add([
            'name' => 'schedules',
            'type' => 'Collection',
            'options' => [
                'target_element' => $this->scheduleFieldset,
            ],
        ]);


        $inputFilter = new InputFilter();

        if ($this->type === self::TYPE_EDIT) {
            $inputFilter->add([
                'name' => 'id',
                'required' => true,
            ]);
        }

        $inputFilter->add([
            'name' => 'title_id',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'title_url',
            'required' => true,
        ]);

        // fieldsetのInputFilterが消えてしまう？ようなので設定しない
        // $inputFilter->add([
        //     'name' => 'schedules',
        //     'required' => true,
        // ]);

        $this->setInputFilter($inputFilter);
    }

    /**
     * return schedule fiedlset
     *
     * @return OyakoCinemaScheduleFieldset
     */
    public function getScheduleFieldset()
    {
        return $this->scheduleFieldset;
    }
}
