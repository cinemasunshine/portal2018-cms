<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\TheaterMetaController;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;

final class TheaterMetaControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&TheaterMetaController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(TheaterMetaController::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(TheaterMetaController::class);
    }

    /**
     * @test
     */
    public function testRenderOpeningHourEdit(): void
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
            ->with($responseMock, 'theater_meta/opening_hour/edit.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderOpeningHourEditMethodRef = $targetRef->getMethod('renderOpeningHourEdit');
        $renderOpeningHourEditMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderOpeningHourEditMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }
}
