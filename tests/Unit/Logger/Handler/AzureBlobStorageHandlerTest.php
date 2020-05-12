<?php

/**
 * AzureBlobStorageHandlerTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Logger\Handler;

use Cinemasunshine\PortalAdmin\Logger\Handler\AzureBlobStorageHandler as Handler;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * AzureBlobStorage handler test
 */
final class AzureBlobStorageHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create BlobRestProxy mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|BlobRestProxy
     */
    protected function createBlobRestProxyMock()
    {
        return Mockery::mock(BlobRestProxy::class);
    }

    /**
     * test construct
     *
     * @test
     * @return void
     */
    public function testConstruct()
    {
        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $container = 'example';
        $blob = 'test.log';

        $handlerMock = Mockery::mock(Handler::class)
            ->makePartial();

        // execute constructor
        $handlerRef = new \ReflectionClass(Handler::class);
        $handlerConstructor = $handlerRef->getConstructor();
        $handlerConstructor->invoke(
            $handlerMock,
            $blobRestProxyMock,
            $container,
            $blob
        );

        // test property "client"
        $clientPropertyRef = $handlerRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $this->assertEquals($blobRestProxyMock, $clientPropertyRef->getValue($handlerMock));

        // test property "container"
        $containerPropertyRef = $handlerRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $this->assertEquals($container, $containerPropertyRef->getValue($handlerMock));

        // test property "blob"
        $blobPropertyRef = $handlerRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $this->assertEquals($blob, $blobPropertyRef->getValue($handlerMock));

        // test property "isBlobCreated"
        $isBlobCreatedPropertyRef = $handlerRef->getProperty('isBlobCreated');
        $isBlobCreatedPropertyRef->setAccessible(true);
        $this->assertFalse($isBlobCreatedPropertyRef->getValue($handlerMock));
    }

    /**
     * test createBlob (Blob Existing)
     *
     * @test
     * @return void
     */
    public function testCreateBlobExisting()
    {
        $container = 'example';
        $blob = 'test.log';

        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('getBlobMetadata')
            ->once()
            ->with($container, $blob);

        $blobRestProxyMock
            ->shouldReceive('createAppendBlob')
            ->never();

        /** @var \Mockery\MockInterface|\Mockery\LegacyMockInterface|Handler $handlerMock */
        $handlerMock = Mockery::mock(Handler::class);
        $handlerRef = new \ReflectionClass(Handler::class);

        $clientPropertyRef = $handlerRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($handlerMock, $blobRestProxyMock);

        $containerPropertyRef = $handlerRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($handlerMock, $container);

        $blobPropertyRef = $handlerRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $blobPropertyRef->setValue($handlerMock, $blob);

        $createBlobMethodRef = $handlerRef->getMethod('createBlob');
        $createBlobMethodRef->setAccessible(true);

        // execute
        $createBlobMethodRef->invoke($handlerMock);
    }

    /**
     * test createBlob (Blob Not Found)
     *
     * @test
     * @return void
     */
    public function testCreateBlobNotFound()
    {
        $container = 'example';
        $blob = 'test.log';

        $exception = $this->createServiceException(404);

        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('getBlobMetadata')
            ->once()
            ->with($container, $blob)
            ->andThrow($exception);

        $blobRestProxyMock
            ->shouldReceive('createAppendBlob')
            ->once();

        $handlerMock = Mockery::mock(Handler::class);
        $handlerRef = new \ReflectionClass(Handler::class);

        $clientPropertyRef = $handlerRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($handlerMock, $blobRestProxyMock);

        $containerPropertyRef = $handlerRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($handlerMock, $container);

        $blobPropertyRef = $handlerRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $blobPropertyRef->setValue($handlerMock, $blob);

        $createBlobMethodRef = $handlerRef->getMethod('createBlob');
        $createBlobMethodRef->setAccessible(true);

        // execute
        $createBlobMethodRef->invoke($handlerMock);
    }

    /**
     * test createBlob (Service Error)
     *
     * @test
     * @return void
     */
    public function testCreateBlobServiceError()
    {
        $container = 'example';
        $blob = 'test.log';

        $exception = $this->createServiceException(500);

        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('getBlobMetadata')
            ->once()
            ->with($container, $blob)
            ->andThrow($exception);

        $blobRestProxyMock
            ->shouldReceive('createAppendBlob')
            ->never();

        $handlerMock = Mockery::mock(Handler::class);
        $handlerRef = new \ReflectionClass(Handler::class);

        $clientPropertyRef = $handlerRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($handlerMock, $blobRestProxyMock);

        $containerPropertyRef = $handlerRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($handlerMock, $container);

        $blobPropertyRef = $handlerRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $blobPropertyRef->setValue($handlerMock, $blob);

        $this->expectException(ServiceException::class);

        $createBlobMethodRef = $handlerRef->getMethod('createBlob');
        $createBlobMethodRef->setAccessible(true);

        // execute
        $createBlobMethodRef->invoke($handlerMock);
    }

    /**
     * Create ServiceException
     *
     * @param integer $status
     * @return ServiceException
     */
    protected function createServiceException(int $status)
    {
        $responceMock = $this->createResponceMock();
        $responceMock
            ->shouldReceive('getStatusCode')
            ->andReturn($status);
        $responceMock
            ->shouldReceive('getReasonPhrase')
            ->andReturn('Reason Phrase');
        $responceMock
            ->shouldReceive('getBody')
            ->andReturn('Body');

        return new ServiceException($responceMock);
    }

    /**
     * Create Responce mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|ResponseInterface
     */
    protected function createResponceMock()
    {
        return Mockery::mock(ResponseInterface::class);
    }

    /**
     * test write
     *
     * @test
     * @return void
     */
    public function testWrite()
    {
        $isBlobCreated = false;
        $container = 'example';
        $blob = 'test.log';
        $record = [
            'formatted' => 'test',
        ];

        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('appendBlock')
            ->once()
            ->with($container, $blob, $record['formatted']);

        $handlerMock = Mockery::mock(Handler::class)
            ->shouldAllowMockingProtectedMethods();
        $handlerMock
            ->shouldReceive('createBlob')
            ->once()
            ->with();
        $handlerRef = new \ReflectionClass(Handler::class);

        $isBlobCreatedPropertyRef = $handlerRef->getProperty('isBlobCreated');
        $isBlobCreatedPropertyRef->setAccessible(true);
        $isBlobCreatedPropertyRef->setValue($handlerMock, $isBlobCreated);

        $clientPropertyRef = $handlerRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($handlerMock, $blobRestProxyMock);

        $containerPropertyRef = $handlerRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($handlerMock, $container);

        $blobPropertyRef = $handlerRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $blobPropertyRef->setValue($handlerMock, $blob);

        $writeMethodRef = $handlerRef->getMethod('write');
        $writeMethodRef->setAccessible(true);

        // execute
        $writeMethodRef->invoke($handlerMock, $record);
    }

    /**
     * test write (Is Blob Created)
     *
     * @test
     * @return void
     */
    public function testWriteIsBlobCreated()
    {
        $isBlobCreated = true;
        $container = 'example';
        $blob = 'test.log';
        $record = [
            'formatted' => 'test',
        ];

        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('appendBlock')
            ->once();

        $handlerMock = Mockery::mock(Handler::class)
            ->shouldAllowMockingProtectedMethods();
        $handlerMock
            ->shouldReceive('createBlob')
            ->never();
        $handlerRef = new \ReflectionClass(Handler::class);

        $isBlobCreatedPropertyRef = $handlerRef->getProperty('isBlobCreated');
        $isBlobCreatedPropertyRef->setAccessible(true);
        $isBlobCreatedPropertyRef->setValue($handlerMock, $isBlobCreated);

        $clientPropertyRef = $handlerRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($handlerMock, $blobRestProxyMock);

        $containerPropertyRef = $handlerRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($handlerMock, $container);

        $blobPropertyRef = $handlerRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $blobPropertyRef->setValue($handlerMock, $blob);

        $writeMethodRef = $handlerRef->getMethod('write');
        $writeMethodRef->setAccessible(true);

        // execute
        $writeMethodRef->invoke($handlerMock, $record);
    }
}
