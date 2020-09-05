<?php

/**
 * AzureBlobStorageHandlerTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Logger\Handler;

use App\Logger\Handler\AzureBlobStorageHandler as Handler;
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
     * Create target mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|Handler
     */
    protected function createTargetMock()
    {
        return Mockery::mock(Handler::class);
    }

    /**
     * Create target reflection
     *
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(Handler::class);
    }

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

        $targetMock = $this->createTargetMock();
        $targetRef = $this->createTargetReflection();

        $clientPropertyRef = $targetRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($targetMock, $blobRestProxyMock);

        $containerPropertyRef = $targetRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($targetMock, $container);

        $blobPropertyRef = $targetRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $blobPropertyRef->setValue($targetMock, $blob);

        $createBlobMethodRef = $targetRef->getMethod('createBlob');
        $createBlobMethodRef->setAccessible(true);

        // execute
        $createBlobMethodRef->invoke($targetMock);
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

        $targetMock = $this->createTargetMock();
        $targetRef = $this->createTargetReflection();

        $clientPropertyRef = $targetRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($targetMock, $blobRestProxyMock);

        $containerPropertyRef = $targetRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($targetMock, $container);

        $blobPropertyRef = $targetRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $blobPropertyRef->setValue($targetMock, $blob);

        $createBlobMethodRef = $targetRef->getMethod('createBlob');
        $createBlobMethodRef->setAccessible(true);

        // execute
        $createBlobMethodRef->invoke($targetMock);
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

        $targetMock = $this->createTargetMock();
        $targetRef = $this->createTargetReflection();

        $clientPropertyRef = $targetRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($targetMock, $blobRestProxyMock);

        $containerPropertyRef = $targetRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($targetMock, $container);

        $blobPropertyRef = $targetRef->getProperty('blob');
        $blobPropertyRef->setAccessible(true);
        $blobPropertyRef->setValue($targetMock, $blob);

        $this->expectException(ServiceException::class);

        $createBlobMethodRef = $targetRef->getMethod('createBlob');
        $createBlobMethodRef->setAccessible(true);

        // execute
        $createBlobMethodRef->invoke($targetMock);
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
        $record = [
            'formatted' => 'test',
        ];

        $targetMock = $this->createTargetMock()
            ->shouldAllowMockingProtectedMethods();
        $targetMock
            ->shouldReceive('createBlob')
            ->once()
            ->with();
        $targetRef = $this->createTargetReflection();

        $isBlobCreatedPropertyRef = $targetRef->getProperty('isBlobCreated');
        $isBlobCreatedPropertyRef->setAccessible(true);
        $isBlobCreatedPropertyRef->setValue($targetMock, $isBlobCreated);

        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('appendBlock')
            ->once();

        $clientPropertyRef = $targetRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($targetMock, $blobRestProxyMock);

        $writeMethodRef = $targetRef->getMethod('write');
        $writeMethodRef->setAccessible(true);

        // execute
        $writeMethodRef->invoke($targetMock, $record);

        $this->assertTrue($isBlobCreatedPropertyRef->getValue($targetMock));
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
        $record = [
            'formatted' => 'test',
        ];

        $targetMock = $this->createTargetMock()
            ->shouldAllowMockingProtectedMethods();
        $targetMock
            ->shouldReceive('createBlob')
            ->never();
        $targetRef = $this->createTargetReflection();

        $isBlobCreatedPropertyRef = $targetRef->getProperty('isBlobCreated');
        $isBlobCreatedPropertyRef->setAccessible(true);
        $isBlobCreatedPropertyRef->setValue($targetMock, $isBlobCreated);

        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('appendBlock')
            ->once();

        $clientPropertyRef = $targetRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($targetMock, $blobRestProxyMock);

        $writeMethodRef = $targetRef->getMethod('write');
        $writeMethodRef->setAccessible(true);

        // execute
        $writeMethodRef->invoke($targetMock, $record);
    }
}
