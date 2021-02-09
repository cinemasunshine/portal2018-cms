<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity\MainBanner;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * MainBanner form class
 */
class MainBannerForm extends BaseForm
{
    public const TYPE_NEW  = 1;
    public const TYPE_EDIT = 2;

    /** @var int */
    protected $type;

    /** @var array<int, string> */
    protected $linkTypeChoices;

    public function __construct(int $type)
    {
        $this->type            = $type;
        $this->linkTypeChoices = MainBanner::getLinkTypes();

        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
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
            'name' => 'link_type',
            'type' => 'Radio',
            'options' => [
                'value_options' => $this->linkTypeChoices,
            ],
        ]);

        $this->add([
            'name' => 'link_url',
            'type' => 'Url',
        ]);

        $this->add([
            'name' => 'image',
            'type' => 'File',
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
            'name' => 'link_type',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'link_url',
            'required' => false, // リンクタイプがURLの場合はtrue
        ]);

        $inputFilter->add([
            'name' => 'image',
            'required' => ($this->type === self::TYPE_NEW),
            'validators' => [
                [
                    'name' => Validator\File\Size::class,
                    'options' => ['max' => '10MB'], // SASAKI-245
                ],
                [
                    'name' => Validator\File\MimeType::class,
                    'options' => [
                        'mimeType' => self::$imageMimeTypes,
                    ],
                ],
            ],
        ]);

        $this->setInputFilter($inputFilter);
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        $this->preValidator($this->data);

        return parent::isValid();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function preValidator(array $data): void
    {
        if (
            isset($data['link_type'])
            && (int) $data['link_type'] ===  MainBanner::LINK_TYPE_URL
        ) {
            $this->getInputFilter()->get('link_url')->setRequired(true);
        }
    }

    /**
     * @return array<int, string>
     */
    public function getLinkTypeChoices(): array
    {
        return $this->linkTypeChoices;
    }
}
