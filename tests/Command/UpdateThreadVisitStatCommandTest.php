<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use ForumBundle\Command\UpdateThreadVisitStatCommand;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\VisitStat;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use ForumBundle\Repository\ThreadRepository;
use ForumBundle\Service\ThreadStatisticsFacade;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 更新线程访问统计命令测试
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(UpdateThreadVisitStatCommand::class)]
final class UpdateThreadVisitStatCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    private ThreadRepository $threadRepository;

    /** @var ThreadStatisticsFacade&MockObject */
    private ThreadStatisticsFacade $threadStatisticsFacade;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        $this->threadRepository = self::getContainer()->get(ThreadRepository::class);

        // Mock ThreadStatisticsFacade 具体类的原因：
        // 1. 这是一个业务门面类，没有定义相应的接口
        // 2. 测试需要隔离复杂的统计逻辑实现，只关注当前测试的逻辑
        // 3. 使用具体类Mock是集成复杂业务服务的标准做法
        $this->threadStatisticsFacade = $this->createMock(ThreadStatisticsFacade::class);

        // 将Mock服务注入到容器中
        $container = self::getContainer();
        $container->set(ThreadStatisticsFacade::class, $this->threadStatisticsFacade);

        $application = new Application();
        $command = self::getContainer()->get(UpdateThreadVisitStatCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatCommand::class, $command);
        $application->addCommand($command);

        $this->commandTester = new CommandTester($command);

        // 开始事务，确保测试隔离
        self::getEntityManager()->beginTransaction();
    }

    protected function onTearDown(): void
    {
        // 回滚事务，清理测试数据
        if (self::getEntityManager()->getConnection()->isTransactionActive()) {
            self::getEntityManager()->rollback();
        }
        parent::onTearDown();
    }

    public function testExecuteWithNoThreadsShouldReturnSuccess(): void
    {
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithThreadsWithoutVisitStatShouldUpdateStats(): void
    {
        // 创建一个测试帖子，没有访问统计（visitStat 为 NULL）
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_PASS);

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        // 确保帖子没有访问统计
        $this->assertNull($thread->getVisitStat());

        // 期望统计门面被调用
        $this->threadStatisticsFacade->expects($this->once())
            ->method('updateAllStat')
            ->with($thread)
        ;

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithThreadsWithVisitStatShouldNotUpdateStats(): void
    {
        // 创建一个测试帖子，已经有访问统计
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_PASS);

        // 创建访问统计并关联到帖子
        $visitStat = new VisitStat();
        $visitStat->setThread($thread);
        $thread->setVisitStat($visitStat);

        self::getEntityManager()->persist($visitStat);
        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        // 确保帖子有访问统计
        $this->assertNotNull($thread->getVisitStat());

        // 期望统计门面不被调用
        $this->threadStatisticsFacade->expects($this->never())
            ->method('updateAllStat')
        ;

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithMultipleThreadsShouldUpdateStats(): void
    {
        // 创建多个测试帖子，都没有访问统计
        $thread1 = new Thread();
        $thread1->setTitle('Test Thread 1');
        $thread1->setContent('Test content 1');
        $thread1->setType(ThreadType::USER_THREAD);
        $thread1->setStatus(ThreadState::AUDIT_PASS);

        $thread2 = new Thread();
        $thread2->setTitle('Test Thread 2');
        $thread2->setContent('Test content 2');
        $thread2->setType(ThreadType::USER_THREAD);
        $thread2->setStatus(ThreadState::AUDIT_PASS);

        self::getEntityManager()->persist($thread1);
        self::getEntityManager()->persist($thread2);
        self::getEntityManager()->flush();

        // 确保帖子没有访问统计
        $this->assertNull($thread1->getVisitStat());
        $this->assertNull($thread2->getVisitStat());

        // 期望统计门面被调用两次
        $invocationMatcher = $this->exactly(2);
        $this->threadStatisticsFacade->expects($invocationMatcher)
            ->method('updateAllStat')
            ->willReturnCallback(function ($thread) use ($thread1, $thread2) {
                static $callCount = 0;
                ++$callCount;
                if (1 === $callCount) {
                    $this->assertSame($thread1, $thread);
                } elseif (2 === $callCount) {
                    $this->assertSame($thread2, $thread);
                }
            })
        ;

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testCommandShouldHaveCorrectName(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatCommand::class, $command);

        $this->assertSame('forum:update-thread-visit-stat', $command->getName());
    }

    public function testCommandShouldHaveDescription(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatCommand::class, $command);

        $this->assertNotEmpty($command->getDescription());
    }
}
