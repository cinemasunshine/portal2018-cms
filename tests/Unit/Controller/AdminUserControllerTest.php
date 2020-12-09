<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\AdminUserController;
use App\Exception\ForbiddenException;
use App\ORM\Entity\AdminUser;
use App\ORM\Repository\AdminUserRepository;
use App\Pagination\DoctrinePaginator;
use Mockery;

final class AdminUserControllerTest extends BaseTestCase
{
    /**
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|AdminUserController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AdminUserController::class);
    }

    /**
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(AdminUserController::class);
    }

    /**
     * @test
     * @return void
     */
    public function testAuthorization()
    {
        $this->invokeAuthorization(true);
    }

    /**
     * @test
     * @return void
     */
    public function testAuthorizationForbidden()
    {
        $this->expectException(ForbiddenException::class);

        $this->invokeAuthorization(false);
    }

    /**
     * @param boolean $userIsMaster
     * @return void
     */
    protected function invokeAuthorization(bool $userIsMaster): void
    {
        $adminUserMock = $this->createAdminUserMock();
        $adminUserMock
            ->shouldReceive('isMaster')
            ->once()
            ->with()
            ->andReturn($userIsMaster);

        $authMock = $this->createAuthMock();
        $authMock
            ->shouldReceive('getUser')
            ->once()
            ->with()
            ->andReturn($adminUserMock);

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $targetMock->auth = $authMock;

        $targetRef = $this->createTargetReflection();

        $authorizationMethodRef = $targetRef->getMethod('authorization');
        $authorizationMethodRef->setAccessible(true);

        $authorizationMethodRef->invoke($targetMock);
    }

    /**
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|AdminUser
     */
    protected function createAdminUserMock()
    {
        return Mockery::mock(AdminUser::class);
    }

    /**
     * @test
     * @return void
     */
    public function testExecuteList()
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

        $entityManagerMock = $this->createEntityManagerMock();
        $entityManagerMock
            ->shouldReceive('getRepository')
            ->once()
            ->with(AdminUser::class)
            ->andReturn($repositoryMock);

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $targetMock->em = $entityManagerMock;

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
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|AdminUserRepository
     */
    protected function createAdminUserRepositoryMock()
    {
        return Mockery::mock(AdminUserRepository::class);
    }

    /**
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|DoctrinePaginator
     */
    protected function createDoctrinePaginatorMock()
    {
         return Mockery::mock(DoctrinePaginator::class);
    }

    /**
     * @test
     * @return void
     */
    public function testRenderNew()
    {
        $responseMock = $this->createResponseMock();
        $data         = ['foo' => 'bar'];

        $targetMock = $this->createTargetMock();
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
