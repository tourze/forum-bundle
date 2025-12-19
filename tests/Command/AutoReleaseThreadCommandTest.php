<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use Carbon\CarbonImmutable;
use ForumBundle\Command\AutoReleaseThreadCommand;
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
 * 自动发布主题命令测试
 *
 * @internal
 */
#[CoversClass(AutoReleaseThreadCommand::class)]
#[RunTestsInSeparateProcesses]
final class AutoReleaseThreadCommandTest extends AbstractCommandTestCase
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
        $command = self::getContainer()->get(AutoReleaseThreadCommand::class);
        $this->assertInstanceOf(AutoReleaseThreadCommand::class, $command);
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

    public function testExecuteWithExpiredReleaseTimeShouldUpdateThreadStatus(): void
    {
        // 创建一个测试帖子，状态为审核拒绝，设置了自动发布时间（已过期）
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_REJECT);
        $thread->setOfficial(true);
        $thread->setAutoReleaseTime(CarbonImmutable::now()->subMinutes(5)); // 5分钟前
        $thread->setAutoTakeDownTime(CarbonImmutable::now()->addMinutes(10)); // 10分钟后

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        // 确保初始状态是审核拒绝
        $this->assertSame(ThreadState::AUDIT_REJECT, $thread->getStatus());

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 刷新实体以获取最新状态
        self::getEntityManager()->refresh($thread);

        // 验证状态已更新为审核通过
        $this->assertSame(ThreadState::AUDIT_PASS, $thread->getStatus());
    }

    public function testExecuteWithExpiredTakeDownTimeShouldNotReleaseThread(): void
    {
        // 创建一个测试帖子，状态为审核拒绝，自动发布时间已过期，但自动下架时间也已过期
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_REJECT);
        $thread->setOfficial(true);
        $thread->setAutoReleaseTime(CarbonImmutable::now()->subMinutes(5)); // 5分钟前
        $thread->setAutoTakeDownTime(CarbonImmutable::now()->subMinutes(1)); // 1分钟前（已过期）

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        // 确保初始状态
        $this->assertSame(ThreadState::AUDIT_REJECT, $thread->getStatus());

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 刷新实体以获取最新状态
        self::getEntityManager()->refresh($thread);

        // 验证状态仍然是审核拒绝（因为下架时间也过了）
        $this->assertSame(ThreadState::AUDIT_REJECT, $thread->getStatus());
    }

    public function testExecuteWithFutureReleaseTimeShouldNotUpdateThread(): void
    {
        // 创建一个测试帖子，状态为审核拒绝，但自动发布时间还未到
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_REJECT);
        $thread->setOfficial(true);
        $thread->setAutoReleaseTime(CarbonImmutable::now()->addMinutes(5)); // 5分钟后

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        // 确保初始状态
        $this->assertSame(ThreadState::AUDIT_REJECT, $thread->getStatus());

        // 执行命令
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());

        // 刷新实体以获取最新状态
        self::getEntityManager()->refresh($thread);

        // 验证状态仍然是审核拒绝（因为发布时间还未到）
        $this->assertSame(ThreadState::AUDIT_REJECT, $thread->getStatus());
    }

    public function testCommandShouldHaveCorrectName(): void
    {
        $command = self::getContainer()->get(AutoReleaseThreadCommand::class);
        $this->assertInstanceOf(AutoReleaseThreadCommand::class, $command);

        $this->assertSame('forum:auto-release-thread', $command->getName());
    }

    public function testCommandShouldHaveDescription(): void
    {
        $command = self::getContainer()->get(AutoReleaseThreadCommand::class);
        $this->assertInstanceOf(AutoReleaseThreadCommand::class, $command);

        $this->assertNotEmpty($command->getDescription());
    }
}
