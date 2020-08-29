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

        $notAllowedHandlerMock = Mockery::mock(NotAllowed::class);

        // execute constructor
        $notAllowedHandlerRef = new \ReflectionClass(NotAllowed::class);
        $notAllowedHandlerConstructor = $notAllowedHandlerRef->getConstructor();
        $notAllowedHandlerConstructor->invoke($notAllowedHandlerMock, $viewMock);

        // test property "view"
        $viewPropertyRef = $notAllowedHandlerRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $this->assertEquals($viewMock, $viewPropertyRef->getValue($notAllowedHandlerMock));
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

        $notAllowedHandlerMock = Mockery::mock(NotAllowed::class)->makePartial();

        $notAllowedHandlerRef = new \ReflectionClass(NotAllowed::class);
        $renderHtmlNotAllowedMessageMethodRef = $notAllowedHandlerRef->getMethod('renderHtmlNotAllowedMessage');
        $renderHtmlNotAllowedMessageMethodRef->setAccessible(true);

        $methods = ['GET'];

        // execute
        $result = $renderHtmlNotAllowedMessageMethodRef->invoke($notAllowedHandlerMock, $methods);

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

        $notAllowedHandlerMock = Mockery::mock(NotAllowed::class)->makePartial();

        $notAllowedHandlerRef = new \ReflectionClass(NotAllowed::class);
        $viewPropertyRef = $notAllowedHandlerRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($notAllowedHandlerMock, $viewMock);

        $renderHtmlNotAllowedMessageMethodRef = $notAllowedHandlerRef->getMethod('renderHtmlNotAllowedMessage');
        $renderHtmlNotAllowedMessageMethodRef->setAccessible(true);

        $methods = ['GET'];

        // execute
        $result = $renderHtmlNotAllowedMessageMethodRef->invoke($notAllowedHandlerMock, $methods);
        $this->assertEquals($html, $result);
    }
}
