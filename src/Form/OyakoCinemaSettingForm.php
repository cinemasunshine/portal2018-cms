<?php

/**
 * OyakoCinemaSettingForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Form;

use Laminas\InputFilter\InputFilter;

/**
 * OyakoCinemaSetting form class
 */
class OyakoCinemaSettingForm extends BaseForm
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
