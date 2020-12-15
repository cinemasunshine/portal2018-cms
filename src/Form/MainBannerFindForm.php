<?php

namespace App\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * MainBanner find form class
 */
class MainBannerFindForm extends BaseForm
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

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

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }
}
