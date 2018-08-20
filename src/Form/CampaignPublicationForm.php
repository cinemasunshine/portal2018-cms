<?php
/**
 * CampaignPublicationForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * CampaignPublication form class
 */
class CampaignPublicationForm extends BaseForm
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        
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
            'name' => 'theater_id',
            'type' => 'Hidden',
        ]);
        
        $this->add([
            'name' => 'page_id',
            'type' => 'Hidden',
        ]);
        
        $this->add([
            'name' => 'special_site_id',
            'type' => 'Hidden',
        ]);
        
        $this->add([
            'name' => 'campaigns',
            'type' => 'Collection',
            'options' => [
                'target_element' => [
                    'type' => PublicationCampaignFieldset::class,
                ],
            ],
        ]);
        
        
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'theater_id',
            'required' => false,
        ]);
        
        $inputFilter->add([
            'name' => 'page_id',
            'required' => false,
        ]);
        
        $inputFilter->add([
            'name' => 'special_site_id',
            'required' => false,
        ]);
        
        $this->setInputFilter($inputFilter);
    }
}