<?php

namespace App\Form;

use App\ORM\Entity;
use Doctrine\ORM\EntityManager;
use Laminas\InputFilter\InputFilter;

/**
 * AdminUser form class
 */
class AdminUserForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $groupChoices;

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

        parent::__construct();

        $this->groupChoices   = Entity\AdminUser::getGroups();
        $this->theaterChoices = [];

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
            'name' => 'display_name',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Password',
        ]);

        $this->add([
            'name' => 'group',
            'type' => 'Select',
            'options' => [
                'value_options' => $this->groupChoices,
            ],
        ]);

        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();

        foreach ($theaters as $theater) {
            /** @var Entity\Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }

        $this->add([
            'name' => 'theater',
            'type' => 'Select',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'display_name',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'password',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'group',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'theater',
            'required' => false, // @todo groupが劇場の場合はtrue
        ]);

        $this->setInputFilter($inputFilter);
    }

    /**
     * return group choices
     *
     * @return array
     */
    public function getGroupChoices()
    {
        return $this->groupChoices;
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
