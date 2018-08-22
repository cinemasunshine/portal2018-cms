<?php
/**
 * EditorUploadForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form\API;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

use Cinemasunshine\PortalAdmin\Form\BaseForm;

/**
 * EditorUpload form class
 */
class EditorUploadForm extends BaseForm
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
            'name' => 'file',
            'type' => 'File',
        ]);
        
        $inputFilter = new InputFilter();
        
        $inputFilter->add([
            'name' => 'file',
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
}