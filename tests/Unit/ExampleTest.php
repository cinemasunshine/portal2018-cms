<?php
/**
 * ExampleTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Example test
 */
final class ExampleTest extends TestCase
{
    /**
     * test success
     *
     * @test
     * @return void
     */
    public function testSuccess()
    {
        $this->assertTrue(true);
    }

    /**
     * test failure
     *
     * @test
     * @return void
     */
    public function testFailure()
    {
        $this->assertTrue(false);
    }
}
