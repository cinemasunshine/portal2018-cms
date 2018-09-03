<?php
/**
 * ShowingController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Cinemasunshine\PortalAdmin\Form\API\TitleMasterFindForm;

/**
 * Showing controller
 */
class ShowingController extends BaseController
{
    /**
     * new action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeNew($request, $response, $args)
    {
        $this->data->set('master_find_form', new TitleMasterFindForm($this->em));
    }
}
