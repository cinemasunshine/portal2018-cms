<?php

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

    /**
     * setup
     *
     * @return void
     */
    protected function setup()
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
     * return inpu filter specification
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'campaign_id' => ['required' => true],
            'display_order' => ['required' => true],
        ];
    }
}
