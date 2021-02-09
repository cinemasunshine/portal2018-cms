<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\BaseController;
use App\ORM\Entity\AdminUser;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use Slim\Container;
use Twig\Environment;

final class BaseControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&BaseController
     */
    protected function createTargetMock(Container $container)
    {
        return Mockery::mock(BaseController::class, [$container]);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(BaseController::class);
    }

    /**
     * @test
     */
    public function testPreExecute(): void
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();

        $container = $this->createContainer();

        $adminUser = $this->createAdminUserEntityMock();
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
     * @return MockInterface&LegacyMockInterface&Environment
     */
    protected function createViewEnvironmentMock()
    {
        return Mockery::mock(Environment::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&AdminUser
     */
    protected function createAdminUserEntityMock()
    {
        return Mockery::mock(AdminUser::class);
    }
}
