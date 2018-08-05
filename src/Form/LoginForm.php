<?php
/**
 * LoginForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;

use Cinemasunshine\PortalAdmin\ValidatorTranslator;

/**
 * Login form class
 */
class LoginForm extends BaseForm
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);
        
        $this->add([
            'name' => 'password',
            'type' => 'Password',
        ]);
        
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'password',
            'required' => true,
        ]);
        
        $this->setInputFilter($inputFilter);
    }
}
