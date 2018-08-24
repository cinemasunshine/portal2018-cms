<?php
/**
 * AdvanceSaleForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

use Doctrine\ORM\EntityManager;

use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * AdvanceSale form class
 */
class AdvanceSaleForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;
    
    /**@var array */
    protected $theaterChoices = [];
    
    /**
     * construct
     * 
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        
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
        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();
        
        foreach ($theaters as $theater) {
            /** @var Entity\Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }
        
        $this->add([
            'name' => 'theater',
            'type' => 'Select',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);
        
        $this->add([
            'name' => 'title_id',
            'type' => 'Hidden',
        ]);
        
        // 作品名を表示するため
        $this->add([
            'name' => 'title_name',
            'type' => 'Hidden',
        ]);
        
        $this->add([
            'name' => 'publishing_expected_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'not_exist_publishing_expected_date',
            'type' => 'Checkbox',
            'options' => [
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
        ]);
        
        $this->add([
            'name' => 'publishing_expected_date_text',
            'type' => 'Text',
        ]);
        
        $this->add([
            'name' => 'tickets',
            'type' => 'Collection',
            'options' => [
                'target_element' => [
                    'type' => AdvanceTicketFieldset::class,
                ],
            ],
        ]);
        
        
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'theater',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'title_id',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'title_name',
            'required' => false, // 表示用なのでfalse
        ]);
        
        $inputFilter->add([
            'name' => 'publishing_expected_date',
            'required' => true, // 未定の場合はfalse
        ]);
        
        $inputFilter->add([
            'name' => 'not_exist_publishing_expected_date',
            'required' => false,
        ]);
        
        $inputFilter->add([
            'name' => 'publishing_expected_date_text',
            'required' => false,
        ]);
        
        $this->setInputFilter($inputFilter);
    }
    
    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        $this->preValidator($this->data);
        
        return parent::isValid();
    }
    
    /**
     * pre validator
     *
     * @param array $data
     * @return void
     */
    protected function preValidator(array $data)
    {
        if ($data['not_exist_publishing_expected_date'] === '1') {
            $this->getInputFilter()->get('publishing_expected_date')->setRequired(false);
        }
    }
    
    /**
     * return theater choices
     *
     * @return array
     */
    public function getTheaterChoices()
    {
        return $this->theaterChoices;
    }
    
    /**
     * return ticket type choices
     *
     * @return array
     */
    public function getTicketTypeChoices()
    {
        return AdvanceTicketFieldset::getTypeChoices();
    }
    
    /**
     * return ticket special_gift_stock choices
     *
     * @return array
     */
    public function getTicketSpecialGiftStockChoices()
    {
        return AdvanceTicketFieldset::getSpecialGiftStockChoices();
    }
}
