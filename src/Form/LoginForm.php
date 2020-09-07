<?php

/**
 * LoginForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Form;

use App\ValidatorTranslator;
use Laminas\InputFilter\InputFilter;

/**
 * Login form class
 */
class LoginForm extends BaseForm
{
    /**
     * construct
     */
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
