<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\AdminUserController;
use App\Exception\ForbiddenException;
use App\ORM\Entity\AdminUser;
use App\ORM\Repository\AdminUserRepository;
use App\Pagination\DoctrinePaginator;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use Slim\Container;

final class AdminUserControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&AdminUserController
     */
    protected function createTargetMock(Container $container)
    {
        return Mockery::mock(AdminUserController::class, [$container]);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(AdminUserController::class);
    }

    /**
     * @test
     */
    public function testAuthorization(): void
    {
        $this->invokeAuthorization(true);
    }

    /**
     * @test
     */
    public function testAuthorizationForbidden(): void
    {
        $this->expectException(ForbiddenException::class);

        $this->invokeAuthorization(false);
    }

    protected function invokeAuthorization(bool $userIsMaster): void
    {
        $adminUserMock = $this->createAdminUserMock();
        $adminUserMock
            ->shouldReceive('isMaster')
            ->once()
            ->with()
            ->andReturn($userIsMaster);

        $container = $this->createContainer();

        $container['auth']
            ->shouldReceive('getUser')
            ->once()
            ->with()
            ->andReturn($adminUserMock);

        $targetMock = $this->createTargetMock($container);
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetRef = $this->createTargetReflection();

        $authorizationMethodRef = $targetRef->getMethod('authorization');
        $authorizationMethodRef->setAccessible(true);

        $authorizationMethodRef->invoke($targetMock);
    }

    /**
     * @return MockInterface&LegacyMockInterface&AdminUser
     */
    protected function createAdminUserMock()
    {
        return Mockery::mock(AdminUser::class);
    }

    /**
     * @test
     */
    public function testExecuteList(): void
    {
        $page = 2;

        $requestMock = $this->createRequestMock();
        $requestMock
            ->shouldReceive('getParam')
            ->once()
            ->with('p', 1)
            ->andReturn($page);

        $responseMock = $this->createResponseMock();
        $args         = [];

        $paginatorMock = $this->createDoctrinePaginatorMock();

        $params         = [];
        $repositoryMock = $this->createAdminUserRepositoryMock();
        $repositoryMock
            ->shouldReceive('findForList')
            ->once()
            ->with($params, $page)
            ->andReturn($paginatorMock);

        $container = $this->createContainer();

        $container['em']
            ->shouldReceive('getRepository')
            ->once()
            ->with(AdminUser::class)
            ->andReturn($repositoryMock);

        $targetMock = $this->createTargetMock($container);
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $data = [
            'page' => $page,
            'params' => $params,
            'pagenater' => $paginatorMock,
        ];
        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'admin_user/list.html.twig', $data)
            ->andReturn($responseMock);

        $this->assertEquals(
            $responseMock,
            $targetMock->executeList($requestMock, $responseMock, $args)
        );
    }

    /**
     * @return MockInterface&LegacyMockInterface&AdminUserRepository
     */
    protected function createAdminUserRepositoryMock()
    {
        return Mockery::mock(AdminUserRepository::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&DoctrinePaginator
     */
    protected function createDoctrinePaginatorMock()
    {
         return Mockery::mock(DoctrinePaginator::class);
    }

    /**
     * @test
     */
    public function testRenderNew(): void
    {
        $responseMock = $this->createResponseMock();
        $data         = ['foo' => 'bar'];

        $container = $this->createContainer();

        $targetMock = $this->createTargetMock($container);
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'admin_user/new.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderNewMethodRef = $targetRef->getMethod('renderNew');
        $renderNewMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderNewMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }
}
