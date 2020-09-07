<?php

/**
 * AbstractMiddlewareTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\AbstractMiddleware;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Abstract Middleware test
 */
final class AbstractMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create target mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|AbstractMiddleware
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AbstractMiddleware::class);
    }

    /**
     * Create target reflection
     *
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(AbstractMiddleware::class);
    }

    /**
     * Create Container mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|ContainerInterface
     */
    protected function createContainerMock()
    {
        return Mockery::mock(ContainerInterface::class);
    }

    /**
     * test construct
     *
     * @test
     * @return void
     */
    public function testConstruct()
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
