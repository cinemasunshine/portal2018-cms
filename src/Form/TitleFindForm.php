<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\InputFilter\InputFilter;

/**
 * Title find form class
 */
class TitleFindForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct();

        $this->add([
            'name' => 'id',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'id',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'name',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }
}
