<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Entity;

use App\ORM\Entity\Title;
use App\ORM\Entity\TitleRanking;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class TitleRankingTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return MockInterface&LegacyMockInterface&TitleRanking
     */
    protected function createTargetMock()
    {
        return Mockery::mock(TitleRanking::class);
    }

    /**
     * @return ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new ReflectionClass(TitleRanking::class);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testGetRank()
    {
        $maxRank = 5;

        for ($i = 1; $i <= $maxRank; $i++) {
            $targetMock = $this->createTargetMock();
            $targetMock->makePartial();

            $rank = $i;

            $titleMock = $this->createTitleMock();
            $titleMock->makePartial();
            $titleMock->setName(sprintf('title %s', $rank));

            $method = sprintf('getRank%sTitle', $rank);
            $targetMock
                ->shouldReceive($method)
                ->once()
                ->with()
                ->andReturn($titleMock);

            $this->assertEquals($titleMock, $targetMock->getRank($rank));
        }
    }

    /**
     * @test
     *
     * @return void
     */
    public function testGetRankInvalidRank()
    {
        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $this->expectException(InvalidArgumentException::class);

        $targetMock->getRank(6);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testSetRank()
    {
        $maxRank = 5;

        for ($i = 1; $i <= $maxRank; $i++) {
            $targetMock = $this->createTargetMock();
            $targetMock->makePartial();

            $rank = $i;

            $titleMock = $this->createTitleMock();
            $titleMock->makePartial();
            $titleMock->setName(sprintf('title %s', $rank));

            $method = sprintf('setRank%sTitle', $rank);
            $targetMock
                ->shouldReceive($method)
                ->once()
                ->with($titleMock);

            $targetMock->setRank($rank, $titleMock);
        }
    }

    /**
     * @test
     *
     * @return void
     */
    public function testSetRankInvalidRank()
    {
        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $titleMock = $this->createTitleMock();
        $titleMock->makePartial();

        $this->expectException(InvalidArgumentException::class);

        $targetMock->setRank(6, $titleMock);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Title
     */
    protected function createTitleMock()
    {
        return Mockery::mock(Title::class);
    }
}
