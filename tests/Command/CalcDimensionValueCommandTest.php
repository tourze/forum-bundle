<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use ForumBundle\Command\CalcDimensionValueCommand;
use ForumBundle\Repository\DimensionRepository;
use ForumBundle\Repository\ThreadRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 计算维度值命令测试
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(CalcDimensionValueCommand::class)]
final class CalcDimensionValueCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    private DimensionRepository $dimensionRepository;

    private ThreadRepository $threadRepository;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        $this->dimensionRepository = self::getContainer()->get(DimensionRepository::class);
        $this->threadRepository = self::getContainer()->get(ThreadRepository::class);

        $application = new Application();
        $command = self::getContainer()->get(CalcDimensionValueCommand::class);
        self::assertInstanceOf(CalcDimensionValueCommand::class, $command);
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

    public function testExecuteShouldReturnSuccess(): void
    {
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testCommandShouldHaveCorrectName(): void
    {
        $command = self::getContainer()->get(CalcDimensionValueCommand::class);
        self::assertInstanceOf(CalcDimensionValueCommand::class, $command);

        $this->assertSame('forum:calc-dimension-value', $command->getName());
    }

    public function testCommandShouldHaveDescription(): void
    {
        $command = self::getContainer()->get(CalcDimensionValueCommand::class);
        self::assertInstanceOf(CalcDimensionValueCommand::class, $command);

        $this->assertNotEmpty($command->getDescription());
    }
}
