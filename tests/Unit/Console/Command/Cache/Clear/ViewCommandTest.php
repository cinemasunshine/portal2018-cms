<?php

/**
 * ViewCommandTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Console\Command\Cache\Clear;

use App\Console\Command\Cache\Clear\ViewCommand;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Slim\Views\Twig;
use Symfony\Component\Filesystem\Filesystem;
use Tests\Unit\Console\Command\AbstructTestCase;
use Twig\Cache\FilesystemCache;
use Twig\Cache\NullCache;

/**
 * View Command test
 */
final class ViewCommandTest extends AbstructTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create target mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|ViewCommand
     */
    protected function createTargetMock()
    {
        return Mockery::mock(ViewCommand::class);
    }

    /**
     * Create target reflection
     *
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(ViewCommand::class);
    }

    /**
     * Create Twig mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|Twig
     */
    protected function createTwigMock()
    {
        return Mockery::mock(Twig::class);
    }

    /**
     * test construct
     *
     * @test
     * @return void
     */
    public function testConstruct()
    {
        $targetMock = $this->createTargetMock()
            ->makePartial();
        $targetRef = $this->createTargetReflection();
        $twigMock = $this->createTwigMock();

        $targetConstructor = $targetRef->getConstructor();
        $targetConstructor->invoke($targetMock, $twigMock);

        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $this->assertEquals($twigMock, $viewPropertyRef->getValue($targetMock));
    }

    /**
     * test execute (user FilesystemCache)
     *
     * @test
     * @return void
     */
    public function testExecuteUseFilesystemCache()
    {
        $filesystemCacheMock = $this->createFilesystemCacheMock();

        $twigEnvironmentMock = $this->createTwigEnvironmentMock();
        $twigEnvironmentMock
            ->shouldReceive('getCache')
            ->once()
            ->with(false)
            ->andReturn($filesystemCacheMock);

        $cacheDir = '/foo/bar/cache';
        $twigEnvironmentMock
            ->shouldReceive('getCache')
            ->once()
            ->with()
            ->andReturn($cacheDir);

        $twigMock = $this->createTwigMock();
        $twigMock
            ->shouldReceive('getEnvironment')
            ->with()
            ->andReturn($twigEnvironmentMock);

        $targetMock = $this->createTargetMock()
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $outputSpy = $this->createOutputSpy();
        $targetMock
            ->shouldReceive('clearFilesystemCache')
            ->once()
            ->with($cacheDir, $outputSpy);

        $inputMock = $this->createInputMock();

        $targetRef = $this->createTargetReflection();
        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($targetMock, $twigMock);

        $executeMethodRef = $targetRef->getMethod('execute');
        $executeMethodRef->setAccessible(true);

        // execute
        $result = $executeMethodRef->invoke($targetMock, $inputMock, $outputSpy);
        $this->assertEquals(0, $result);

        $outputSpy
            ->shouldHaveReceived('writeln')
            ->with(Mockery::type('string'))
            ->once();
    }

    /**
     * Create TwigEnvironment mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    protected function createTwigEnvironmentMock()
    {
        return Mockery::mock('TwigEnvironment');
    }

    /**
     * Create FilesystemCache mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|FilesystemCache
     */
    protected function createFilesystemCacheMock()
    {
        return Mockery::mock(FilesystemCache::class);
    }

    /**
     * test execute (user NullCahce)
     *
     * @test
     * @return void
     */
    public function testExecuteUseNullCache()
    {
        $nullCache = $this->createNullCache();

        $twigEnvironmentMock = $this->createTwigEnvironmentMock();
        $twigEnvironmentMock
            ->shouldReceive('getCache')
            ->once()
            ->with(false)
            ->andReturn($nullCache);

        $twigMock = $this->createTwigMock();
        $twigMock
            ->shouldReceive('getEnvironment')
            ->with()
            ->andReturn($twigEnvironmentMock);

        $targetMock = $this->createTargetMock()
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $targetMock
            ->shouldReceive('clearFilesystemCache')
            ->never();

        $inputMock = $this->createInputMock();
        $outputSpy = $this->createOutputSpy();

        $targetRef = $this->createTargetReflection();
        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($targetMock, $twigMock);

        $executeMethodRef = $targetRef->getMethod('execute');
        $executeMethodRef->setAccessible(true);

        // execute
        $result = $executeMethodRef->invoke($targetMock, $inputMock, $outputSpy);
        $this->assertEquals(0, $result);

        $outputSpy
            ->shouldHaveReceived('writeln')
            ->with(Mockery::type('string'))
            ->twice();
    }

    /**
     * Create NullCache
     *
     * finalが指定されたクラスはプロキシパーシャルテストダブルを使うことになるが、
     * instanceofのチェックをパスできないのでモックしない。
     *
     * @return NullCache
     */
    protected function createNullCache()
    {
        return new NullCache();
    }

    /**
     * test execute (use other cache)
     *
     * @test
     * @return void
     */
    public function testExecuteUseOtherCache()
    {
        $otherCache = $this->createOtherCacheMock();

        $twigEnvironmentMock = $this->createTwigEnvironmentMock();
        $twigEnvironmentMock
            ->shouldReceive('getCache')
            ->once()
            ->with(false)
            ->andReturn($otherCache);

        $twigMock = $this->createTwigMock();
        $twigMock
            ->shouldReceive('getEnvironment')
            ->with()
            ->andReturn($twigEnvironmentMock);

        $targetMock = $this->createTargetMock()
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $targetMock
            ->shouldReceive('clearFilesystemCache')
            ->never();

        $inputMock = $this->createInputMock();
        $outputSpy = $this->createOutputSpy();

        $targetRef = $this->createTargetReflection();
        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($targetMock, $twigMock);

        $this->expectException(\RuntimeException::class);

        $executeMethodRef = $targetRef->getMethod('execute');
        $executeMethodRef->setAccessible(true);

        // execute
        $executeMethodRef->invoke($targetMock, $inputMock, $outputSpy);
    }

    /**
     * Create OtherCache mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    protected function createOtherCacheMock()
    {
        return Mockery::mock('OtherCache');
    }

    /**
     * test clearFilesystemCache
     *
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @return void
     */
    public function testClearFilesystemCache()
    {
        $dir = '/foo/bar/cache';
        $filesystemMock = $this->createFilesystemMock();
        $filesystemMock
            ->shouldReceive('remove')
            ->once()
            ->with($dir);

        $outputSpy = $this->createOutputSpy();

        $targetMock = $this->createTargetMock();
        $targetRef = $this->createTargetReflection();

        $clearFilesystemCacheMethodRef = $targetRef->getMethod('clearFilesystemCache');
        $clearFilesystemCacheMethodRef->setAccessible(true);

        // execute
        $clearFilesystemCacheMethodRef->invoke($targetMock, $dir, $outputSpy);

        $outputSpy
            ->shouldHaveReceived('writeln')
            ->with(Mockery::type('string'))
            ->twice();
    }

    /**
     * Create Filesystem mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    protected function createFilesystemMock()
    {
        return Mockery::mock('overload:' . Filesystem::class);
    }
}
