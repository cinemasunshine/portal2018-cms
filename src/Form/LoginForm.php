<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\InputFilter\InputFilter;

/**
 * Login form class
 */
class LoginForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct();

        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Password',
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'password',
            'required' => true,
        ]);

        $this->setInputFilter($inputFilter);
    }
}
