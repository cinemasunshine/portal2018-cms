<?php
/**
 * MainBannerForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

use Cinemasunshine\PortalAdmin\ORM\Entity\MainBanner;

/**
 * MainBanner form class
 */
class MainBannerForm extends BaseForm
{
    /** @var array */
    protected $linkTypeChoices;
    
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->linkTypeChoices = MainBanner::getLinkTypes();
        
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
            'name' => 'name',
            'type' => 'Text',
        ]);
        
        $this->add([
            'name' => 'link_type',
            'type' => 'Radio',
            'options' => [
                'value_options' => $this->linkTypeChoices,
            ],
        ]);
        
        $this->add([
            'name' => 'link_url',
            'type' => 'Url',
        ]);
        
        $this->add([
            'name' => 'image',
            'type' => 'File',
        ]);
        
        $inputFilter = new InputFilter();
        
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'link_type',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'link_url',
            'required' => false, // リンクタイプがURLの場合はtrue
        ]);
        
        $inputFilter->add([
            'name' => 'image',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\File\Size::class,
                    'options' => [
                        'max' => '200KB', // @todo 調整
                    ],
                ],
                [
                    'name' => Validator\File\MimeType::class,
                    'options' => [
                        'mimeType' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                    ],
                ],
            ],
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
        if (
            isset($data['link_type'])
            && (int) $data['link_type'] ===  MainBanner::LINK_TYPE_URL
        ) {
            $this->getInputFilter()->get('link_url')->setRequired(true);
        }
    }
    
    /**
     * return link_type choices
     *
     * @return array
     */
    public function getLinkTypeChoices()
    {
        return $this->linkTypeChoices;
    }
}
