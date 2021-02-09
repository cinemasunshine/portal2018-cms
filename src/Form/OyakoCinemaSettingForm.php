<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\InputFilter\InputFilter;

/**
 * OyakoCinemaSetting form class
 */
class OyakoCinemaSettingForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'oyako_cinema_url',
            'type' => 'Url',
        ]);

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'oyako_cinema_url',
            'required' => true,
        ]);

        $this->setInputFilter($inputFilter);
    }
}
