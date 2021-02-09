<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity;
use Doctrine\ORM\EntityManager;
use Laminas\InputFilter\InputFilter;

/**
 * Campaign find form class
 */
class CampaignFindForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;

    /** @var array<int, string> */
    protected $statusChoices = [
        '1' => 'キャンペーン中',
        '2' => 'キャンペーン終了',
    ];

    /** @var array<int, string> */
    protected $pageChoices = [];

    /** @var array<int, string> */
    protected $theaterChoices = [];

    /** @var array<int, string> */
    protected $specialSiteChoices = [];

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'status',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->statusChoices,
            ],
        ]);

        $pages = $this->em->getRepository(Entity\Page::class)->findActive();

        foreach ($pages as $page) {
            /** @var Entity\Page $page */
            $this->pageChoices[$page->getId()] = $page->getNameJa();
        }

        $this->add([
            'name' => 'page',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->pageChoices,
            ],
        ]);

        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();

        foreach ($theaters as $theater) {
            /** @var Entity\Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }

        $this->add([
            'name' => 'theater',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);

        $specialSites = $this->em->getRepository(Entity\SpecialSite::class)->findActive();

        foreach ($specialSites as $specialSite) {
            /** @var Entity\SpecialSite $specialSite */
            $this->specialSiteChoices[$specialSite->getId()] = $specialSite->getNameJa();
        }

        $this->add([
            'name' => 'special_site',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->specialSiteChoices,
            ],
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'status',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'page',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'theater',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'special_site',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }

    /**
     * @return array<int, string>
     */
    public function getStatusChoices(): array
    {
        return $this->statusChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getPageChoices(): array
    {
        return $this->pageChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getTheaterChoices(): array
    {
        return $this->theaterChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getSpecialSiteChoices(): array
    {
        return $this->specialSiteChoices;
    }
}
