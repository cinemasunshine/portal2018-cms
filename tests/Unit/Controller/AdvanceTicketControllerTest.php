<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\AdvanceTicketController;
use App\ORM\Entity\AdvanceSale;
use App\ORM\Entity\AdvanceTicket;
use App\ORM\Entity\Theater;
use App\ORM\Entity\Title;
use App\ORM\Repository\AdvanceSaleRepository;
use App\ORM\Repository\AdvanceTicketRepository;
use App\ORM\Repository\TheaterRepository;
use App\ORM\Repository\TitleRepository;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use Slim\Container;

/**
 * @coversDefaultClass \App\Controller\AdvanceTicketController
 */
final class AdvanceTicketControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&AdvanceTicketController
     */
    protected function createTargetMock(Container $container)
    {
        return Mockery::mock(AdvanceTicketController::class, [$container]);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(AdvanceTicketController::class);
    }

    /**
     * @covers ::getAdvanceTicketRepository
     * @test
     * @testdox getAdvanceTicketRepositoryはエンティティAdvanceTicketのリポジトリを取得する
     */
    public function testGetAdvanceTicketRepository(): void
    {
        $container = $this->createContainer();
        $container['em']
            ->shouldReceive('getRepository')
            ->with(AdvanceTicket::class)
            ->andReturn(Mockery::mock(AdvanceTicketRepository::class));

        $targetMock = $this->createTargetMock($container);

        $targetRef = $this->createTargetReflection();

        $getAdvanceTicketRepositoryMethodRef = $targetRef->getMethod('getAdvanceTicketRepository');
        $getAdvanceTicketRepositoryMethodRef->setAccessible(true);

        $this->assertInstanceOf(
            AdvanceTicketRepository::class,
            $getAdvanceTicketRepositoryMethodRef->invoke($targetMock)
        );
    }

    /**
     * @covers ::getAdvanceSaleRepository
     * @test
     * @testdox getAdvanceSaleRepositoryはエンティティAdvanceSaleのリポジトリを取得する
     */
    public function testGetAdvanceSaleRepository(): void
    {
        $container = $this->createContainer();
        $container['em']
            ->shouldReceive('getRepository')
            ->with(AdvanceSale::class)
            ->andReturn(Mockery::mock(AdvanceSaleRepository::class));

        $targetMock = $this->createTargetMock($container);

        $targetRef = $this->createTargetReflection();

        $getAdvanceSaleRepositoryMethodRef = $targetRef->getMethod('getAdvanceSaleRepository');
        $getAdvanceSaleRepositoryMethodRef->setAccessible(true);

        $this->assertInstanceOf(
            AdvanceSaleRepository::class,
            $getAdvanceSaleRepositoryMethodRef->invoke($targetMock)
        );
    }

    /**
     * @covers ::getTheaterRepository
     * @test
     * @testdox getTheaterRepositoryはエンティティTheaterのリポジトリを取得する
     */
    public function testGetTheaterRepository(): void
    {
        $container = $this->createContainer();
        $container['em']
            ->shouldReceive('getRepository')
            ->with(Theater::class)
            ->andReturn(Mockery::mock(TheaterRepository::class));

        $targetMock = $this->createTargetMock($container);

        $targetRef = $this->createTargetReflection();

        $getTheaterRepositoryMethodRef = $targetRef->getMethod('getTheaterRepository');
        $getTheaterRepositoryMethodRef->setAccessible(true);

        $this->assertInstanceOf(
            TheaterRepository::class,
            $getTheaterRepositoryMethodRef->invoke($targetMock)
        );
    }

    /**
     * @covers ::getTitleRepository
     * @test
     * @testdox getTitleRepositoryはエンティティTitleのリポジトリを取得する
     */
    public function testGetTitleRepository(): void
    {
        $container = $this->createContainer();
        $container['em']
            ->shouldReceive('getRepository')
            ->with(Title::class)
            ->andReturn(Mockery::mock(TitleRepository::class));

        $targetMock = $this->createTargetMock($container);

        $targetRef = $this->createTargetReflection();

        $getTitleRepositoryMethodRef = $targetRef->getMethod('getTitleRepository');
        $getTitleRepositoryMethodRef->setAccessible(true);

        $this->assertInstanceOf(
            TitleRepository::class,
            $getTitleRepositoryMethodRef->invoke($targetMock)
        );
    }

    /**
     * @covers ::renderNew
     * @test
     * @testdox renderNewはテンプレート"advance_ticket/new.html.twig"をレンダリングする
     */
    public function testRenderNew(): void
    {
        $responseMock = $this->createResponseMock();
        $data         = ['foo' => 'bar'];

        $targetMock = $this->createTargetMock($this->createContainer());
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'advance_ticket/new.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderNewMethodRef = $targetRef->getMethod('renderNew');
        $renderNewMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderNewMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }

    /**
     * @covers ::renderEdit
     * @test
     * @testdox renderEditはテンプレート"advance_ticket/edit.html.twig"をレンダリングする
     */
    public function testRenderEdit(): void
    {
        $responseMock = $this->createResponseMock();
        $data         = ['foo' => 'bar'];

        $targetMock = $this->createTargetMock($this->createContainer());
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'advance_ticket/edit.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderEditMethodRef = $targetRef->getMethod('renderEdit');
        $renderEditMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderEditMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }
}
