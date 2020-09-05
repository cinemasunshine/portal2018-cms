<?php

/**
 * DbalLoggerTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Logger;

use App\Logger\DbalLogger;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * DBAL Logger test
 */
final class DbalLoggerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create Logger mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|Logger
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

        $dbalLoggerMock = Mockery::mock(DbalLogger::class);

        // execute constructor
        $dbalLoggerRef = new \ReflectionClass(DbalLogger::class);
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
     * @return void
     */
    public function testStartQuery()
    {
        $sql = 'SHOW TABLES';
        $params = [
            'p' => 1,
        ];
        $types = [
            't' => 2,
        ];

        /** @var \Mockery\MockInterface|\Mockery\LegacyMockInterface|DbalLogger $dbalLoggerMock */
        $dbalLoggerMock = Mockery::mock(DbalLogger::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $dbalLoggerMock
            ->shouldReceive('log')
            ->once()
            ->with($sql, [ 'params' => $params, 'types' => $types ]);

        $dbalLoggerMock->startQuery($sql, $params, $types);
    }

    /**
     * test log
     *
     * @test
     * @return void
     */
    public function testLog()
    {
        $message = 'test';
        $context = [
            'detail' => 'example',
        ];

        $loggerMock = $this->createLoggerMock();
        $loggerMock
            ->shouldReceive('debug')
            ->once()
            ->with($message, $context);

        $dbalLoggerMock = Mockery::mock(DbalLogger::class);

        $dbalLoggerRef = new \ReflectionClass(DbalLogger::class);
        $loggerPropertyRef = $dbalLoggerRef->getProperty('logger');
        $loggerPropertyRef->setAccessible(true);
        $loggerPropertyRef->setValue($dbalLoggerMock, $loggerMock);

        $logMethodRef = $dbalLoggerRef->getMethod('log');
        $logMethodRef->setAccessible(true);
        $logMethodRef->invoke($dbalLoggerMock, $message, $context);
    }
}
