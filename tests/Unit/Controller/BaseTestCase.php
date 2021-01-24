<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Auth;
use Doctrine\ORM\EntityManager;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
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
        $container['em']    = $this->createEntityManagerMock();
        $container['flash'] = $this->createFlashMock();
        $container['view']  = $this->createViewMock();

        return $container;
    }

    /**
     * @return MockInterface&LegacyMockInterface&Auth
     */
    protected function createAuthMock()
    {
        return Mockery::mock(Auth::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&EntityManager
     */
    protected function createEntityManagerMock()
    {
        return Mockery::mock(EntityManager::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&FlashMessages
     */
    protected function createFlashMock()
    {
        return Mockery::mock(FlashMessages::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Request
     */
    protected function createRequestMock()
    {
        return Mockery::mock(Request::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Response
     */
    protected function createResponseMock()
    {
        return Mockery::mock(Response::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Router
     */
    protected function createRouterMock()
    {
        return Mockery::mock(Router::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&View
     */
    protected function createViewMock()
    {
        return Mockery::mock(View::class);
    }
}
