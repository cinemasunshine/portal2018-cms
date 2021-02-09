<?php

declare(strict_types=1);

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

    /** @var array<int, string> */
    protected $groupChoices;

    /** @var array<int, string> */
    protected $theaterChoices;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        parent::__construct();

        $this->groupChoices   = Entity\AdminUser::getGroups();
        $this->theaterChoices = [];

        $this->setup();
    }

    protected function setup(): void
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
     * @return array<int, string>
     */
    public function getGroupChoices(): array
    {
        return $this->groupChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getTheaterChoices(): array
    {
        return $this->theaterChoices;
    }
}
