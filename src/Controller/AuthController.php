<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginForm;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Auth controller class
 */
class AuthController extends BaseController
{
    /**
     * @param array<string, mixed> $data
     */
    protected function renderLogin(Response $response, array $data = []): Response
    {
        return $this->render($response, 'auth/login.html.twig', $data);
    }

    /**
     * login action
     *
     * @param array<string, mixed> $args
     */
    public function executeLogin(Request $request, Response $response, array $args): Response
    {
        return $this->renderLogin($response);
    }

    /**
     * auth action
     *
     * @param array<string, mixed> $args
     */
    public function executeAuth(Request $request, Response $response, array $args): Response
    {
        $form = new LoginForm();
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderLogin($response, [
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $result = $this->auth->login($cleanData['name'], $cleanData['password']);

        if (! $result) {
            return $this->renderLogin($response, [
                'values' => $request->getParams(),
                'errors' => ['global' => ['ユーザ名かパスワードが間違っています。']],
                'is_validated' => true,
            ]);
        }

        $user = $this->auth->getUser();

        $this->logger->info('User "{name}" login.', [
            'id'   => $user->getId(),
            'name' => $user->getName(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('ようこそ、 %s さん！', $user->getDisplayName()),
        ]);

        $this->redirect($this->router->pathFor('homepage'));
    }

    /**
     * logout action
     *
     * @param array<string, mixed> $args
     */
    public function executeLogout(Request $request, Response $response, array $args): void
    {
        $user = $this->auth->getUser();

        $this->auth->logout();

        $this->logger->info('User "{name}" logout.', [
            'id'   => $user->getId(),
            'name' => $user->getName(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => 'ログアウトしました。',
        ]);

        $this->redirect($this->router->pathFor('login'));
    }
}
