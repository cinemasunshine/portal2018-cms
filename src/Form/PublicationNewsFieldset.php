<?php

namespace App\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * PublicationNews fieldset class
 */
class PublicationNewsFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('news');

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
            'name' => 'news_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'display_order',
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
            'news_id' => ['required' => true],
            'display_order' => ['required' => true],
        ];
    }
}
