<?php
/**
 * TitleFindForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * Title find form class
 */
class TitleFindForm extends BaseForm
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->add([
            'name' => 'id',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'id',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'name',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }
}
