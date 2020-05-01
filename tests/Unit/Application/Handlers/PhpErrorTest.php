<?php

/**
 * PhpErrorTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use Cinemasunshine\PortalAdmin\Application\Handlers\PhpError;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Slim\Container;

/**
 * PhpError handler test
 */
final class PhpErrorTest extends TestCase
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

        $phpErrorHandlerMock = Mockery::mock(PhpError::class);

        // execute constructor
        $phpErrorHandlerRef = new \ReflectionClass(PhpError::class);
        $phpErrorHandlerConstructor = $phpErrorHandlerRef->getConstructor();
        $phpErrorHandlerConstructor->invoke($phpErrorHandlerMock, $containerMock);

        // test property "container"
        $containerPropertyRef = $phpErrorHandlerRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $this->assertEquals($containerMock, $containerPropertyRef->getValue($phpErrorHandlerMock));

        // test property "logger"
        $loggerPropertyRef = $phpErrorHandlerRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $this->assertEquals($loggerMock, $loggerPropertyRef->getValue($phpErrorHandlerMock));
    }

    /**
     * test writeToErrorLog
     *
     * @return void
     */
    public function testWriteToErrorLog()
    {
        $exception = new \Exception();
        $phpErrorHandlerMock = Mockery::mock(PhpError::class)->makePartial();
        $phpErrorHandlerMock->shouldAllowMockingProtectedMethods();
        $phpErrorHandlerMock
            ->shouldReceive('log')
            ->once()
            ->with($exception);

        $writeToErrorLogRef = new \ReflectionMethod($phpErrorHandlerMock, 'writeToErrorLog');
        $writeToErrorLogRef->setAccessible(true);

        // execute
        $writeToErrorLogRef->invoke($phpErrorHandlerMock, $exception);
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

        $phpErrorHandlerMock = Mockery::mock(PhpError::class)->makePartial();

        $phpErrorHandlerRef = new \ReflectionClass(PhpError::class);

        // test property "logger"
        $loggerPropertyRef = $phpErrorHandlerRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $loggerPropertyRef->setValue($phpErrorHandlerMock, $loggerMock);

        $logMethodRef = $phpErrorHandlerRef->getMethod('log');
        $logMethodRef->setAccessible(true);

        // execute
        $logMethodRef->invoke($phpErrorHandlerMock, $exception);
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

        $phpErrorHandlerMock = Mockery::mock(PhpError::class)->makePartial();

        $phpErrorHandlerRef = new \ReflectionClass(PhpError::class);
        $renderHtmlErrorMessageMethodRef = $phpErrorHandlerRef->getMethod('renderHtmlErrorMessage');
        $renderHtmlErrorMessageMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlErrorMessageMethodRef->invoke($phpErrorHandlerMock, $exception);

        // @see Slim\Handlers\PhpError::renderHtmlErrorMessage()
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

        $phpErrorHandlerMock = Mockery::mock(PhpError::class)->makePartial();

        $phpErrorHandlerRef = new \ReflectionClass(PhpError::class);
        $renderHtmlErrorMessageMethodRef = $phpErrorHandlerRef->getMethod('renderHtmlErrorMessage');
        $renderHtmlErrorMessageMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlErrorMessageMethodRef->invoke($phpErrorHandlerMock, $exception);
        $this->assertStringContainsString('<title>Test Application Error</title>', $result);
    }
}
