<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity\Theater;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

/**
 * OyakoCinemaSchedule fieldset class
 */
class OyakoCinemaScheduleFieldset extends Fieldset implements InputFilterProviderInterface
{
    protected EntityManager $em;

    /** @var array<int, string> */
    protected array $theaterChoices;

    public function __construct(EntityManager $em)
    {
        $this->em             = $em;
        $this->theaterChoices = [];

        parent::__construct('schedules');

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $theaters = $this->em->getRepository(Theater::class)->findActive();

        foreach ($theaters as $theater) {
            /** @var Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }

        $this->add([
            'name' => 'theaters',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'date' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\Date::class,
                        'options' => ['format' => 'Y/m/d'],
                    ],
                ],
            ],
            'theaters' => ['required' => true],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getTheaterChoices(): array
    {
        return $this->theaterChoices;
    }
}
