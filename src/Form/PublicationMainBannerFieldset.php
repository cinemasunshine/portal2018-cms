<?php

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

    /**
     * setup
     *
     * @return void
     */
    protected function setup()
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
     * return inpu filter specification
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'main_banner_id' => ['required' => true],
            'display_order' => ['required' => true],
        ];
    }
}
