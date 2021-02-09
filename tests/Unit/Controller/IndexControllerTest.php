<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\IndexController;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

final class IndexControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&IndexController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(IndexController::class);
    }

    /**
     * @test
     */
    public function testExecuteIndex(): void
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();
        $args         = [];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'index/index.html.twig')
            ->andReturn($responseMock);

        $this->assertEquals(
            $responseMock,
            $targetMock->executeIndex($requestMock, $responseMock, $args)
        );
    }
}
