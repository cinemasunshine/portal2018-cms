<?php

declare(strict_types=1);

namespace App\Form;

use Cinemasunshine\ORM\Entities\TheaterOpeningHour;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * OpeningHour fieldset class
 */
class OpeningHourFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var array<int, string> */
    protected array $typeChoices = [
        TheaterOpeningHour::TYPE_DATE => '日付',
        TheaterOpeningHour::TYPE_TERM => '期間',
    ];

    public function __construct()
    {
        parent::__construct('opening_hour');

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'type',
            'type' => 'Radio',
            'options' => [
                'value_options' => $this->typeChoices,
            ],
        ]);

        $this->add([
            'name' => 'from_date',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'to_date',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'time',
            'type' => 'Text',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'type' => ['required' => true],
            'from_date' => ['required' => true],
            'to_date' => ['required' => false], // @todo typeが期間の時はtrue
            'time' => ['required' => true],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getTypeChoices(): array
    {
        return $this->typeChoices;
    }
}
