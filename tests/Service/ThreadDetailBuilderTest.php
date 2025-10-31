<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Repository\ThreadCollectRepository;
use ForumBundle\Repository\ThreadLikeRepository;
use ForumBundle\Service\ThreadDetailBuilder;
use ForumBundle\Vo\ThreadDetail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\UserFollowBundle\Service\FollowService;

/**
 * @internal
 */
#[CoversClass(ThreadDetailBuilder::class)]
#[RunTestsInSeparateProcesses] final class ThreadDetailBuilderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getThreadDetailBuilder(
        ?FollowService $followService = null,
        ?ThreadLikeRepository $threadLikeRepository = null,
        ?ThreadCollectRepository $threadCollectRepository = null,
        ?EventDispatcherInterface $eventDispatcher = null,
    ): ThreadDetailBuilder {
        // 在需要Mock依赖时，应在容器初始化前设置Mock服务
        if (null !== $followService) {
            self::getContainer()->set(FollowService::class, $followService);
        }
        if (null !== $threadLikeRepository) {
            self::getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        }
        if (null !== $threadCollectRepository) {
            self::getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        }
        if (null !== $eventDispatcher) {
            self::getContainer()->set(EventDispatcherInterface::class, $eventDispatcher);
        }

        // 从容器中获取服务实例（符合集成测试的最佳实践）
        return self::getService(ThreadDetailBuilder::class);
    }

    public function testConstructorAcceptsRequiredDependencies(): void
    {
        /*
         * Mock FollowService 服务类的原因：
         * 1. 重构后使用Service层替代直接调用Repository
         * 2. Service层封装了业务逻辑，提供清晰的接口
         * 3. 符合模块化架构原则，避免跨模块Repository调用
         */
        $followService = $this->createMock(FollowService::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $builder = $this->getThreadDetailBuilder(
            $followService,
            $threadLikeRepository,
            $threadCollectRepository,
            $eventDispatcher
        );

        $this->assertInstanceOf(ThreadDetailBuilder::class, $builder);
    }

    public function testBuildThreadDetailReturnsThreadDetailInstance(): void
    {
        /*
         * Mock FollowService 服务类的原因：
         * 1. 重构后使用Service层替代直接调用Repository
         * 2. Service层封装了业务逻辑，提供清晰的接口
         * 3. 符合模块化架构原则，避免跨模块Repository调用
         */
        $followService = $this->createMock(FollowService::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $thread = new Thread();
        $thread->setTitle('Test Thread');
        // 设置thread的user为null，避免setFollowInfo调用isFollowing
        $thread->setUser(null);
        $thread->setContent('Test Content');

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $builder = $this->getThreadDetailBuilder(
            $followService,
            $threadLikeRepository,
            $threadCollectRepository,
            $eventDispatcher
        );

        $result = $builder->buildThreadDetail($thread, null);

        $this->assertInstanceOf(ThreadDetail::class, $result);
        $this->assertEquals('Test Thread', $result->getTitle());
        $this->assertEquals('Test Content', $result->getContent());
    }

    public function testBuildThreadDetailWithUserSetsUserInteractionInfo(): void
    {
        /*
         * Mock FollowService 服务类的原因：
         * 1. 重构后使用Service层替代直接调用Repository
         * 2. Service层封装了业务逻辑，提供清晰的接口
         * 3. 符合模块化架构原则，避免跨模块Repository调用
         */
        $followService = $this->createMock(FollowService::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如findOneBy），这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如findOneBy），这些方法定义在具体Repository实现中
         * 3. 使用具体类是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test-user');

        $threadUser = $this->createMock(UserInterface::class);
        $threadUser->method('getUserIdentifier')->willReturn('thread-user');

        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setUser($threadUser);

        $followService
            ->expects($this->once())
            ->method('isFollowing')
            ->with($user, $threadUser)
            ->willReturn(false)
        ;

        $threadLikeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $threadCollectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $builder = $this->getThreadDetailBuilder(
            $followService,
            $threadLikeRepository,
            $threadCollectRepository,
            $eventDispatcher
        );

        $result = $builder->buildThreadDetail($thread, $user);

        $this->assertInstanceOf(ThreadDetail::class, $result);
        $this->assertFalse($result->getLike());
        $this->assertFalse($result->getCollect());
        $this->assertFalse($result->isFollow()); // 验证重构后的关注状态
    }
}
