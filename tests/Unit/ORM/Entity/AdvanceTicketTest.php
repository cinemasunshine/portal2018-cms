<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Entity;

use App\ORM\Entity\AdvanceSale;
use App\ORM\Entity\AdvanceTicket;
use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass \App\ORM\Entity\AdvanceTicket
 */
final class AdvanceTicketTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return MockInterface&LegacyMockInterface&AdvanceTicket
     */
    private function createAdvanceTicketMock()
    {
        return Mockery::mock(AdvanceTicket::class);
    }

    private function createAdvanceTicketReflection(): ReflectionClass
    {
        return new ReflectionClass(AdvanceTicket::class);
    }

    /**
     * @return array<string,array{int,string}>
     */
    public function getTypeLabelDataProvider(): array
    {
        $dp = [];

        $types = AdvanceTicket::getTypes();

        foreach ($types as $type => $label) {
            $dp['type ' . $type] = [
                $type,
                $label,
            ];
        }

        return $dp;
    }

    /**
     * @covers ::getTypeLabel
     * @dataProvider getTypeLabelDataProvider
     * @test
     * @testdox getTypeLabelはtypeに対応する文字列を返す
     */
    public function testGetTypeLabelCaseExists(int $type, string $label): void
    {
        $targetMock = $this->createAdvanceTicketMock();
        $targetMock->makePartial();
        $targetMock
            ->shouldReceive('getType')
            ->andReturn($type);

        $this->assertEquals($label, $targetMock->getTypeLabel());
    }

    /**
     * @covers ::getTypeLabel
     * @test
     * @testdox getTypeLabelはtypeに対応するlabelが無い場合はnullを返す
     */
    public function testGetTypeLabelCaseNotExists(): void
    {
        $notExistsType = 99;

        $targetMock = $this->createAdvanceTicketMock();
        $targetMock->makePartial();
        $targetMock
            ->shouldReceive('getType')
            ->andReturn($notExistsType);

        $this->assertNull($targetMock->getTypeLabel());
    }

    /**
     * @return array<string,array{int,string}>
     */
    public function getSpecialGiftStockLabelDataProvider(): array
    {
        $dp = [];

        $specialGiftStockList = AdvanceTicket::getSpecialGiftStockList();

        foreach ($specialGiftStockList as $specialGiftStock => $label) {
            $dp['special_gift_stock ' . $specialGiftStock] = [
                $specialGiftStock,
                $label,
            ];
        }

        return $dp;
    }

    /**
     * @covers ::getSpecialGiftStockLabel
     * @dataProvider getSpecialGiftStockLabelDataProvider
     * @test
     * @testdox getSpecialGiftStockLabelはspecialGiftStockに対応する文字列を返す
     */
    public function testGetSpecialGiftStockLabelCaseExists(int $specialGiftStock, string $label): void
    {
        $targetMock = $this->createAdvanceTicketMock();
        $targetMock->makePartial();
        $targetMock
            ->shouldReceive('getSpecialGiftStock')
            ->andReturn($specialGiftStock);

        $this->assertEquals($label, $targetMock->getSpecialGiftStockLabel());
    }

    /**
     * @covers ::getSpecialGiftStockLabel
     * @test
     * @testdox getSpecialGiftStockLabelはspecialGiftStockに対応するlabelが無い場合はnullを返す
     */
    public function testGetSpecialGiftStockLabelCaseNotExists(): void
    {
        $notExistsSpecialGiftStock = 99;

        $targetMock = $this->createAdvanceTicketMock();
        $targetMock->makePartial();
        $targetMock
            ->shouldReceive('getSpecialGiftStock')
            ->andReturn($notExistsSpecialGiftStock);

        $this->assertNull($targetMock->getSpecialGiftStockLabel());
    }

    /**
     * @covers ::getStatusLabel
     * @test
     * @testdox getStatusLabelはisSalseEndがtrueの場合、"販売終了"を返す
     */
    public function testGetStatusLabelCaseIsSalesEndTrue(): void
    {
        $targetMock = $this->createAdvanceTicketMock();
        $targetMock->makePartial();
        $targetMock
            ->shouldReceive('isSalseEnd')
            ->andReturn(true);

        $this->assertEquals('販売終了', $targetMock->getStatusLabel());
    }

    /**
     * @return MockInterface&LegacyMockInterface&AdvanceSale
     */
    private function craeteAdvanceSaleMock()
    {
        return Mockery::mock(AdvanceSale::class);
    }

    /**
     * @covers ::getStatusLabel
     * @test
     * @testdox getStatusLabelは現在日が公開予定日を過ぎている場合、"販売終了"を返す
     */
    public function testGetStatusLabelCaseAfterPublishingExpectedDate(): void
    {
        $targetMock = $this->createAdvanceTicketMock();
        $targetMock->makePartial();
        $targetMock
            ->shouldReceive('isSalseEnd')
            ->andReturn(false);

        $advanceSaleMock = $this->craeteAdvanceSaleMock();
        $advanceSaleMock
            ->shouldReceive('getPublishingExpectedDate')
            ->andReturn((new DateTime())->modify('-1 day'));

        $targetMock
            ->shouldReceive('getAdvanceSale')
            ->andReturn($advanceSaleMock);

        $this->assertEquals('販売終了', $targetMock->getStatusLabel());
    }

    /**
     * @covers ::getStatusLabel
     * @test
     * @testdox getStatusLabelは現在日が発売日時より前の場合、"販売予定"を返す
     */
    public function estGetStatusLabelCaseBeforeReleaseDt(): void
    {
        $targetMock = $this->createAdvanceTicketMock();
        $targetMock->makePartial();
        $targetMock
            ->shouldReceive('isSalseEnd')
            ->andReturn(false);
        $targetMock
            ->shouldReceive('getReleaseDt')
            ->andReturn((new DateTime())->modify('+1 minute'));

        $advanceSaleMock = $this->craeteAdvanceSaleMock();
        $advanceSaleMock
            ->shouldReceive('getPublishingExpectedDate')
            ->andReturn((new DateTime())->modify('+1 day')); // bug 同じ日付も有効でなければならない

        $targetMock
            ->shouldReceive('getAdvanceSale')
            ->andReturn($advanceSaleMock);

        $this->assertEquals('販売予定', $targetMock->getStatusLabel());
    }

    /**
     * @covers ::getStatusLabel
     * @test
     * @testdox getStatusLabelは現在日が発売日時を過ぎている場合、"販売中"を返す
     */
    public function testGetStatusLabelCaseAfterReleaseDt(): void
    {
        $targetMock = $this->createAdvanceTicketMock();
        $targetMock->makePartial();
        $targetMock
            ->shouldReceive('isSalseEnd')
            ->andReturn(false);
        $targetMock
            ->shouldReceive('getReleaseDt')
            ->andReturn((new DateTime())->modify('-1 minute'));

        $advanceSaleMock = $this->craeteAdvanceSaleMock();
        $advanceSaleMock
            ->shouldReceive('getPublishingExpectedDate')
            ->andReturn((new DateTime())->modify('+1 day')); // bug 同じ日付も有効でなければならない

        $targetMock
            ->shouldReceive('getAdvanceSale')
            ->andReturn($advanceSaleMock);

        $this->assertEquals('販売中', $targetMock->getStatusLabel());
    }

    /**
     * @covers ::getTypes
     * @test
     * @testdox getTypesはプロパティtypesの値を返す
     */
    public function testGetTypes(): void
    {
        $advanceTiketRef = $this->createAdvanceTicketReflection();

        $typesPropertyRef = $advanceTiketRef->getProperty('types');
        $typesPropertyRef->setAccessible(true);

        $this->assertEquals(
            $typesPropertyRef->getValue(),
            AdvanceTicket::getTypes()
        );
    }

    /**
     * @covers ::getSpecialGiftStockList
     * @test
     * @testdox getSpecialGiftStockListはプロパティspecialGiftStockListの値を返す
     */
    public function testGetSpecialGiftStockList(): void
    {
        $advanceTiketRef = $this->createAdvanceTicketReflection();

        $specialGiftStockListPropertyRef = $advanceTiketRef->getProperty('specialGiftStockList');
        $specialGiftStockListPropertyRef->setAccessible(true);

        $this->assertEquals(
            $specialGiftStockListPropertyRef->getValue(),
            AdvanceTicket::getSpecialGiftStockList()
        );
    }
}
