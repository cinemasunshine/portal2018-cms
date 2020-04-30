<?php

/**
 * ErrorTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use Cinemasunshine\PortalAdmin\Application\Handlers\Error;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Slim\Container;

/**
 * Error handler test
 */
final class ErrorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create Container mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|Container
     */
    protected function createContainerMock()
    {
        return Mockery::mock(Container::class);
    }

    /**
     * Create Logger mock
     *
     * ひとまず仮のクラスで実装する。
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    protected function createLoggerMock()
    {
        return Mockery::mock('Logger');
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

        $loggerMock = $this->createLoggerMock();
        $containerMock
            ->shouldReceive('get')
            ->once()
            ->with('logger')
            ->andReturn($loggerMock);

        $settings = [
            'displayErrorDetails' => true,
        ];
        $containerMock
            ->shouldReceive('get')
            ->once()
            ->with('settings')
            ->andReturn($settings);

        $errorHandlerMock = Mockery::mock(Error::class);

        // execute constructor
        $errorHandlerRef = new \ReflectionClass(Error::class);
        $errorHandlerConstructor = $errorHandlerRef->getConstructor();
        $errorHandlerConstructor->invoke($errorHandlerMock, $containerMock);

        // test property "container"
        $containerPropertyRef = $errorHandlerRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $this->assertEquals($containerMock, $containerPropertyRef->getValue($errorHandlerMock));

        // test property "logger"
        $loggerPropertyRef = $errorHandlerRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $this->assertEquals($loggerMock, $loggerPropertyRef->getValue($errorHandlerMock));
    }

    /**
     * test writeToErrorLog
     *
     * @return void
     */
    public function testWriteToErrorLog()
    {
        $exception = new \Exception();
        $errorHandlerMock = Mockery::mock(Error::class)->makePartial();
        $errorHandlerMock->shouldAllowMockingProtectedMethods();
        $errorHandlerMock
            ->shouldReceive('log')
            ->once()
            ->with($exception);

        $writeToErrorLogRef = new \ReflectionMethod($errorHandlerMock, 'writeToErrorLog');
        $writeToErrorLogRef->setAccessible(true);

        // execute
        $writeToErrorLogRef->invoke($errorHandlerMock, $exception);
    }

    /**
     * test log
     *
     * @test
     * @return void
     */
    public function testLog()
    {
        $message = 'message';

        // Exceptionのmockは出来ない？
        $exception = new \Exception($message);

        $loggerMock = $this->createLoggerMock();
        $loggerMock
            ->shouldReceive('error')
            ->once()
            ->with($message, Mockery::type('array'));

        $errorHandlerMock = Mockery::mock(Error::class)->makePartial();

        $errorHandlerRef = new \ReflectionClass(Error::class);

        // test property "logger"
        $loggerPropertyRef = $errorHandlerRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $loggerPropertyRef->setValue($errorHandlerMock, $loggerMock);

        $logMethodRef = $errorHandlerRef->getMethod('log');
        $logMethodRef->setAccessible(true);

        // execute
        $logMethodRef->invoke($errorHandlerMock, $exception);
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
    public function testRenderHtmlErrorMessageDebugOn()
    {
        define('APP_DEBUG', true);
        define('APP_ROOT', __DIR__);

        $exception = new \Exception('message');

        $errorHandlerMock = Mockery::mock(Error::class)->makePartial();

        $errorHandlerRef = new \ReflectionClass(Error::class);
        $renderHtmlErrorMessageMethodRef = $errorHandlerRef->getMethod('renderHtmlErrorMessage');
        $renderHtmlErrorMessageMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlErrorMessageMethodRef->invoke($errorHandlerMock, $exception);

        // @see Slim\Handlers\Error::renderHtmlErrorMessage()
        $this->assertStringContainsString('<title>Slim Application Error</title>', $result);
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
    public function testRenderHtmlErrorMessageDebugOff()
    {
        define('APP_DEBUG', false);
        define('APP_ROOT', __DIR__);

        $exception = new \Exception('message');

        $errorHandlerMock = Mockery::mock(Error::class)->makePartial();

        $errorHandlerRef = new \ReflectionClass(Error::class);
        $renderHtmlErrorMessageMethodRef = $errorHandlerRef->getMethod('renderHtmlErrorMessage');
        $renderHtmlErrorMessageMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlErrorMessageMethodRef->invoke($errorHandlerMock, $exception);
        $this->assertStringContainsString('<title>Test Application Error</title>', $result);
    }
}
