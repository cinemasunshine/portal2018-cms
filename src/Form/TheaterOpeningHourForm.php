<?php

declare(strict_types=1);

namespace App\Form;

/**
 * Theater opening hour form class
 */
class TheaterOpeningHourForm extends BaseForm
{
    /** @var OpeningHourFieldset */
    protected $openingHourFieldset;

    public function __construct()
    {
        parent::__construct();

        $this->openingHourFieldset = new OpeningHourFieldset();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'hours',
            'type' => 'Collection',
            'options' => [
                'target_element' => $this->openingHourFieldset,
            ],
        ]);
    }

    public function getOpeingHourFieldset(): OpeningHourFieldset
    {
        return $this->openingHourFieldset;
    }
}
