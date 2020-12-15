<?php

namespace App\Form;

use App\ORM\Entity\Theater;
use Doctrine\ORM\EntityManager;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * Schedule form class
 */
class ScheduleForm extends BaseForm
{
    public const TYPE_NEW  = 1;
    public const TYPE_EDIT = 2;

    /** @var int */
    protected $type;

    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $theaterChoices;

    /** @var ShowingFormatFieldset */
    protected $showingFormatFieldset;

    /**
     * construct
     *
     * @param int           $type
     * @param EntityManager $em
     */
    public function __construct(int $type, EntityManager $em)
    {
        $this->type = $type;
        $this->em   = $em;

        parent::__construct();

        $this->theaterChoices        = [];
        $this->showingFormatFieldset = new ShowingFormatFieldset();

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
            'name' => 'start_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'end_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'public_start_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'public_end_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'remark',
            'type' => 'Textarea',
        ]);

        $theaters = $this->em->getRepository(Theater::class)->findActive();

        foreach ($theaters as $theater) {
            /** @var Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }

        $this->add([
            'name' => 'theater',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);

        $this->add([
            'name' => 'formats',
            'type' => 'Collection',
            'options' => [
                'target_element' => $this->showingFormatFieldset,
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
            'name' => 'start_date',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'end_date',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'public_start_dt',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'public_end_dt',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'theater',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'remark',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
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

    /**
     * return format Fieldset
     *
     * @return ShowingFormatFieldset
     */
    public function getShowingFormatFieldset()
    {
        return $this->showingFormatFieldset;
    }
}
