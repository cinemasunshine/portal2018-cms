<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Auth;
use Doctrine\ORM\EntityManager;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Slim\Container;
use Slim\Flash\Messages as FlashMessages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig as View;

abstract class BaseTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return Container
     */
    protected function createContainer()
    {
        $container = new Container();

        $container['auth']  = $this->createAuthMock();
        $container['flash'] = $this->createFlashMock();
        $container['view']  = $this->createViewMock();

        return $container;
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&Auth
     */
    protected function createAuthMock()
    {
        return Mockery::mock(Auth::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&EntityManager
     */
    protected function createEntityManagerMock()
    {
        return Mockery::mock(EntityManager::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&FlashMessages
     */
    protected function createFlashMock()
    {
        return Mockery::mock(FlashMessages::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&Request
     */
    protected function createRequestMock()
    {
        return Mockery::mock(Request::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&Response
     */
    protected function createResponseMock()
    {
        return Mockery::mock(Response::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&Router
     */
    protected function createRouterMock()
    {
        return Mockery::mock(Router::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&View
     */
    protected function createViewMock()
    {
        return Mockery::mock(View::class);
    }
}
