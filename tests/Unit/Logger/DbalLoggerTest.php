<?php

/**
 * DbalLoggerTest.php
 */

declare(strict_types=1);

namespace Tests\Unit\Logger;

use App\Logger\DbalLogger;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * DBAL Logger test
 */
final class DbalLoggerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create Logger mock
     *
     * @return MockInterface|LegacyMockInterface|Logger
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

        $dbalLoggerMock = Mockery::mock(DbalLogger::class);
        $dbalLoggerRef  = new ReflectionClass(DbalLogger::class);

        // execute constructor
        $dbalLoggerConstructor = $dbalLoggerRef->getConstructor();
        $dbalLoggerConstructor->invoke($dbalLoggerMock, $loggerMock);

        // test property "logger"
        $loggerPropertyRef = $dbalLoggerRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $this->assertEquals($loggerMock, $loggerPropertyRef->getValue($dbalLoggerMock));
    }

    /**
     * test startQuery
     *
     * @test
     */
    public function testStartQuery(): void
    {
        $sql    = 'SHOW TABLES';
        $params = ['p' => 1];
        $types  = ['t' => 2];

        /** @var MockInterface|LegacyMockInterface|DbalLogger $dbalLoggerMock */
        $dbalLoggerMock = Mockery::mock(DbalLogger::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $dbalLoggerMock
            ->shouldReceive('log')
            ->once()
            ->with($sql, ['params' => $params, 'types' => $types]);

        $dbalLoggerMock->startQuery($sql, $params, $types);
    }

    /**
     * test log
     *
     * @test
     */
    public function testLog(): void
    {
        $message = 'test';
        $context = ['detail' => 'example'];

        $loggerMock = $this->createLoggerMock();
        $loggerMock
            ->shouldReceive('debug')
            ->once()
            ->with($message, $context);

        $dbalLoggerMock = Mockery::mock(DbalLogger::class);
        $dbalLoggerRef  = new ReflectionClass(DbalLogger::class);

        $loggerPropertyRef = $dbalLoggerRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $loggerPropertyRef->setValue($dbalLoggerMock, $loggerMock);

        $logMethodRef = $dbalLoggerRef->getMethod('log');
        $logMethodRef->setAccessible(true);
        $logMethodRef->invoke($dbalLoggerMock, $message, $context);
    }
}
