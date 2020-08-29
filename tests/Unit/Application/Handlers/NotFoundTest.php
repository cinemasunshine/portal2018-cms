<?php

/**
 * NotFoundTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use Cinemasunshine\PortalAdmin\Application\Handlers\NotFound;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Views\Twig;

/**
 * NotFound handler test
 */
final class NotFoundTest extends TestCase
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
     * Create Request mock
     *
     * ひとまず仮のクラスで実装する。
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|ServerRequestInterface
     */
    protected function createRequestMock()
    {
        return Mockery::mock('Request,' . ServerRequestInterface::class);
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

        $notFoundHandlerMock = Mockery::mock(NotFound::class);

        // execute constructor
        $notFoundHandlerRef = new \ReflectionClass(NotFound::class);
        $notFoundHandlerConstructor = $notFoundHandlerRef->getConstructor();
        $notFoundHandlerConstructor->invoke($notFoundHandlerMock, $viewMock);

        // test property "view"
        $viewPropertyRef = $notFoundHandlerRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $this->assertEquals($viewMock, $viewPropertyRef->getValue($notFoundHandlerMock));
    }

    /**
     * test renderHtmlNotFoundOutput (debug on)
     *
     * 定数を使うので別プロセスで実行。
     *
     * @test
     * @runInSeparateProcess
     * @return void
     */
    public function testRenderHtmlNotFoundOutputDebugOn()
    {
        define('APP_DEBUG', true);

        $uriMock = Mockery::mock(UriInterface::class);
        $uriMock
            ->shouldReceive('withPath')
            ->andReturn($uriMock);
        $uriMock
            ->shouldReceive('withQuery')
            ->andReturn($uriMock);
        $uriMock
            ->shouldReceive('withFragment')
            ->andReturn($uriMock);
        $uriMock
            ->shouldReceive('__toString')
            ->andReturn('/');

        $requestMock = $this->createRequestMock();
        $requestMock
            ->shouldReceive('getUri')
            ->andReturn($uriMock);

        $notFoundHandlerMock = Mockery::mock(NotFound::class);

        $notFoundHandlerRef = new \ReflectionClass(NotFound::class);
        $renderHtmlNotFoundOutputMethodRef = $notFoundHandlerRef->getMethod('renderHtmlNotFoundOutput');
        $renderHtmlNotFoundOutputMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlNotFoundOutputMethodRef->invoke($notFoundHandlerMock, $requestMock);

        // @see Slim\Handlers\NotFound::renderHtmlNotFoundOutput()
        $this->assertStringContainsString('<title>Page Not Found</title>', $result);
    }

    /**
     * test renderHtmlNotFoundOutput (debug off)
     *
     * 定数を使うので別プロセスで実行。
     *
     * @test
     * @runInSeparateProcess
     * @return void
     */
    public function testRenderHtmlNotFoundOutputDebugOff()
    {
        define('APP_DEBUG', false);

        $html = '<html><head><title>Test</title></head><body></body></html>';
        $viewMock = $this->createViewMock();
        $viewMock
            ->shouldReceive('fetch')
            ->once()
            ->with(Mockery::type('string'), Mockery::type('array'))
            ->andReturn($html);

        $requestMock = $this->createRequestMock();

        $notFoundHandlerMock = Mockery::mock(NotFound::class);

        $notFoundHandlerRef = new \ReflectionClass(NotFound::class);
        $viewPropertyRef = $notFoundHandlerRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($notFoundHandlerMock, $viewMock);

        $renderHtmlNotFoundOutputMethodRef = $notFoundHandlerRef->getMethod('renderHtmlNotFoundOutput');
        $renderHtmlNotFoundOutputMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlNotFoundOutputMethodRef->invoke($notFoundHandlerMock, $requestMock);
        $this->assertEquals($html, $result);
    }
}
