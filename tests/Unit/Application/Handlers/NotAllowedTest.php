<?php

/**
 * NotAllowedTest.php
 */

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Handlers\NotAllowed;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Slim\Views\Twig;

/**
 * NotAllowed handler test
 */
final class NotAllowedTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(NotAllowed::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&NotAllowed
     */
    protected function createTargetMock()
    {
        return Mockery::mock(NotAllowed::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Twig
     */
    protected function createViewMock()
    {
        return Mockery::mock(Twig::class);
    }

    /**
     * test construct
     *
     * @test
     */
    public function testConstruct(): void
    {
        $viewMock = $this->createViewMock();

        $targetMock = $this->createTargetMock();
        $targetRef  = $this->createTargetReflection();

        // execute constructor
        $notAllowedHandlerConstructor = $targetRef->getConstructor();
        $notAllowedHandlerConstructor->invoke($targetMock, $viewMock);

        // test property "view"
        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $this->assertEquals($viewMock, $viewPropertyRef->getValue($targetMock));
    }

    /**
     * test renderHtmlErrorMessage (debug on)
     *
     * 定数を使うので別プロセスで実行。
     *
     * @runInSeparateProcess
     * @test
     */
    public function testRenderHtmlNotAllowedMessageDebugOn(): void
    {
        define('APP_DEBUG', true);

        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetRef = $this->createTargetReflection();

        $renderHtmlNotAllowedMessageMethodRef = $targetRef->getMethod('renderHtmlNotAllowedMessage');
        $renderHtmlNotAllowedMessageMethodRef->setAccessible(true);

        $methods = ['GET'];

        // execute
        $result = $renderHtmlNotAllowedMessageMethodRef->invoke($targetMock, $methods);

        // @see Slim\Handlers\NotAllowed::renderHtmlNotAllowedMessage()
        $this->assertStringContainsString('<title>Method not allowed</title>', $result);
    }

    /**
     * test renderHtmlErrorMessage (debug off)
     *
     * 定数を使うので別プロセスで実行。
     *
     * @runInSeparateProcess
     * @test
     */
    public function testRenderHtmlNotAllowedMessageDebugOff(): void
    {
        define('APP_DEBUG', false);

        $html = '<html><head><title>Test</title></head><body></body></html>';

        $viewMock = $this->createViewMock();
        $viewMock
            ->shouldReceive('fetch')
            ->once()
            ->with(Mockery::type('string'), Mockery::type('array'))
            ->andReturn($html);

        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetRef = $this->createTargetReflection();

        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($targetMock, $viewMock);

        $renderHtmlNotAllowedMessageMethodRef = $targetRef->getMethod('renderHtmlNotAllowedMessage');
        $renderHtmlNotAllowedMessageMethodRef->setAccessible(true);

        $methods = ['GET'];

        // execute
        $result = $renderHtmlNotAllowedMessageMethodRef->invoke($targetMock, $methods);
        $this->assertEquals($html, $result);
    }
}
