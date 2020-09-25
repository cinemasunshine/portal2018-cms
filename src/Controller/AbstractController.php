<?php

/**
 * AbstractController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller;

use App\Exception\RedirectException;
use App\Responder\AbstractResponder;
use Psr\Container\ContainerInterface;
use Slim\Collection;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Abstract controller
 *
 * @property-read \App\Auth $auth
 * @property-read \MicrosoftAzure\Storage\Blob\BlobRestProxy $bc
 * @property-read \Doctrine\ORM\EntityManager $em
 * @property-read \Slim\Flash\Messages $flash
 * @property-read \Monolog\Logger $logger
 * @property-read \Slim\Router $router
 * @property-read array $settings
 * @property-read \App\Session\SessionManager $sm
 * @property-read \Slim\Views\Twig $view
 */
abstract class AbstractController
{
    /** @var ContainerInterface container */
    protected $container;

    /**
     * data
     *
     * Responderへ値を渡すために作成。
     *
     * @var Collection
     */
    protected $data;

    /** @var string */
    protected $actionName;

    /**
     * construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->data      = new Collection();
    }

    /**
     * execute
     *
     * 前後でpreExecute(),postExecute()処理を自動実行するために実装。
     * __call()からの呼び出しを想定。
     *
     *
     * @param string   $actionMethod
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    protected function execute(
        string $actionMethod,
        Request $request,
        Response $response,
        array $args
    ) {
        try {
            $this->logger->debug('Run preExecute().');
            $this->preExecute($request, $response);

            $this->logger->debug('Run {method}().', [ 'method' => $actionMethod ]);

            /** @var string|null */
            $method = $this->$actionMethod($request, $response, $args);

            $this->logger->debug('Run postExecute().');
            $this->postExecute($request, $response);
        } catch (RedirectException $e) {
            $this->logger->debug('Redirect.', [
                'url'    => $e->getUrl(),
                'status' => $e->getStatus(),
            ]);

            return $response->withRedirect($e->getUrl(), $e->getStatus());
        }

        $this->logger->debug('Run buildResponse().');

        return $this->buildResponse($response, $method);
    }

    /**
     * pre execute
     *
     * argsはそれぞれの処理固有のパラメータなので渡さない。
     * responseなどをreturnしたいケースがあれば検討する。
     *
     * @param Request  $request
     * @param Response $response
     * @return void
     */
    abstract protected function preExecute($request, $response): void;

    /**
     * pre execute
     *
     * argsはそれぞれの処理固有のパラメータなので渡さない。
     * responseなどをreturnしたいケースがあれば検討する。
     *
     * @param Request  $request
     * @param Response $response
     * @return void
     */
    abstract protected function postExecute($request, $response): void;

    /**
     * redirect
     *
     * withRedirect()ではなくこちらを使う。
     * すぐにリダイレクトさせるためにExceptionを利用している。
     *
     * @param string|\Psr\Http\Message\UriInterface $url
     * @param int|null                              $status
     * @return void
     * @throws RedirectException
     */
    protected function redirect($url, $status = null): void
    {
        throw new RedirectException($url, $status);
    }

    /**
     * build response
     *
     * @param Response    $response
     * @param string|null $method responder method
     * @return Response
     */
    protected function buildResponse(Response $response, string $method = null): Response
    {
        $responder = $this->getResponder();

        if (empty($method)) {
            $method = $this->actionName;
        }

        return $responder->$method($response, $this->data);
    }

    abstract protected function getResponder(): AbstractResponder;

    /**
     * call
     *
     * @param string $name
     * @param array $argments
     * @return mixed
     * @throws \LogicException
     */
    public function __call($name, $argments)
    {
        $this->logger->debug('Call "{name}" action.', [ 'name' => $name ]);

        $actionMethod = 'execute' . ucfirst($name);

        // is_callable()は__call()があると常にtrueとなるので不可
        if (! method_exists($this, $actionMethod)) {
            throw new \LogicException(sprintf('The method "%s" dose not exist.', $name));
        }

        $this->actionName = $name;

        return $this->execute($actionMethod, $argments[0], $argments[1], $argments[2]);
    }

    /**
     * __get
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }
}
