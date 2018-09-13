<?php
/**
 * TheaterOpeningHourForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;


/**
 * Theater opening hour form class
 */
class TheaterOpeningHourForm extends BaseForm
{
    /** @var OpeningHourFieldset */
    protected $openingHourFieldset;
    
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->openingHourFieldset = new OpeningHourFieldset();
        
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
            'name' => 'hours',
            'type' => 'Collection',
            'options' => [
                'target_element' => $this->openingHourFieldset,
            ],
        ]);
    }
    
    /**
     * return opening hour fieldset
     *
     * @return OpeningHourFieldset
     */
    public function getOpeingHourFieldset()
    {
        return $this->openingHourFieldset;
    }
}