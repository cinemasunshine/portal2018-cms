<?php
/**
 * LoginForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\Form\Form;
use Zend\I18n\Translator\Resources;
use Zend\Validator\Translator;
use Zend\Validator\AbstractValidator;

use Cinemasunshine\PortalAdmin\Translator\ValidatorTranslator;

/**
 * Base form class
 */
class BaseForm extends Form
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        
        $translator = new ValidatorTranslator();
        $translationFile = Resources::getBasePath() . sprintf(Resources::getPatternForValidator(), 'ja');
        $translator->addTranslationFile(
            'phpArray',
            $translationFile
        );

        AbstractValidator::setDefaultTranslator($translator);
    }
}