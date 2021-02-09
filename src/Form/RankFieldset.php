<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Rank fieldset class
 */
class RankFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('rank');

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'title_id',
            'type' => 'Hidden',
        ]);

        // 作品名を表示するため
        $this->add([
            'name' => 'title_name',
            'type' => 'Hidden',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'title_id' => ['required' => false],
            'title_name' => ['required' => false],
        ];
    }
}
