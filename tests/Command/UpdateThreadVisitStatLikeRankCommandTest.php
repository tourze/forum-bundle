<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use ForumBundle\Command\UpdateThreadVisitStatLikeRankCommand;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\VisitStat;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use ForumBundle\Repository\VisitStatRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 更新线程访问统计点赞排名命令测试
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(UpdateThreadVisitStatLikeRankCommand::class)]
final class UpdateThreadVisitStatLikeRankCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    private VisitStatRepository $visitStatRepository;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        $this->visitStatRepository = self::getContainer()->get(VisitStatRepository::class);

        $application = new Application();
        $command = self::getContainer()->get(UpdateThreadVisitStatLikeRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatLikeRankCommand::class, $command);
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

    public function testExecuteWithDisabledTaskShouldReturnSuccess(): void
    {
        $_ENV['ENABLE_THREAD_STAT_RANK_TASK'] = '0';

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithEnabledTaskShouldUpdateLikeRank(): void
    {
        $_ENV['ENABLE_THREAD_STAT_RANK_TASK'] = '1';
        $_ENV['THREAD_RANK_LIMIT'] = '10';

        // 创建一个测试帖子和对应的访问统计
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_PASS);

        $visitStat = new VisitStat();
        $visitStat->setThread($thread);
        $visitStat->setLikeTotal(100); // 设置一个较高的点赞数

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->persist($visitStat);
        self::getEntityManager()->flush();

        // 验证初始排名是 0
        $this->assertSame(0, $visitStat->getLikeRank());

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 刷新实体以获取最新状态
        self::getEntityManager()->refresh($visitStat);

        // 验证排名已更新（应该大于0，因为我们设置了较高的点赞数）
        $this->assertGreaterThan(0, $visitStat->getLikeRank());
    }

    public function testExecuteWithExistingRanksShouldResetZeroRanks(): void
    {
        $_ENV['ENABLE_THREAD_STAT_RANK_TASK'] = '1';
        $_ENV['THREAD_RANK_LIMIT'] = '1';

        // 创建一个访问统计，点赞数很低
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_PASS);

        $visitStat = new VisitStat();
        $visitStat->setThread($thread);
        $visitStat->setLikeTotal(1); // 很少的点赞数
        $visitStat->setLikeRank(5); // 有初始排名

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->persist($visitStat);
        self::getEntityManager()->flush();

        // 验证初始排名
        $this->assertSame(5, $visitStat->getLikeRank());

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 刷新实体以获取最新状态
        self::getEntityManager()->refresh($visitStat);

        // 由于点赞数很低，可能不会被排进前列，但排名应该被重新计算
        // 我们只验证排名确实被重新设置了（不是原来的5）
        $this->assertNotSame(5, $visitStat->getLikeRank());
    }

    public function testCommandShouldHaveCorrectName(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatLikeRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatLikeRankCommand::class, $command);

        $this->assertSame('forum:update-thread-stat-like-rank', $command->getName());
    }

    public function testCommandShouldHaveDescription(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatLikeRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatLikeRankCommand::class, $command);

        $this->assertNotEmpty($command->getDescription());
    }
}
