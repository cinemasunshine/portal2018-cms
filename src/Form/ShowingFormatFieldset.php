<?php
/**
 * ShowingFormatFieldset.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

use Cinemasunshine\PortalAdmin\ORM\Entity\ShowingFormat;

/**
 * ShowingFormat fieldset class
 */
class ShowingFormatFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var array */
    protected $systemChoices;

    /** @var array */
    protected $soundChoices;

    /** @var array */
    protected $voiceChoices;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct('showing');

        $this->systemChoices = ShowingFormat::getSystemList();
        $this->soundChoices = ShowingFormat::getSoundList();
        $this->voiceChoices = ShowingFormat::getVoiceList();

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
            'name' => 'system',
            'type' => 'Select',
            'options' => [
                'empty_option' => '',
                'value_options' => $this->systemChoices,
            ],
        ]);

        $this->add([
            'name' => 'sound',
            'type' => 'Select',
            'options' => [
                'empty_option' => '',
                'value_options' => $this->soundChoices,
            ],
        ]);

        $this->add([
            'name' => 'voice',
            'type' => 'Select',
            'options' => [
                'empty_option' => '',
                'value_options' => $this->voiceChoices,
            ],
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
            'system' => [
                'required' => true,
            ],
            'sound' => [
                'required' => true,
            ],
            'voice' => [
                'required' => true,
            ],
        ];
    }

    /**
     * return system choices
     *
     * @return array
     */
    public function getSystemChoices()
    {
        return $this->systemChoices;
    }

    /**
     * return sound choices
     *
     * @return array
     */
    public function getSoundChoices()
    {
        return $this->soundChoices;
    }

    /**
     * return voice choices
     *
     * @return array
     */
    public function getVoiceChoices()
    {
        return $this->voiceChoices;
    }
}
