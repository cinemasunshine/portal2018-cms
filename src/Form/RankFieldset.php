<?php

namespace App\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Rank fieldset class
 */
class RankFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct('rank');

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
     * return inpu filter specification
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'title_id' => ['required' => false],
            'title_name' => ['required' => false],
        ];
    }
}
