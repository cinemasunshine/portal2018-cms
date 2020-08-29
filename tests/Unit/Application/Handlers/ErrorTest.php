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
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * Error handler test
 */
final class ErrorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&Logger
     */
    protected function createLoggerMock()
    {
        return Mockery::mock(Logger::class);
    }

    /**
     * test construct
     *
     * @test
     * @return void
     */
    public function testConstruct()
    {
        $loggerMock = $this->createLoggerMock();

        $displayErrorDetails = true;

        $errorHandlerMock = Mockery::mock(Error::class);

        // execute constructor
        $errorHandlerRef = new \ReflectionClass(Error::class);
        $errorHandlerConstructor = $errorHandlerRef->getConstructor();
        $errorHandlerConstructor->invoke($errorHandlerMock, $loggerMock, $displayErrorDetails);

        // test property "logger"
        $loggerPropertyRef = $errorHandlerRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $this->assertEquals($loggerMock, $loggerPropertyRef->getValue($errorHandlerMock));

        // test property "displayErrorDetails"
        $displayErrorDetailsPropertyRef = $errorHandlerRef->getProperty('displayErrorDetails');
        $displayErrorDetailsPropertyRef->setAccessible(true);
        $this->assertEquals(
            $displayErrorDetails,
            $displayErrorDetailsPropertyRef->getValue($errorHandlerMock)
        );
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
