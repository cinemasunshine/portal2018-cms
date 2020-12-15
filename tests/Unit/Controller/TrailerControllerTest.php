<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\TrailerController;
use App\Exception\ForbiddenException;
use App\ORM\Entity\AdminUser;
use Mockery;
use Slim\Container;

final class TrailerControllerTest extends BaseTestCase
{
    /**
     * @param Container $container
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&TrailerController
     */
    protected function createTargetMock(Container $container)
    {
        return Mockery::mock(TrailerController::class, [$container]);
    }

    /**
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(TrailerController::class);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testAuthorization()
    {
        $this->invokeAuthorization(false);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testAuthorizationForbidden()
    {
        $this->expectException(ForbiddenException::class);

        $this->invokeAuthorization(true);
    }

    protected function invokeAuthorization(bool $userIsTheater): void
    {
        $adminUserMock = $this->createAdminUserMock();
        $adminUserMock
            ->shouldReceive('isTheater')
            ->once()
            ->with()
            ->andReturn($userIsTheater);

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
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&AdminUser
     */
    protected function createAdminUserMock()
    {
        return Mockery::mock(AdminUser::class);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testRenderNew()
    {
        $responseMock = $this->createResponseMock();
        $data         = [];

        $container = $this->createContainer();

        $targetMock = $this->createTargetMock($container);
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'trailer/new.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderNewMethodRef = $targetRef->getMethod('renderNew');
        $renderNewMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderNewMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testRenderEdit()
    {
        $responseMock = $this->createResponseMock();
        $data         = [];

        $container = $this->createContainer();

        $targetMock = $this->createTargetMock($container);
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'trailer/edit.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderEditMethodRef = $targetRef->getMethod('renderEdit');
        $renderEditMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderEditMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }
}
