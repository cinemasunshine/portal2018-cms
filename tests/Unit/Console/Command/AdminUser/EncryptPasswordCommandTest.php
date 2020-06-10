<?php

/**
 * EncryptPasswordCommandTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Console\Command\AdminUser;

use Cinemasunshine\PortalAdmin\Console\Command\AdminUser\EncryptPasswordCommand;
use Cinemasunshine\PortalAdmin\ORM\Entity\AdminUser;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\Unit\Console\Command\AbstructTestCase;

/**
 * Encrypt Passwerd Command test
 */
final class EncryptPasswordCommandTest extends AbstructTestCase
{
    use MockeryPHPUnitIntegration;

    protected const ARGUMENT_PASSWORD = 'password';

    /**
     * Create Target mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|EncryptPasswordCommand
     */
    protected function createTargetMock()
    {
        return Mockery::mock(EncryptPasswordCommand::class);
    }

    /**
     * Create target reflection
     *
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(EncryptPasswordCommand::class);
    }

    /**
     * test configure
     *
     * @test
     * @return void
     */
    public function testConfigure()
    {
        $targetMock = $this->createTargetMock();
        $targetMock
            ->shouldReceive('setDescription')
            ->once()
            ->with(Mockery::type('string'));
        $targetMock
            ->shouldReceive('addArgument')
            ->once()
            ->with(
                self::ARGUMENT_PASSWORD,
                Mockery::type('integer'),
                Mockery::type('string')
            );

        $targetRef = $this->createTargetReflection();
        $configureMethodRef = $targetRef->getMethod('configure');
        $configureMethodRef->setAccessible(true);

        $configureMethodRef->invoke($targetMock);
    }

    /**
     * test execute
     *
     * @test
     * @return void
     */
    public function testExecute()
    {
        $password = 'plain_password';
        $encryptedPassword = 'encrypted_password';

        $inputMock = $this->createInputMock();
        $inputMock
            ->shouldReceive('getArgument')
            ->once()
            ->with(self::ARGUMENT_PASSWORD)
            ->andReturn($password);

        $outputSpy = $this->createOutputSpy();

        $targetMock = $this->createTargetMock();
        $targetMock
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $targetMock
            ->shouldReceive('encryptPassword')
            ->once()
            ->with($password)
            ->andReturn($encryptedPassword);

        $targetRef = $this->createTargetReflection();
        $executeMethodRef = $targetRef->getMethod('execute');
        $executeMethodRef->setAccessible(true);

        // execute
        $result = $executeMethodRef->invoke($targetMock, $inputMock, $outputSpy);
        $this->assertEquals(0, $result);

        $outputSpy
            ->shouldHaveReceived('writeln')
            ->with(Mockery::on(function ($argument) use ($password) {
                return strpos($argument, $password) !== false;
            }))
            ->once();
        $outputSpy
            ->shouldHaveReceived('writeln')
            ->with(Mockery::on(function ($argument) use ($encryptedPassword) {
                return strpos($argument, $encryptedPassword) !== false;
            }))
            ->once();
    }

    /**
     * test encryptPassword
     *
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @return void
     */
    public function testEncryptPassword()
    {
        $password = 'plain_password';
        $encryptedPassword = 'encrypted_password';

        $adminUserMock = Mockery::mock('alias:' . AdminUser::class);
        $adminUserMock
            ->shouldReceive('encryptPassword')
            ->once()
            ->with($password)
            ->andReturn($encryptedPassword);

        $targetMock = $this->createTargetMock();
        $targetRef = $this->createTargetReflection();
        $encryptPasswordMethodRef = $targetRef->getMethod('encryptPassword');
        $encryptPasswordMethodRef->setAccessible(true);

        // execute
        $result = $encryptPasswordMethodRef->invoke($targetMock, $password);
        $this->assertEquals($encryptedPassword, $result);
    }
}
