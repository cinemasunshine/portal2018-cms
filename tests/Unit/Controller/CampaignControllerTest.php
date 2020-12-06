<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\CampaignController;
use Mockery;

final class CampaignControllerTest extends BaseTestCase
{
    /**
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|CampaignController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(CampaignController::class);
    }

    /**
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(CampaignController::class);
    }

    /**
     * @test
     * @return void
     */
    public function testExecuteNew()
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();
        $args         = [];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('renderNew')
            ->once()
            ->with($responseMock)
            ->andReturn($responseMock);

        $this->assertEquals(
            $responseMock,
            $targetMock->executeNew($requestMock, $responseMock, $args)
        );
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
            ->with($responseMock, 'campaign/new.html.twig', $data)
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
            ->with($responseMock, 'campaign/edit.html.twig', $data)
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
