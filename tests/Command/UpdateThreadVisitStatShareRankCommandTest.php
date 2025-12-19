<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use ForumBundle\Command\UpdateThreadVisitStatShareRankCommand;
use ForumBundle\Repository\VisitStatRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 更新线程访问统计分享排名命令测试
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(UpdateThreadVisitStatShareRankCommand::class)]
final class UpdateThreadVisitStatShareRankCommandTest extends AbstractCommandTestCase
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
        $command = self::getContainer()->get(UpdateThreadVisitStatShareRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatShareRankCommand::class, $command);
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

    public function testExecuteWithEnabledTaskShouldReturnSuccess(): void
    {
        $_ENV['ENABLE_THREAD_STAT_RANK_TASK'] = '1';
        $_ENV['THREAD_RANK_LIMIT'] = '10';

        // 执行命令（应该成功）
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testCommandShouldHaveCorrectName(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatShareRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatShareRankCommand::class, $command);

        $this->assertSame('forum:update-thread-stat-share-rank', $command->getName());
    }

    public function testCommandShouldHaveDescription(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatShareRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatShareRankCommand::class, $command);

        $this->assertNotEmpty($command->getDescription());
    }
}
