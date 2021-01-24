<?php

/**
 * AbstructTestCase.php
 */

declare(strict_types=1);

namespace Tests\Unit\Console\Command;

use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstructTestCase extends TestCase
{
    /**
     * Create Input mock
     *
     * @return MockInterface|LegacyMockInterface|InputInterface
     */
    protected function createInputMock()
    {
        return Mockery::mock(InputInterface::class);
    }

    /**
     * Create Input spy
     *
     * @return MockInterface|LegacyMockInterface
     */
    protected function createInputSpy()
    {
        return Mockery::spy(InputInterface::class);
    }

    /**
     * Create Output mock
     *
     * @return MockInterface|LegacyMockInterface|OutputInterface
     */
    protected function createOutputMock()
    {
        return Mockery::mock(OutputInterface::class);
    }

    /**
     * Create Output spy
     *
     * @return MockInterface|LegacyMockInterface
     */
    protected function createOutputSpy()
    {
        return Mockery::spy(OutputInterface::class);
    }
}
