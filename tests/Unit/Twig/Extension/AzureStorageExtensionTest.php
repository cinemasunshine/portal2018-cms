<?php

/**
 * AzureStorageExtensionTest.php
 */

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use App\Twig\Extension\AzureStorageExtension;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Twig\TwigFunction;

/**
 * Azure Storage extension test
 */
final class AzureStorageExtensionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create BlobRestProxy mock
     *
     * @return MockInterface|LegacyMockInterface|BlobRestProxy
     */
    protected function crateBlobRestProxyMock()
    {
        return Mockery::mock(BlobRestProxy::class);
    }

    /**
     * test construct
     *
     * @test
     */
    public function testConstruct(): void
    {
        $azureStorageExtensionMock = Mockery::mock(AzureStorageExtension::class);
        $blobRestProxyMock         = $this->crateBlobRestProxyMock();
        $publicEndpoint            = 'http://example.com';

        $azureStorageExtensionClassRef = new ReflectionClass(AzureStorageExtension::class);

        // execute constructor
        $constructorRef = $azureStorageExtensionClassRef->getConstructor();
        $constructorRef->invoke($azureStorageExtensionMock, $blobRestProxyMock, $publicEndpoint);

        // test property "client"
        $clientPropertyRef = $azureStorageExtensionClassRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $this->assertEquals(
            $blobRestProxyMock,
            $clientPropertyRef->getValue($azureStorageExtensionMock)
        );

        // test property "publicEndpoint"
        $publicEndpointPropertyRef = $azureStorageExtensionClassRef->getProperty('publicEndpoint');
        $publicEndpointPropertyRef->setAccessible(true);
        $this->assertEquals(
            $publicEndpoint,
            $publicEndpointPropertyRef->getValue($azureStorageExtensionMock)
        );
    }

    /**
     * test getFunctions
     *
     * @test
     */
    public function testGetFunctions(): void
    {
        $azureStorageExtensionMock = Mockery::mock(AzureStorageExtension::class)
            ->makePartial();

        $functions = $azureStorageExtensionMock->getFunctions();

        $this->assertIsArray($functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }

    /**
     * test blobUrl has publicEndpoint
     *
     * @test
     */
    public function testBlobUrlHasPublicEndpoint(): void
    {
        $azureStorageExtensionMock = Mockery::mock(AzureStorageExtension::class)
            ->makePartial();

        $azureStorageExtensionClassRef = new ReflectionClass(AzureStorageExtension::class);

        $publicEndpointPropertyRef = $azureStorageExtensionClassRef->getProperty('publicEndpoint');
        $publicEndpointPropertyRef->setAccessible(true);

        $publicEndpoint = 'http://example.com';
        $publicEndpointPropertyRef->setValue($azureStorageExtensionMock, $publicEndpoint);

        $container = 'test';
        $blob      = 'sample.txt';

        // execute
        $result = $azureStorageExtensionMock->blobUrl($container, $blob);
        $this->assertStringContainsString($publicEndpoint, $result);
        $this->assertStringContainsString($container, $result);
        $this->assertStringContainsString($blob, $result);
    }

    /**
     * test blobUrl do not has publicEndpoint
     *
     * @test
     */
    public function testBlobUrlDoNotHasPublicEndpoint(): void
    {
        $container = 'test';
        $blob      = 'sample.txt';
        $url       = 'http://storage.example.com/' . $container . '/' . $blob;

        $azureStorageExtensionMock = Mockery::mock(AzureStorageExtension::class)
            ->makePartial();

        $blobRestProxyMock = $this->crateBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('getBlobUrl')
            ->once()
            ->with($container, $blob)
            ->andReturn($url);

        $azureStorageExtensionClassRef = new ReflectionClass(AzureStorageExtension::class);

        $clientPropertyRef = $azureStorageExtensionClassRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($azureStorageExtensionMock, $blobRestProxyMock);

        $publicEndpointPropertyRef = $azureStorageExtensionClassRef->getProperty('publicEndpoint');
        $publicEndpointPropertyRef->setAccessible(true);
        $publicEndpointPropertyRef->setValue($azureStorageExtensionMock, null);

        // execute
        $result = $azureStorageExtensionMock->blobUrl($container, $blob);
        $this->assertEquals($url, $result);
    }
}
