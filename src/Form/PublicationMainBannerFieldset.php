<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * PublicationMainBanner fieldset class
 */
class PublicationMainBannerFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('main_banner');

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'main_banner_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'display_order',
            'type' => 'Hidden',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'main_banner_id' => ['required' => true],
            'display_order' => ['required' => true],
        ];
    }
}
