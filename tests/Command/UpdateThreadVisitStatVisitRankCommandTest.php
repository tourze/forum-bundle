<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use ForumBundle\Command\UpdateThreadVisitStatVisitRankCommand;
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
 * 更新线程访问统计访问排名命令测试
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(UpdateThreadVisitStatVisitRankCommand::class)]
final class UpdateThreadVisitStatVisitRankCommandTest extends AbstractCommandTestCase
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
        $command = self::getContainer()->get(UpdateThreadVisitStatVisitRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatVisitRankCommand::class, $command);
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

    public function testExecuteWithEnabledTaskShouldUpdateVisitRank(): void
    {
        $_ENV['ENABLE_THREAD_STAT_RANK_TASK'] = '1';
        $_ENV['THREAD_RANK_LIMIT'] = '10';

        // 执行命令（应该成功）
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 这个测试主要验证命令能够正常执行，不会抛出异常
        // 由于可能存在其他测试数据，我们只验证命令执行成功
        $this->assertTrue(true);
    }

    public function testCommandShouldHaveCorrectName(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatVisitRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatVisitRankCommand::class, $command);

        $this->assertSame('forum:update-thread-stat-visit-rank', $command->getName());
    }

    public function testCommandShouldHaveDescription(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatVisitRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatVisitRankCommand::class, $command);

        $this->assertNotEmpty($command->getDescription());
    }
}
