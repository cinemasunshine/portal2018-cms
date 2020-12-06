<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\ScheduleController;
use App\Exception\ForbiddenException;
use App\ORM\Entity\AdminUser;
use Mockery;

final class ScheduleControllerTest extends BaseTestCase
{
    /**
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|ScheduleController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(ScheduleController::class);
    }

    /**
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(ScheduleController::class);
    }

    /**
     * @test
     * @return void
     */
    public function testPreExecute()
    {
        $this->invokePreExecute(false);
    }

    /**
     * @test
     * @return void
     */
    public function testPreExecuteForbidden()
    {
        $this->expectException(ForbiddenException::class);

        $this->invokePreExecute(true);
    }

    /**
     * @param boolean $userIsTheater
     * @return void
     */
    protected function invokePreExecute(bool $userIsTheater): void
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();

        $adminUserMock = $this->createAdminUserMock();
        $adminUserMock
            ->shouldReceive('isTheater')
            ->once()
            ->with()
            ->andReturn($userIsTheater);

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

        $preExecuteMethodRef = $targetRef->getMethod('preExecute');
        $preExecuteMethodRef->setAccessible(true);

        $preExecuteMethodRef->invoke($targetMock, $requestMock, $responseMock);
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
    public function testRenderNew()
    {
        $responseMock = $this->createResponseMock();
        $data         = [];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'schedule/new.html.twig', $data)
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
     * @return void
     */
    public function testRenderEdit()
    {
        $responseMock = $this->createResponseMock();
        $data         = [];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'schedule/edit.html.twig', $data)
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
