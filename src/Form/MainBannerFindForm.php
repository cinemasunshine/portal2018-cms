<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\InputFilter\InputFilter;

/**
 * MainBanner find form class
 */
class MainBannerFindForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
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
