<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\OyakoCinemaController;
use App\Exception\ForbiddenException;
use App\ORM\Entity\AdminUser;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use Slim\Container;

final class OyakoCinemaControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&OyakoCinemaController
     */
    protected function createTargetMock(Container $container)
    {
        return Mockery::mock(OyakoCinemaController::class, [$container]);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(OyakoCinemaController::class);
    }

    /**
     * @test
     */
    public function testAuthorization(): void
    {
        $this->invokeAuthorization(false);
    }

    /**
     * @test
     */
    public function testAuthorizationForbidden(): void
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
     */
    public function testRenderNew(): void
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
            ->with($responseMock, 'oyako_cinema/new.html.twig', $data)
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
     */
    public function testRenderEdit(): void
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
            ->with($responseMock, 'oyako_cinema/edit.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderEditMethodRef = $targetRef->getMethod('renderEdit');
        $renderEditMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderEditMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }

    /**
     * @test
     */
    public function testRenderSettingEdit(): void
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
            ->with($responseMock, 'oyako_cinema/setting/edit.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderSettingEditMethodRef = $targetRef->getMethod('renderSettingEdit');
        $renderSettingEditMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderSettingEditMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }
}
