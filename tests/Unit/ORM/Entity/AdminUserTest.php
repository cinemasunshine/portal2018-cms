<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Entity;

use App\ORM\Entity\AdminUser;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class AdminUserTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return MockInterface&LegacyMockInterface&AdminUser
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AdminUser::class);
    }

    /**
     * @return ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new ReflectionClass(AdminUser::class);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testGetGroupLabel()
    {
        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetRef = $this->createTargetReflection();

        $groupPropertyRef = $targetRef->getProperty('group');
        $groupPropertyRef->setAccessible(true);

        $groupPropertyRef->setValue($targetMock, AdminUser::GROUP_MASTER);
        $this->assertEquals('マスター', $targetMock->getGroupLabel());

        $groupPropertyRef->setValue($targetMock, AdminUser::GROUP_MANAGER);
        $this->assertEquals('マネージャー', $targetMock->getGroupLabel());

        $groupPropertyRef->setValue($targetMock, AdminUser::GROUP_THEATER);
        $this->assertEquals('劇場', $targetMock->getGroupLabel());

        $groupPropertyRef->setValue($targetMock, 99);
        $this->assertEquals('', $targetMock->getGroupLabel());
    }
}
