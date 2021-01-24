<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\TitleController;
use App\Exception\ForbiddenException;
use App\ORM\Entity\AdminUser;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use Slim\Container;

final class TitleControllerTest extends BaseTestCase
{
    /**
     * @param Container $container
     * @return MockInterface&LegacyMockInterface&TitleController
     */
    protected function createTargetMock(Container $container)
    {
        return Mockery::mock(TitleController::class, [$container]);
    }

    /**
     * @return ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new ReflectionClass(TitleController::class);
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
     * @return MockInterface&LegacyMockInterface&AdminUser
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
            ->with($responseMock, 'title/new.html.twig', $data)
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
            ->with($responseMock, 'title/edit.html.twig', $data)
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
