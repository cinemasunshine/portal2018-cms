<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * PublicationCampaign fieldset class
 */
class PublicationCampaignFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('campaign');

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'campaign_id',
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
            'campaign_id' => ['required' => true],
            'display_order' => ['required' => true],
        ];
    }
}
