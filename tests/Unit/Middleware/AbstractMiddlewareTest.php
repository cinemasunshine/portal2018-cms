<?php

/**
 * AbstractMiddlewareTest.php
 */

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\AbstractMiddleware;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * Abstract Middleware test
 */
final class AbstractMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create target mock
     *
     * @return MockInterface|LegacyMockInterface|AbstractMiddleware
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AbstractMiddleware::class);
    }

    /**
     * Create target reflection
     */
    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(AbstractMiddleware::class);
    }

    /**
     * Create Container mock
     *
     * @return MockInterface|LegacyMockInterface|ContainerInterface
     */
    protected function createContainerMock()
    {
        return Mockery::mock(ContainerInterface::class);
    }

    /**
     * test construct
     *
     * @test
     */
    public function testConstruct(): void
    {
        $containerMock = $this->createContainerMock();
        $targetMock    = $this->createTargetMock();
        $targetRef     = $this->createTargetReflection();

        // execute constructor
        $targetConstructor = $targetRef->getConstructor();
        $targetConstructor->invoke($targetMock, $containerMock);

        // test property "container"
        $containerPropertyRef = $targetRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $this->assertEquals($containerMock, $containerPropertyRef->getValue($targetMock));
    }
}
