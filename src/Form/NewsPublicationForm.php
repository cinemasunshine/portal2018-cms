<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * NewsPublication form class
 */
class NewsPublicationForm extends BaseForm
{
    public const TARGET_PAGE          = 'page';
    public const TARGET_TEATER        = 'theater';
    public const TARGET_SPESICAL_SITE = 'special_site';

    /** @var string */
    protected $target;

    /** @var EntityManager */
    protected $em;

    public function __construct(string $target, EntityManager $em)
    {
        if (! in_array($target, [self::TARGET_PAGE, self::TARGET_TEATER, self::TARGET_SPESICAL_SITE])) {
            throw new InvalidArgumentException('invalid target.');
        }

        $this->target = $target;
        $this->em     = $em;

        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
    {
        if ($this->target === self::TARGET_PAGE) {
            $this->add([
                'name' => 'page_id',
                'type' => 'Hidden',
            ]);
        } elseif ($this->target === self::TARGET_TEATER) {
            $this->add([
                'name' => 'theater_id',
                'type' => 'Hidden',
            ]);
        } elseif ($this->target === self::TARGET_SPESICAL_SITE) {
            $this->add([
                'name' => 'special_site_id',
                'type' => 'Hidden',
            ]);
        }

        $this->add([
            'name' => 'news_list',
            'type' => 'Collection',
            'options' => [
                'target_element' => [
                    'type' => PublicationNewsFieldset::class,
                ],
            ],
        ]);

        $inputFilter = new InputFilter();

        if ($this->target === self::TARGET_PAGE) {
            $pageIds = [];
            $pages   = $this->em->getRepository(Entity\Page::class)->findActive();

            foreach ($pages as $page) {
                $pageIds[] = $page->getId();
            }

            $inputFilter->add([
                'name' => 'page_id',
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\InArray::class,
                        'options' => ['haystack' => $pageIds],
                    ],
                ],
            ]);
        } elseif ($this->target === self::TARGET_TEATER) {
            $theaterIds = [];
            $theaters   = $this->em->getRepository(Entity\Theater::class)->findActive();

            foreach ($theaters as $theater) {
                $theaterIds[] = $theater->getId();
            }

            $inputFilter->add([
                'name' => 'theater_id',
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\InArray::class,
                        'options' => ['haystack' => $theaterIds],
                    ],
                ],
            ]);
        } elseif ($this->target === self::TARGET_SPESICAL_SITE) {
            $specialSiteIds = [];
            $specialSites   = $this->em->getRepository(Entity\SpecialSite::class)->findActive();

            foreach ($specialSites as $specialSite) {
                $specialSiteIds[] = $specialSite->getId();
            }

            $inputFilter->add([
                'name' => 'special_site_id',
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\InArray::class,
                        'options' => ['haystack' => $specialSiteIds],
                    ],
                ],
            ]);
        }

        $inputFilter->add([
            'name' => 'news_list',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }
}
