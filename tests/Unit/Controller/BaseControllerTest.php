<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\BaseController;
use Mockery;
use Twig\Environment;
use Slim\Container;

final class BaseControllerTest extends BaseTestCase
{
    /**
     * @param Container $container
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&BaseController
     */
    protected function createTargetMock(Container $container)
    {
        return Mockery::mock(BaseController::class, [$container]);
    }

    /**
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(BaseController::class);
    }

    /**
     * @test
     * @return void
     */
    public function testPreExecute()
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();

        $container = $this->createContainer();

        $adminUser = 'admin user';
        $container['auth']
            ->shouldReceive('getUser')
            ->once()
            ->with()
            ->andReturn($adminUser);

        $alertMessages = 'alert messages';
        $container['flash']
            ->shouldReceive('getMessage')
            ->once()
            ->with('alerts')
            ->andReturn($alertMessages);

        $viewEnvMock = $this->createViewEnvironmentMock();
        $viewEnvMock
            ->shouldReceive('addGlobal')
            ->once()
            ->with('user', $adminUser);
        $viewEnvMock
            ->shouldReceive('addGlobal')
            ->once()
            ->with('alerts', $alertMessages);

        $container['view']
            ->shouldReceive('getEnvironment')
            ->once()
            ->with()
            ->andReturn($viewEnvMock);

        $targetMock = $this->createTargetMock($container);
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetRef = $this->createTargetReflection();

        $preExecuteMethodRef = $targetRef->getMethod('preExecute');
        $preExecuteMethodRef->setAccessible(true);

        $preExecuteMethodRef->invoke($targetMock, $requestMock, $responseMock);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&Environment
     */
    protected function createViewEnvironmentMock()
    {
        return Mockery::mock(Environment::class);
    }
}
