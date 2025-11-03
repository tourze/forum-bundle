<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use ForumBundle\Command\AutoTakeDownThreadCommand;
use ForumBundle\Repository\ThreadRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
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

    /** @var ThreadRepository&MockObject */
    private ThreadRepository $threadRepository;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        /*
         * Mock ThreadRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如createQueryBuilder等），这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $this->threadRepository = $this->createMock(ThreadRepository::class);

        // 将Mock服务注入到容器中，移除EntityManager Mock避免重复初始化错误
        $container = self::getContainer();
        $container->set(ThreadRepository::class, $this->threadRepository);

        $application = new Application();
        $command = self::getContainer()->get(AutoTakeDownThreadCommand::class);
        $this->assertInstanceOf(AutoTakeDownThreadCommand::class, $command);
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteShouldReturnSuccessCode(): void
    {
        $query = $this->createMock(Query::class);
        $query->method('toIterable')->willReturn([]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getQuery')->willReturn($query);

        $this->threadRepository->method('createQueryBuilder')->willReturn($queryBuilder);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
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
