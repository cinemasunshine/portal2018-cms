<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Repository;

use App\ORM\Repository\TheaterRepository;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

final class TheaterRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return MockInterface&LegacyMockInterface&TheaterRepository
     */
    protected function createTargetMock()
    {
        return Mockery::mock(TheaterRepository::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&QueryBuilder
     */
    protected function createQueryBuilderMock()
    {
        return Mockery::mock(QueryBuilder::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface
     */
    protected function createQueryMock()
    {
        return Mockery::mock('Query');
    }

    /**
     * @test
     */
    public function testFindActive(): void
    {
        $alias = 't';

        $queryBuilderMock = $this->createQueryBuilderMock();

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $targetMock
            ->shouldReceive('createQueryBuilder')
            ->once()
            ->with($alias)
            ->andReturn($queryBuilderMock);
        $targetMock
            ->shouldReceive('addActiveQuery')
            ->once()
            ->with($queryBuilderMock, $alias);

        $queryBuilderMock
            ->shouldReceive('orderBy')
            ->once()
            ->with($alias . '.displayOrder', 'ASC');

        $queryMock = $this->createQueryMock();
        $queryBuilderMock
            ->shouldReceive('getQuery')
            ->once()
            ->andReturn($queryMock);

        $result = [];
        $queryMock
            ->shouldReceive('getResult')
            ->once()
            ->andReturn($result);

        $this->assertEquals($result, $targetMock->findActive());
    }

    /**
     * @test
     */
    public function testFindByIds(): void
    {
        $alias = 't';

        $queryBuilderMock = $this->createQueryBuilderMock();

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $targetMock
            ->shouldReceive('createQueryBuilder')
            ->once()
            ->with($alias)
            ->andReturn($queryBuilderMock);
        $targetMock
            ->shouldReceive('addActiveQuery')
            ->once()
            ->with($queryBuilderMock, $alias);

        $queryBuilderMock
            ->shouldReceive('andWhere')
            ->once()
            ->with($alias . '.id IN (:ids)')
            ->andReturn($queryBuilderMock);

        $ids = [2, 5];
        $queryBuilderMock
            ->shouldReceive('setParameter')
            ->once()
            ->with('ids', $ids)
            ->andReturn($queryBuilderMock);
        $queryBuilderMock
            ->shouldReceive('orderBy')
            ->once()
            ->with($alias . '.displayOrder', 'ASC');

        $queryMock = $this->createQueryMock();
        $queryBuilderMock
            ->shouldReceive('getQuery')
            ->once()
            ->andReturn($queryMock);

        $result = [];
        $queryMock
            ->shouldReceive('getResult')
            ->once()
            ->andReturn($result);

        $this->assertEquals($result, $targetMock->findByIds($ids));
    }

    /**
     * @test
     */
    public function testFindOneById(): void
    {
        $alias = 't';

        $queryBuilderMock = $this->createQueryBuilderMock();

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('createQueryBuilder')
            ->once()
            ->with($alias)
            ->andReturn($queryBuilderMock);
        $targetMock
            ->shouldReceive('addActiveQuery')
            ->once()
            ->with($queryBuilderMock, $alias);

        $queryBuilderMock
            ->shouldReceive('andWhere')
            ->once()
            ->with($alias . '.id = :id')
            ->andReturn($queryBuilderMock);

        $id = 6;
        $queryBuilderMock
            ->shouldReceive('setParameter')
            ->once()
            ->with('id', $id);

        $queryMock = $this->createQueryMock();
        $queryBuilderMock
            ->shouldReceive('getQuery')
            ->once()
            ->andReturn($queryMock);

        $result = null;
        $queryMock
            ->shouldReceive('getOneOrNullResult')
            ->once()
            ->andReturn($result);

        $this->assertEquals($result, $targetMock->findOneById($id));
    }
}
