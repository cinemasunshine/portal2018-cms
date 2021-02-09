<?php

/**
 * NotFoundTest.php
 */

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Handlers\NotFound;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use Slim\Views\Twig;

/**
 * NotFound handler test
 */
final class NotFoundTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(NotFound::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&NotFound
     */
    protected function createTargetMock()
    {
        return Mockery::mock(NotFound::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Twig
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
     * @return MockInterface&LegacyMockInterface&ServerRequestInterface
     */
    protected function createRequestMock()
    {
        return Mockery::mock('Request,' . ServerRequestInterface::class);
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
        $notFoundHandlerConstructor = $targetRef->getConstructor();
        $notFoundHandlerConstructor->invoke($targetMock, $viewMock);

        // test property "view"
        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $this->assertEquals($viewMock, $viewPropertyRef->getValue($targetMock));
    }

    /**
     * test renderHtmlNotFoundOutput (debug on)
     *
     * 定数を使うので別プロセスで実行。
     *
     * @runInSeparateProcess
     * @test
     */
    public function testRenderHtmlNotFoundOutputDebugOn(): void
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

        $targetMock = $this->createTargetMock();
        $targetRef  = $this->createTargetReflection();

        $renderHtmlNotFoundOutputMethodRef = $targetRef->getMethod('renderHtmlNotFoundOutput');
        $renderHtmlNotFoundOutputMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlNotFoundOutputMethodRef->invoke($targetMock, $requestMock);

        // @see Slim\Handlers\NotFound::renderHtmlNotFoundOutput()
        $this->assertStringContainsString('<title>Page Not Found</title>', $result);
    }

    /**
     * test renderHtmlNotFoundOutput (debug off)
     *
     * 定数を使うので別プロセスで実行。
     *
     * @runInSeparateProcess
     * @test
     */
    public function testRenderHtmlNotFoundOutputDebugOff(): void
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

        $targetMock = $this->createTargetMock();

        $targetRef = $this->createTargetReflection();

        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($targetMock, $viewMock);

        $renderHtmlNotFoundOutputMethodRef = $targetRef->getMethod('renderHtmlNotFoundOutput');
        $renderHtmlNotFoundOutputMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlNotFoundOutputMethodRef->invoke($targetMock, $requestMock);
        $this->assertEquals($html, $result);
    }
}
