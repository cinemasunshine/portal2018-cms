<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Repository;

use App\ORM\Repository\CampaignRepository;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CampaignRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(CampaignRepository::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&CampaignRepository
     */
    protected function createTargetMock()
    {
        return Mockery::mock(CampaignRepository::class);
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
    public function testFindOneById(): void
    {
        $id    = 99;
        $alias = 'c';

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
            ->with()
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

    /**
     * @test
     */
    public function testFindForListApi(): void
    {
        $name  = 'test';
        $alias = 'c';

        $queryBuilderMock = $this->createQueryBuilderMock();
        $queryBuilderMock
            ->shouldReceive('andWhere')
            ->once()
            ->with($alias . '.name LIKE :name')
            ->andReturn($queryBuilderMock);

        $queryBuilderMock
            ->shouldReceive('andWhere')
            ->once()
            ->with($alias . '.endDt > CURRENT_TIMESTAMP()')
            ->andReturn($queryBuilderMock);

        $queryBuilderMock
            ->shouldReceive('orderBy')
            ->once()
            ->with($alias . '.createdAt', 'DESC')
            ->andReturn($queryBuilderMock);

        $queryBuilderMock
            ->shouldReceive('setParameter')
            ->once()
            ->with('name', '%' . $name . '%')
            ->andReturn($queryBuilderMock);

        $result = [];
        $queryBuilderMock
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->with()
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

        $this->assertEquals($result, $targetMock->findForListApi($name));
    }

    /**
     * @test
     */
    public function testFindForListApiInvalidName(): void
    {
        $name = '';

        $this->expectException(InvalidArgumentException::class);

        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetMock->findForListApi($name);
    }
}
