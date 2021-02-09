<?php

/**
 * PhpErrorTest.php
 */

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Handlers\PhpError;
use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * PhpError handler test
 */
final class PhpErrorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(PhpError::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&PhpError
     */
    protected function createTargetMock()
    {
        return Mockery::mock(PhpError::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Logger
     */
    protected function createLoggerMock()
    {
        return Mockery::mock(Logger::class);
    }

    /**
     * test construct
     *
     * @test
     */
    public function testConstruct(): void
    {
        $loggerMock = $this->createLoggerMock();

        $displayErrorDetails = true;

        $targetMock = $this->createTargetMock();
        $targetRef  = $this->createTargetReflection();

        // execute constructor
        $phpErrorHandlerConstructor = $targetRef->getConstructor();
        $phpErrorHandlerConstructor->invoke($targetMock, $loggerMock, $displayErrorDetails);

        // test property "logger"
        $loggerPropertyRef = $targetRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $this->assertEquals($loggerMock, $loggerPropertyRef->getValue($targetMock));

        // test property "displayErrorDetails"
        $displayErrorDetailsPropertyRef = $targetRef->getProperty('displayErrorDetails');
        $displayErrorDetailsPropertyRef->setAccessible(true);
        $this->assertEquals(
            $displayErrorDetails,
            $displayErrorDetailsPropertyRef->getValue($targetMock)
        );
    }

    /**
     * test writeToErrorLog
     */
    public function testWriteToErrorLog(): void
    {
        $exception = new Exception();

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $targetMock
            ->shouldReceive('log')
            ->once()
            ->with($exception);

        $targetRef = $this->createTargetReflection();

        $writeToErrorLogRef = $targetRef->getMethod('writeToErrorLog');
        $writeToErrorLogRef->setAccessible(true);

        // execute
        $writeToErrorLogRef->invoke($targetMock, $exception);
    }

    /**
     * test log
     *
     * @test
     */
    public function testLog(): void
    {
        $message = 'message';

        // Exceptionのmockは出来ない？
        $exception = new Exception($message);

        $loggerMock = $this->createLoggerMock();
        $loggerMock
            ->shouldReceive('error')
            ->once()
            ->with($message, Mockery::type('array'));

        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetRef = $this->createTargetReflection();

        // test property "logger"
        $loggerPropertyRef = $targetRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $loggerPropertyRef->setValue($targetMock, $loggerMock);

        $logMethodRef = $targetRef->getMethod('log');
        $logMethodRef->setAccessible(true);

        // execute
        $logMethodRef->invoke($targetMock, $exception);
    }

    /**
     * test renderHtmlErrorMessage (debug on)
     *
     * 定数を使うので別プロセスで実行。
     *
     * @runInSeparateProcess
     * @test
     */
    public function testRenderHtmlErrorMessageDebugOn(): void
    {
        define('APP_DEBUG', true);
        define('APP_ROOT', __DIR__);

        $exception = new Exception('message');

        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetRef = $this->createTargetReflection();

        $renderHtmlErrorMessageMethodRef = $targetRef->getMethod('renderHtmlErrorMessage');
        $renderHtmlErrorMessageMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlErrorMessageMethodRef->invoke($targetMock, $exception);

        // @see Slim\Handlers\PhpError::renderHtmlErrorMessage()
        $this->assertStringContainsString('<title>Slim Application Error</title>', $result);
    }

    /**
     * test renderHtmlErrorMessage (debug off)
     *
     * 定数を使うので別プロセスで実行。
     *
     * @runInSeparateProcess
     * @test
     */
    public function testRenderHtmlErrorMessageDebugOff(): void
    {
        define('APP_DEBUG', false);
        define('APP_ROOT', __DIR__);

        $exception = new Exception('message');

        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetRef = $this->createTargetReflection();

        $renderHtmlErrorMessageMethodRef = $targetRef->getMethod('renderHtmlErrorMessage');
        $renderHtmlErrorMessageMethodRef->setAccessible(true);

        // execute
        $result = $renderHtmlErrorMessageMethodRef->invoke($targetMock, $exception);
        $this->assertStringContainsString('<title>Test Application Error</title>', $result);
    }
}
