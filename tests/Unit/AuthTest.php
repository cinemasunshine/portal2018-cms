<?php

/**
 * AuthTest.php
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Auth;
use App\ORM\Entity\AdminUser;
use App\Session\SessionManager;
use Doctrine\ORM\EntityManager;
use Laminas\Session\Config\StandardConfig;
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

    private SessionManager $sessionManager;

    protected function setUp(): void
    {
        $sessionConfig = new StandardConfig();
        $sessionConfig->setOptions(['name' => 'test']);
        $this->sessionManager = new SessionManager($sessionConfig);
    }

    protected function tearDown(): void
    {
        $this->sessionManager->getStorage()->clear();
    }

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
     * @return MockInterface|LegacyMockInterface|EntityManager
     */
    protected function createEntityManagerMock()
    {
        return Mockery::mock(EntityManager::class);
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
        $entityManagerMock = $this->createEntityManagerMock();
        $sessionContainer  = $this->sessionManager->getContainer();

        $authMock = Mockery::mock(Auth::class)->makePartial();
        $authRef  = new ReflectionClass(Auth::class);

        // execute constructor
        $authConstructor = $authRef->getConstructor();
        $authConstructor->invoke($authMock, $entityManagerMock, $sessionContainer);

        // test property "em"
        $emPropertyRef = $authRef->getProperty('em');
        $emPropertyRef->setAccessible(true);
        $this->assertEquals($entityManagerMock, $emPropertyRef->getValue($authMock));

        // test property "session"
        $sessionPropertyRef = $authRef->getProperty('session');
        $sessionPropertyRef->setAccessible(true);
        $this->assertEquals($sessionContainer, $sessionPropertyRef->getValue($authMock));
    }

    /**
     * @test
     */
    public function testLoginInvalidUser(): void
    {
        $inputName = 'not_found_user';

        $repositoryMock = $this->createAdminUserRepositoryMock();
        $repositoryMock
            ->shouldReceive('findOneByName')
            ->once()
            ->with($inputName)
            ->andReturn(null);

        $entityManagerMock = $this->createEntityManagerMockOfTestLogin($repositoryMock);

        $auth = new Auth($entityManagerMock, $this->sessionManager->getContainer());

        // execute
        $this->assertFalse($auth->login($inputName, 'password'));
    }

    /**
     * @test
     */
    public function testLoginInvalidPassword(): void
    {
        $inputName     = 'username';
        $inputPassword = 'invalid_password';

        $adminUserMock = $this->createAdminUserMock();
        $adminUserMock->makePartial();
        $adminUserMock->setPassword('valid_password');

        $repositoryMock = $this->createAdminUserRepositoryMock();
        $repositoryMock
            ->shouldReceive('findOneByName')
            ->once()
            ->with($inputName)
            ->andReturn($adminUserMock);

        $entityManagerMock = $this->createEntityManagerMockOfTestLogin($repositoryMock);

        $auth = new Auth($entityManagerMock, $this->sessionManager->getContainer());

        $this->assertFalse($auth->login($inputName, $inputPassword));
    }

    /**
     * @test
     */
    public function testLoginValidUser(): void
    {
        $userId   = 1;
        $name     = 'username';
        $password = 'password';

        $adminUserMock = $this->createAdminUserMock();
        $adminUserMock->makePartial();
        $adminUserMock->setPassword($password);
        $adminUserMock
            ->shouldReceive('getId')
            ->once()
            ->with()
            ->andReturn($userId);

        $repositoryMock = $this->createAdminUserRepositoryMock();
        $repositoryMock
            ->shouldReceive('findOneByName')
            ->once()
            ->with($name)
            ->andReturn($adminUserMock);

        $entityManagerMock = $this->createEntityManagerMockOfTestLogin($repositoryMock);

        $sessionContainer = $this->sessionManager->getContainer();
        $auth             = new Auth($entityManagerMock, $sessionContainer);

        // execute
        $this->assertTrue($auth->login($name, $password));

        $authRef         = new ReflectionClass(Auth::class);
        $userPropertyRef = $authRef->getProperty('user');
        $userPropertyRef->setAccessible(true);

        $this->assertSame($adminUserMock, $userPropertyRef->getValue($auth));
        $this->assertSame($userId, $sessionContainer['user_id']);
    }

    /**
     * Create EntityManager mock of testLogin
     *
     * @param mixed $repository
     * @return MockInterface|LegacyMockInterface|EntityManager
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
        $sessionContainer            = $this->sessionManager->getContainer();
        $sessionContainer['user_id'] = 1;

        $auth    = new Auth($this->createEntityManagerMock(), $sessionContainer);
        $authRef = new ReflectionClass(Auth::class);

        $adminUserMock = $this->createAdminUserMock();

        // initialize property
        $userPropertyRef = $authRef->getProperty('user');
        $userPropertyRef->setAccessible(true);
        $userPropertyRef->setValue($auth, $adminUserMock);

        // execute
        $auth->logout();

        $this->assertNull($userPropertyRef->getValue($auth));
        $this->assertArrayNotHasKey('user_id', $sessionContainer);
    }

    /**
     * @test
     */
    public function testIsAuthenticated(): void
    {
        $sessionContainer = $this->sessionManager->getContainer();

        $auth = new Auth($this->createEntityManagerMock(), $sessionContainer);

        // execute
        $this->assertFalse($auth->isAuthenticated());

        $sessionContainer['user_id'] = 1;

        // execute
        $this->assertTrue($auth->isAuthenticated());
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

        $sessionContainer            = $this->sessionManager->getContainer();
        $sessionContainer['user_id'] = $id;

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
        $sessionPropertyRef->setValue($authMock, $sessionContainer);

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
