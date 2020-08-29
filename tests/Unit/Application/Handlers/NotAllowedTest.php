<?php

/**
 * NotAllowedTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use Cinemasunshine\PortalAdmin\Application\Handlers\NotAllowed;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Slim\Views\Twig;

/**
 * NotAllowed handler test
 */
final class NotAllowedTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(NotAllowed::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&NotAllowed
     */
    protected function createTargetMock()
    {
        return Mockery::mock(NotAllowed::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&Twig
     */
    protected function createViewMock()
    {
        return Mockery::mock(Twig::class);
    }

    /**
     * test construct
     *
     * @test
     * @return void
     */
    public function testConstruct()
    {
        $viewMock = $this->createViewMock();

        $targetMock = $this->createTargetMock();

        // execute constructor
        $targetRef = $this->createTargetReflection();
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
     * @test
     * @runInSeparateProcess
     * @return void
     */
    public function testRenderHtmlNotAllowedMessageDebugOn()
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
     * @test
     * @runInSeparateProcess
     * @return void
     */
    public function testRenderHtmlNotAllowedMessageDebugOff()
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
