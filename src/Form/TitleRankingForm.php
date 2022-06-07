<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class TitleRankingForm extends BaseForm
{
    protected TitleRankingRankFieldset $rankFieldset;

    public function __construct()
    {
        parent::__construct();

        $this->rankFieldset = new TitleRankingRankFieldset();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'from_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'to_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'ranks',
            'type' => 'Collection',
            'options' => [
                'target_element' => $this->rankFieldset,
            ],
        ]);

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'from_date',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'to_date',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d'],
                ],
            ],
        ]);

        $this->setInputFilter($inputFilter);
    }
}
