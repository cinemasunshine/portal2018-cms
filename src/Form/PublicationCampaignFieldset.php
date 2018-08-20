<?php
/**
 * PublicationCampaignFieldset.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use zend\Form\Fieldset;

/**
 * PublicationCampaign fieldset class
 */
class PublicationCampaignFieldset extends Fieldset
{
    public function __construct()
    {
        parent::__construct('campaign');
        
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
            'name' => 'campaign_id',
        ]);
        
        $this->add([
            'name' => 'display_order',
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
            'campaing_id' => [
                'required' => true,
            ],
            'display_order' => [
                'required' => true,
            ],
        ];
    }
}