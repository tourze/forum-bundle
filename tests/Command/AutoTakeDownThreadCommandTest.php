<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use Carbon\CarbonImmutable;
use ForumBundle\Command\AutoTakeDownThreadCommand;
use ForumBundle\Entity\Thread;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use ForumBundle\Repository\ThreadRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 自动下架主题命令测试
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(AutoTakeDownThreadCommand::class)]
final class AutoTakeDownThreadCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    private ThreadRepository $threadRepository;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        $this->threadRepository = self::getContainer()->get(ThreadRepository::class);

        $application = new Application();
        $command = self::getContainer()->get(AutoTakeDownThreadCommand::class);
        $this->assertInstanceOf(AutoTakeDownThreadCommand::class, $command);
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

    public function testExecuteWithNoThreadsShouldReturnSuccessCode(): void
    {
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithExpiredTakeDownTimeShouldUpdateThreadStatus(): void
    {
        // 创建一个测试帖子，状态为审核通过，设置了自动下架时间（已过期）
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_PASS);
        $thread->setOfficial(true);
        $thread->setAutoTakeDownTime(CarbonImmutable::now()->subMinutes(5)); // 5分钟前

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        // 确保初始状态是审核通过
        $this->assertSame(ThreadState::AUDIT_PASS, $thread->getStatus());

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 刷新实体以获取最新状态
        self::getEntityManager()->refresh($thread);

        // 验证状态已更新为审核拒绝
        $this->assertSame(ThreadState::AUDIT_REJECT, $thread->getStatus());
    }

    public function testExecuteWithFutureTakeDownTimeShouldNotUpdateThread(): void
    {
        // 创建一个测试帖子，状态为审核通过，但自动下架时间还未到
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_PASS);
        $thread->setOfficial(true);
        $thread->setAutoTakeDownTime(CarbonImmutable::now()->addMinutes(5)); // 5分钟后

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        // 确保初始状态
        $this->assertSame(ThreadState::AUDIT_PASS, $thread->getStatus());

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 刷新实体以获取最新状态
        self::getEntityManager()->refresh($thread);

        // 验证状态仍然是审核通过（因为下架时间还未到）
        $this->assertSame(ThreadState::AUDIT_PASS, $thread->getStatus());
    }

    public function testExecuteWithNonOfficialThreadShouldNotUpdateThread(): void
    {
        // 创建一个非官方帖子，状态为审核通过，自动下架时间已过期
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_PASS);
        $thread->setOfficial(false); // 非官方帖子
        $thread->setAutoTakeDownTime(CarbonImmutable::now()->subMinutes(5)); // 5分钟前

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        // 确保初始状态
        $this->assertSame(ThreadState::AUDIT_PASS, $thread->getStatus());

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 刷新实体以获取最新状态
        self::getEntityManager()->refresh($thread);

        // 验证状态仍然是审核通过（因为不是官方帖子）
        $this->assertSame(ThreadState::AUDIT_PASS, $thread->getStatus());
    }

    public function testCommandShouldHaveCorrectName(): void
    {
        $command = self::getContainer()->get(AutoTakeDownThreadCommand::class);
        $this->assertInstanceOf(AutoTakeDownThreadCommand::class, $command);

        $this->assertSame('forum:auto-take-down-thread', $command->getName());
    }

    public function testCommandShouldHaveDescription(): void
    {
        $command = self::getContainer()->get(AutoTakeDownThreadCommand::class);
        $this->assertInstanceOf(AutoTakeDownThreadCommand::class, $command);

        $this->assertNotEmpty($command->getDescription());
    }
}
