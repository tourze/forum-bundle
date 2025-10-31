<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\VisitStat;
use ForumBundle\Repository\ForumShareRecordRepository;
use ForumBundle\Repository\ThreadCollectRepository;
use ForumBundle\Repository\ThreadCommentRepository;
use ForumBundle\Repository\ThreadLikeRepository;
use ForumBundle\Repository\VisitStatRepository;
use ForumBundle\Service\StatService;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(StatService::class)]
#[RunTestsInSeparateProcesses] final class StatServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getStatService(
        ?ThreadCommentRepository $threadCommentRepository = null,
        ?ThreadCollectRepository $threadCollectRepository = null,
        ?ThreadLikeRepository $threadLikeRepository = null,
        ?ForumShareRecordRepository $forumShareRecordRepository = null,
        ?VisitStatRepository $visitStatRepository = null,
        ?EntityLockService $entityLockService = null,
        ?EntityManagerInterface $entityManager = null,
    ): StatService {
        // 在需要Mock依赖时，应在容器初始化前设置Mock服务
        if (null !== $threadCommentRepository) {
            self::getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        }
        if (null !== $threadCollectRepository) {
            self::getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        }
        if (null !== $threadLikeRepository) {
            self::getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        }
        if (null !== $forumShareRecordRepository) {
            self::getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        }
        if (null !== $visitStatRepository) {
            self::getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        }
        if (null !== $entityLockService) {
            self::getContainer()->set(EntityLockService::class, $entityLockService);
        }
        if (null !== $entityManager) {
            self::getContainer()->set(EntityManagerInterface::class, $entityManager);
        }

        // 从容器中获取服务实例（符合集成测试的最佳实践）
        return self::getService(StatService::class);
    }

    public function testConstructorAcceptsRequiredDependencies(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $this->assertInstanceOf(StatService::class, $service);
    }

    public function testUpdateCommentTotalUpdatesStatCorrectly(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如count），这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $thread = new Thread();
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);

        $threadCommentRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(5)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->updateCommentTotal($visitStat);

        $this->assertEquals(5, $visitStat->getCommentTotal());
    }

    public function testUpdateCollectCountUpdatesStatCorrectly(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如count），这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如count），这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $thread = new Thread();
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);

        $threadCollectRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(3)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->updateCollectCount($visitStat);

        $this->assertEquals(3, $visitStat->getCollectCount());
    }

    public function testAsyncUpdateCollectCountCallsUpdateCollectCount(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $thread = new Thread();
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);

        $threadCollectRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(5)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->asyncUpdateCollectCount($visitStat);

        $this->assertEquals(5, $visitStat->getCollectCount());
    }

    public function testAsyncUpdateCommentTotalCallsUpdateCommentTotal(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $thread = new Thread();
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);

        $threadCommentRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(10)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->asyncUpdateCommentTotal($visitStat);

        $this->assertEquals(10, $visitStat->getCommentTotal());
    }

    public function testUpdateAllStatisticsUpdatesAllStats(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        /*
         * Mock Thread 具体类的原因：
         * 1. Thread是一个Doctrine实体类，没有对应的接口
         * 2. 测试需要Mock实体的特定方法（如getId），这些方法定义在具体实体类中
         * 3. 在单元测试中Mock实体类是标准做法，可以隔离数据库依赖
         */
        $thread = $this->createMock(Thread::class);
        $thread->method('getId')->willReturn('123');

        $threadComments = $this->createMock(Collection::class);
        $threadComments->method('count')->willReturn(8);
        $thread->method('getThreadComments')->willReturn($threadComments);

        $visitStat = new VisitStat();
        $visitStat->setVisitTotal(100);

        $visitStatRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['thread' => $thread])
            ->willReturn($visitStat)
        ;

        $threadLikeRepository
            ->expects($this->once())
            ->method('count')
            ->with(['thread' => $thread, 'status' => 1])
            ->willReturn(15)
        ;

        $forumShareRecordRepository
            ->expects($this->once())
            ->method('count')
            ->with(['type' => 'thread', 'sourceId' => '123'])
            ->willReturn(20)
        ;

        $threadCollectRepository
            ->expects($this->once())
            ->method('count')
            ->with(['thread' => $thread, 'valid' => true])
            ->willReturn(25)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->updateAllStatistics($thread);

        $this->assertEquals(101, $visitStat->getVisitTotal());
        $this->assertEquals(8, $visitStat->getCommentTotal());
        $this->assertEquals(15, $visitStat->getLikeTotal());
        $this->assertEquals(20, $visitStat->getShareTotal());
        $this->assertEquals(25, $visitStat->getCollectCount());
    }

    public function testUpdateCollectStatisticsCallsUpdateCollectCountSync(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $thread = new Thread();
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);
        $visitStat->setCommentTotal(100);

        $visitStatRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['thread' => $thread])
            ->willReturn($visitStat)
        ;

        $threadCollectRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(7)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->updateCollectStatistics($thread);

        $this->assertEquals(7, $visitStat->getCollectCount());
    }

    public function testUpdateCommentStatisticsCallsUpdateCommentTotalSync(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $thread = new Thread();
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);
        $visitStat->setCommentTotal(50);

        $visitStatRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['thread' => $thread])
            ->willReturn($visitStat)
        ;

        $threadCommentRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(12)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->updateCommentStatistics($thread);

        $this->assertEquals(12, $visitStat->getCommentTotal());
    }

    public function testUpdateLikeStatisticsUpdatesLikeTotal(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $thread = new Thread();
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);

        $visitStatRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['thread' => $thread])
            ->willReturn($visitStat)
        ;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        $threadLikeRepository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('l')
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->expects($this->once())
            ->method('select')
            ->with('COUNT(l.id)')
        ;

        $queryBuilder
            ->expects($this->once())
            ->method('where')
            ->with('l.thread = :thread')
        ;

        $queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with('l.status = :status')
        ;

        $queryBuilder
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnMap([
                ['thread', $thread, null, $queryBuilder],
                ['status', 1, null, $queryBuilder],
            ])
        ;

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $query
            ->expects($this->once())
            ->method('getSingleScalarResult')
            ->willReturn(30)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->updateLikeStatistics($thread);

        $this->assertEquals(30, $visitStat->getLikeTotal());
    }

    public function testUpdateShareStatisticsUpdatesShareTotal(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        /*
         * Mock Thread 具体类的原因：
         * 1. Thread是一个Doctrine实体类，没有对应的接口
         * 2. 测试需要Mock实体的特定方法（如getId），这些方法定义在具体实体类中
         * 3. 在单元测试中Mock实体类是标准做法，可以隔离数据库依赖
         */
        $thread = $this->createMock(Thread::class);
        $thread->method('getId')->willReturn('456');

        $visitStat = new VisitStat();
        $visitStat->setThread($thread);

        $visitStatRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['thread' => $thread])
            ->willReturn($visitStat)
        ;

        $forumShareRecordRepository
            ->expects($this->once())
            ->method('count')
            ->with(['type' => 'thread', 'sourceId' => '456'])
            ->willReturn(18)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $entityLockService
            ->expects($this->once())
            ->method('lockEntity')
            ->with($thread, Assert::callback(function ($value) {
                return is_callable($value);
            }))
            ->willReturnCallback(function ($thread, $callback): void {
                self::assertIsCallable($callback);
                call_user_func($callback);
            })
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->updateShareStatistics($thread);

        $this->assertEquals(18, $visitStat->getShareTotal());
    }

    public function testUpdateVisitStatisticsIncrementsVisitTotal(): void
    {
        /*
         * Mock ThreadCommentRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCommentRepository = $this->createMock(ThreadCommentRepository::class);
        /*
         * Mock ThreadCollectRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadCollectRepository = $this->createMock(ThreadCollectRepository::class);
        /*
         * Mock ThreadLikeRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        /*
         * Mock ForumShareRecordRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $forumShareRecordRepository = $this->createMock(ForumShareRecordRepository::class);
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法，这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $visitStatRepository = $this->createMock(VisitStatRepository::class);
        /*
         * Mock EntityLockService 具体类的原因：
         * 1. 这是一个第三方Bundle的具体服务类，没有提供接口
         * 2. 测试需要隔离该服务的具体实现，只关注业务逻辑
         * 3. 使用具体类Mock是集成第三方服务的标准做法
         */
        $entityLockService = $this->createMock(EntityLockService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $thread = new Thread();
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);
        $visitStat->setVisitTotal(50);

        $visitStatRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['thread' => $thread])
            ->willReturn($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($visitStat)
        ;

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        // 设置 mock 服务到容器
        // $this->getContainer()->set(ThreadCommentRepository::class, $threadCommentRepository);
        //         $this->getContainer()->set(ThreadCollectRepository::class, $threadCollectRepository);
        //         $this->getContainer()->set(ThreadLikeRepository::class, $threadLikeRepository);
        //         $this->getContainer()->set(ForumShareRecordRepository::class, $forumShareRecordRepository);
        //         $this->getContainer()->set(VisitStatRepository::class, $visitStatRepository);
        //         $this->getContainer()->set(EntityLockService::class, $entityLockService);
        //         $this->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getStatService(
            $threadCommentRepository,
            $threadCollectRepository,
            $threadLikeRepository,
            $forumShareRecordRepository,
            $visitStatRepository,
            $entityLockService,
            $entityManager
        );

        $service->updateVisitStatistics($thread);

        $this->assertEquals(51, $visitStat->getVisitTotal());
    }
}
