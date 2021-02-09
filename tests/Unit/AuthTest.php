<?php

/**
 * AuthTest.php
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Auth;
use App\ORM\Entity\AdminUser;
use Laminas\Stdlib\ArrayObject;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * Auth test
 */
final class AuthTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create Container mock
     *
     * ContainerInterfaceを実装したモックを作成する。
     *
     * @return MockInterface|LegacyMockInterface|ContainerInterface
     */
    protected function createContainerMock()
    {
        return Mockery::mock(ContainerInterface::class);
    }

    /**
     * Create EntityManager mock
     *
     * ひとまず仮のクラスで実装する。
     *
     * @return MockInterface|LegacyMockInterface
     */
    protected function createEntityManagerMock()
    {
        return Mockery::mock('EntityManager');
    }

    /**
     * Create SessionManager mock
     *
     * 実際のセッション（$_SESSION）は利用しない形にする。
     * ひとまず仮のクラスで実装する。
     *
     * @return MockInterface|LegacyMockInterface
     */
    protected function createSessionManagerMock()
    {
        return Mockery::mock('SessionManager');
    }

    /**
     * Create SessionContaier mock
     *
     * 実際のセッション（$_SESSION）は利用しない形にする。
     * 現状ではoffsetGet()、offsetSet()が利用できれば良いので、
     * 元になっているArrayObjectを利用する。
     *
     * @return MockInterface|LegacyMockInterface
     */
    protected function createSessionContaierMock()
    {
        return Mockery::mock(ArrayObject::class);
    }

    /**
     * Create AdminUser Repository mock
     *
     * ひとまず仮のクラスで実装する。
     *
     * @return MockInterface|LegacyMockInterface
     */
    protected function createAdminUserRepositoryMock()
    {
        return Mockery::mock('AdminUserRepository');
    }

    /**
     * Create AdminUser Repository mock
     *
     * @return MockInterface|LegacyMockInterface|AdminUser
     */
    protected function createAdminUserMock()
    {
        return Mockery::mock(AdminUser::class);
    }

    /**
     * @test
     */
    public function testConstruct(): void
    {
        $entityManagerMock    = $this->createEntityManagerMock();
        $sessionContainerMock = $this->createSessionContaierMock();

        $containerMock = $this->createContainerMockOfTestConstruct(
            $entityManagerMock,
            $sessionContainerMock
        );

        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authRef  = new ReflectionClass(Auth::class);

        // execute constructor
        $authConstructor = $authRef->getConstructor();
        $authConstructor->invoke($authMock, $containerMock);

        // test property "em"
        $emPropertyRef = $authRef->getProperty('em');
        $emPropertyRef->setAccessible(true);
        $this->assertEquals($entityManagerMock, $emPropertyRef->getValue($authMock));

        // test property "session"
        $sessionPropertyRef = $authRef->getProperty('session');
        $sessionPropertyRef->setAccessible(true);
        $this->assertEquals($sessionContainerMock, $sessionPropertyRef->getValue($authMock));
    }

    /**
     * @param mixed $entityMananger
     * @param mixed $sessionContainer
     */
    protected function createContainerMockOfTestConstruct($entityMananger, $sessionContainer): ContainerInterface
    {
        $mock = $this->createContainerMock();
        $mock
            ->shouldReceive('get')
            ->once()
            ->with('em')
            ->andReturn($entityMananger);

        $sessionManagerMock = $this->createSessionManagerMock();
        $sessionManagerMock
            ->shouldReceive('getContainer')
            ->once()
            ->with('auth')
            ->andReturn($sessionContainer);

        $mock
            ->shouldReceive('get')
            ->once()
            ->with('sm')
            ->andReturn($sessionManagerMock);

        return $mock;
    }

    /**
     * @test
     */
    public function testLoginInvalidUser(): void
    {
        $name     = 'username';
        $password = 'password';

        $inputName     = 'invalid';
        $inputPassword = $password;

        $repositoryMock = $this->createAdminUserRepositoryMock();
        $repositoryMock
            ->shouldReceive('findOneByName')
            ->once()
            ->with($inputName)
            ->andReturn(null);

        $entityManagerMock = $this->createEntityManagerMockOfTestLogin($repositoryMock);

        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authRef  = new ReflectionClass(Auth::class);

        $emPropertyRef = $authRef->getProperty('em');
        $emPropertyRef->setAccessible(true);
        $emPropertyRef->setValue($authMock, $entityManagerMock);

        // execute
        $this->assertFalse($authMock->login($inputName, $inputPassword));
    }

    /**
     * @test
     */
    public function testLoginInvalidPassword(): void
    {
        $name     = 'username';
        $password = 'password';

        $inputName     = $name;
        $inputPassword = 'invalid';

        $adminUserMock = $this->createAdminUserMock();
        $adminUserMock->makePartial();
        $adminUserMock->setPassword($password);

        $repositoryMock = $this->createAdminUserRepositoryMock();
        $repositoryMock
            ->shouldReceive('findOneByName')
            ->once()
            ->with($inputName)
            ->andReturn($adminUserMock);

        $entityManagerMock = $this->createEntityManagerMockOfTestLogin($repositoryMock);

        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authRef  = new ReflectionClass(Auth::class);

        $emPropertyRef = $authRef->getProperty('em');
        $emPropertyRef->setAccessible(true);
        $emPropertyRef->setValue($authMock, $entityManagerMock);

        // execute
        $this->assertFalse($authMock->login($inputName, $inputPassword));
    }

    /**
     * @test
     */
    public function testLoginValidUser(): void
    {
        $id       = 1;
        $name     = 'username';
        $password = 'password';

        $inputName     = $name;
        $inputPassword = $password;

        $adminUserMock = $this->createAdminUserMock();
        $adminUserMock->makePartial();
        $adminUserMock->setPassword($password);
        $adminUserMock
            ->shouldReceive('getId')
            ->once()
            ->with()
            ->andReturn($id);

        $repositoryMock = $this->createAdminUserRepositoryMock();
        $repositoryMock
            ->shouldReceive('findOneByName')
            ->once()
            ->with($inputName)
            ->andReturn($adminUserMock);

        $entityManagerMock = $this->createEntityManagerMockOfTestLogin($repositoryMock);

        $sessionContainerMock = $this->createSessionContaierMock();
        $sessionContainerMock
            ->shouldReceive('offsetSet')
            ->once()
            ->with('user_id', 1);

        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authRef  = new ReflectionClass(Auth::class);

        $emPropertyRef = $authRef->getProperty('em');
        $emPropertyRef->setAccessible(true);
        $emPropertyRef->setValue($authMock, $entityManagerMock);

        $sessionPropertyRef = $authRef->getProperty('session');
        $sessionPropertyRef->setAccessible(true);
        $sessionPropertyRef->setValue($authMock, $sessionContainerMock);

        // execute
        $this->assertTrue($authMock->login($inputName, $inputPassword));

        $userPropertyRef = $authRef->getProperty('user');
        $userPropertyRef->setAccessible(true);

        $this->assertEquals($adminUserMock, $userPropertyRef->getValue($authMock));
    }

    /**
     * Create EntityManager mock of testLogin
     *
     * @param mixed $repository
     * @return MockInterface|LegacyMockInterface
     */
    protected function createEntityManagerMockOfTestLogin($repository)
    {
        $mock = $this->createEntityManagerMock();
        $mock
            ->shouldReceive('getRepository')
            ->once()
            ->with(AdminUser::class)
            ->andReturn($repository);

        return $mock;
    }

    /**
     * @test
     */
    public function testLogout(): void
    {
        $sessionContainerMock = $this->createSessionContaierMock();
        $sessionContainerMock
            ->shouldReceive('offsetUnset')
            ->once()
            ->with('user_id');

        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authRef  = new ReflectionClass(Auth::class);

        // initialize property
        $userPropertyRef = $authRef->getProperty('user');
        $userPropertyRef->setAccessible(true);
        $userPropertyRef->setValue($authMock, 'user');

        $sessionPropertyRef = $authRef->getProperty('session');
        $sessionPropertyRef->setAccessible(true);
        $sessionPropertyRef->setValue($authMock, $sessionContainerMock);

        // execute
        $authMock->logout();

        $this->assertNull($userPropertyRef->getValue($authMock));
    }

    /**
     * @test
     */
    public function testIsAuthenticated(): void
    {
        $sessionContainerMock = $this->createSessionContaierMock();
        $sessionContainerMock->makePartial();

        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authRef  = new ReflectionClass(Auth::class);

        // initialize property
        $sessionPropertyRef = $authRef->getProperty('session');
        $sessionPropertyRef->setAccessible(true);
        $sessionPropertyRef->setValue($authMock, $sessionContainerMock);

        // execute
        $this->assertFalse($authMock->isAuthenticated());

        $sessionContainerMock['user_id'] = 1;

        // execute
        $this->assertTrue($authMock->isAuthenticated());
    }

    /**
     * @test
     */
    public function testGetUserIsNotAuthenticated(): void
    {
        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authMock
            ->shouldReceive('isAuthenticated')
            ->once()
            ->with()
            ->andReturn(false);

        $this->assertNull($authMock->getUser());
    }

    /**
     * @test
     */
    public function testGetUserIsAuthenticated(): void
    {
        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authMock
            ->shouldReceive('isAuthenticated')
            ->once()
            ->with()
            ->andReturn(true);

        $id = 1;

        $sessionContainerMock = $this->createSessionContaierMock();
        $sessionContainerMock->makePartial();
        $sessionContainerMock['user_id'] = $id;

        $adminUserMock = $this->createAdminUserMock();

        $repositoryMock = $this->createAdminUserRepositoryMock();
        $repositoryMock
            ->shouldReceive('findOneById')
            ->once()
            ->with($id)
            ->andReturn($adminUserMock);

        $entityManagerMock = $this->createEntityManagerMock();
        $entityManagerMock
            ->shouldReceive('getRepository')
            ->once()
            ->with(AdminUser::class)
            ->andReturn($repositoryMock);

        $authRef = new ReflectionClass(Auth::class);

        // initialize property
        $emPropertyRef = $authRef->getProperty('em');
        $emPropertyRef->setAccessible(true);
        $emPropertyRef->setValue($authMock, $entityManagerMock);

        $sessionPropertyRef = $authRef->getProperty('session');
        $sessionPropertyRef->setAccessible(true);
        $sessionPropertyRef->setValue($authMock, $sessionContainerMock);

        $userPropertyRef = $authRef->getProperty('user');
        $userPropertyRef->setAccessible(true);
        $userPropertyRef->setValue($authMock, null);

        // execute
        $this->assertEquals($adminUserMock, $authMock->getUser());
    }

    /**
     * @test
     */
    public function testGetUserLoadedUser(): void
    {
        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authMock
            ->shouldReceive('isAuthenticated')
            ->once()
            ->with()
            ->andReturn(true);

        $adminUserMock = $this->createAdminUserMock();

        $entityManagerMock = $this->createEntityManagerMock();
        $entityManagerMock
            ->shouldReceive('getRepository')
            ->never();

        $authRef = new ReflectionClass(Auth::class);

        // initialize property
        $emPropertyRef = $authRef->getProperty('em');
        $emPropertyRef->setAccessible(true);
        $emPropertyRef->setValue($authMock, $entityManagerMock);

        $userPropertyRef = $authRef->getProperty('user');
        $userPropertyRef->setAccessible(true);
        $userPropertyRef->setValue($authMock, $adminUserMock);

        // execute
        $this->assertEquals($adminUserMock, $authMock->getUser());
    }
}
