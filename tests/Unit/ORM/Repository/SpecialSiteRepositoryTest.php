<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Repository;

use App\ORM\Repository\SpecialSiteRepository;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

final class SpecialSiteRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return MockInterface&LegacyMockInterface&SpecialSiteRepository
     */
    protected function createTargetMock()
    {
        return Mockery::mock(SpecialSiteRepository::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&QueryBuilder
     */
    protected function createQueryBuilderMock()
    {
        return Mockery::mock(QueryBuilder::class);
    }

    /**
     * @test
     */
    public function testfindActive(): void
    {
        $alias = 's';

        $queryBuilderMock = $this->createQueryBuilderMock();

        $result = [];
        $queryBuilderMock
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->andReturn($result);

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

        $this->assertEquals($result, $targetMock->findActive());
    }

    /**
     * @test
     */
    public function testFindByIds(): void
    {
        $ids   = [98, 99];
        $alias = 's';

        $queryBuilderMock = $this->createQueryBuilderMock();

        $queryBuilderMock
            ->shouldReceive('andWhere')
            ->once()
            ->with($alias . '.id IN (:ids)')
            ->andReturn($queryBuilderMock);

        $queryBuilderMock
            ->shouldReceive('setParameter')
            ->once()
            ->with('ids', $ids)
            ->andReturn($queryBuilderMock);

        $result = [];
        $queryBuilderMock
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->andReturn($result);

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

        $this->assertEquals($result, $targetMock->findByIds($ids));
    }

    /**
     * @test
     */
    public function testFindOneById(): void
    {
        $id    = 99;
        $alias = 's';

        $queryBuilderMock = $this->createQueryBuilderMock();

        $queryBuilderMock
            ->shouldReceive('andWhere')
            ->once()
            ->with($alias . '.id = :id')
            ->andReturn($queryBuilderMock);

        $queryBuilderMock
            ->shouldReceive('setParameter')
            ->once()
            ->with('id', $id)
            ->andReturn($queryBuilderMock);

        $result = null;
        $queryBuilderMock
            ->shouldReceive('getQuery->getOneOrNullResult')
            ->once()
            ->andReturn($result);

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

        $this->assertEquals($result, $targetMock->findOneById($id));
    }
}
