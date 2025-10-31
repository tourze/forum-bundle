<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use ForumBundle\Command\UpdateThreadVisitStatCommand;
use ForumBundle\Entity\Thread;
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

    /** @var ThreadRepository&\PHPUnit\Framework\MockObject\MockObject */
    private ThreadRepository $threadRepository;

    /** @var ThreadStatisticsFacade&\PHPUnit\Framework\MockObject\MockObject */
    private ThreadStatisticsFacade $threadStatisticsFacade;

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
        /*
         * Mock ThreadStatisticsFacade 具体类的原因：
         * 1. 这是一个业务门面类，没有定义相应的接口
         * 2. 测试需要隔离复杂的统计逻辑实现，只关注当前测试的逻辑
         * 3. 使用具体类Mock是集成复杂业务服务的标准做法
         */
        $this->threadStatisticsFacade = $this->createMock(ThreadStatisticsFacade::class);

        // 将Mock服务注入到容器中
        $container = self::getContainer();
        $container->set(ThreadRepository::class, $this->threadRepository);
        $container->set(ThreadStatisticsFacade::class, $this->threadStatisticsFacade);

        $application = new Application();
        $command = self::getContainer()->get(UpdateThreadVisitStatCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatCommand::class, $command);
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithNoThreadsShouldReturnSuccess(): void
    {
        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn([]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getQuery')->willReturn($query);

        $this->threadRepository->method('createQueryBuilder')->willReturn($queryBuilder);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithThreadsShouldUpdateStats(): void
    {
        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);
        $thread->method('getId')->willReturn('1');

        $query = $this->createMock(Query::class);
        $query->method('setMaxResults')->willReturn($query);
        $query->method('getResult')->willReturn([$thread]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('where')->willReturn($queryBuilder);
        $queryBuilder->method('orderBy')->willReturn($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);

        $this->threadRepository->method('createQueryBuilder')->willReturn($queryBuilder);
        $this->threadStatisticsFacade->expects($this->once())->method('updateAllStat')->with($thread);

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
