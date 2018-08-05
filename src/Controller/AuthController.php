<?php
/**
 * AuthController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Cinemasunshine\PortalAdmin\Form\LoginForm;

/**
 * Auth controller class
 */
class AuthController extends BaseController
{
    /**
     * login action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return \Slim\Http\Response
     */
    public function executeLogin($request, $response, $args)
    {
    }
    
    /**
     * auth action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return \Slim\Http\Response
     */
    public function executeAuth($request, $response, $args)
    {
        $form = new LoginForm();
        $form->setData($request->getParams());
        
        if (!$form->isValid()) {
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'login';
        }
        
        $cleanData = $form->getData();
        
        $result = $this->auth->login($cleanData['name'], $cleanData['password']);
        
        if (!$result) {
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', ['global' => ['ユーザ名かパスワードが間違っています。']]);
            $this->data->set('is_validated', true);
            
            return 'login';
        }
        
        $this->redirect($this->router->pathFor('homepage'));
    }
    
    /**
     * logout action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return \Slim\Http\Response
     */
    public function executeLogout($request, $response, $args)
    {
        $this->auth->logout();
        
        $this->redirect($this->router->pathFor('login'));
    }
}