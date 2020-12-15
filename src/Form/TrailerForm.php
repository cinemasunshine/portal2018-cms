<?php

namespace App\Form;

use App\ORM\Entity;
use Doctrine\ORM\EntityManager;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * Trailer form class
 */
class TrailerForm extends BaseForm
{
    public const TYPE_NEW  = 1;
    public const TYPE_EDIT = 2;

    /** @var int */
    protected $type;

    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $pageChoices;

    /** @var array */
    protected $theaterChoices;

    /** @var array */
    protected $specialSiteChoices;

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

        $this->pageChoices        = [];
        $this->theaterChoices     = [];
        $this->specialSiteChoices = [];

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
            'name' => 'youtube',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'banner_image',
            'type' => 'File',
        ]);

        $this->add([
            'name' => 'banner_link_url',
            'type' => 'Url',
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

        if ($this->type === self::TYPE_EDIT) {
            $inputFilter->add([
                'name' => 'id',
                'required' => true,
            ]);
        }

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
            'name' => 'youtube',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'banner_image',
            'required' => ($this->type === self::TYPE_NEW),
            'validators' => [
                [
                    'name' => Validator\File\Size::class,
                    'options' => ['max' => '10MB'], // SASAKI-245
                ],
                [
                    'name' => Validator\File\MimeType::class,
                    'options' => [
                        'mimeType' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'banner_link_url',
            'required' => true,
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
     * return page choices
     *
     * @return array
     */
    public function getPageChoices()
    {
        return $this->pageChoices;
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
     * return special_site choices
     *
     * @return array
     */
    public function getSpecialSiteChoices()
    {
        return $this->specialSiteChoices;
    }
}
