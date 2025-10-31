<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use ForumBundle\Command\UpdateThreadVisitStatCollectRankCommand;
use ForumBundle\Entity\VisitStat;
use ForumBundle\Repository\VisitStatRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 更新线程访问统计收藏排名命令测试
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(UpdateThreadVisitStatCollectRankCommand::class)]
final class UpdateThreadVisitStatCollectRankCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    /** @var VisitStatRepository&\PHPUnit\Framework\MockObject\MockObject */
    private VisitStatRepository $visitStatRepository;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        /*
         * Mock VisitStatRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如createQueryBuilder等），这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $this->visitStatRepository = $this->createMock(VisitStatRepository::class);

        // 将Mock服务注入到容器中，移除EntityManager Mock避免重复初始化错误
        $container = self::getContainer();
        $container->set(VisitStatRepository::class, $this->visitStatRepository);

        $application = new Application();
        $command = self::getContainer()->get(UpdateThreadVisitStatCollectRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatCollectRankCommand::class, $command);
        $application->add($command);

        $this->commandTester = new CommandTester($command);
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

        // 返回空查询结果，避免EntityManager处理Mock对象的问题
        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn([]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('orderBy')->willReturn($queryBuilder);
        $queryBuilder->method('setMaxResults')->willReturn($queryBuilder);
        $queryBuilder->method('where')->willReturn($queryBuilder);
        $queryBuilder->method('andWhere')->willReturn($queryBuilder);
        $queryBuilder->method('setParameter')->willReturn($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);

        $this->visitStatRepository->method('createQueryBuilder')->willReturn($queryBuilder);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testCommandShouldHaveCorrectName(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatCollectRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatCollectRankCommand::class, $command);

        $this->assertSame('forum:update-thread-stat-collect-rank', $command->getName());
    }

    public function testCommandShouldHaveDescription(): void
    {
        $command = self::getContainer()->get(UpdateThreadVisitStatCollectRankCommand::class);
        self::assertInstanceOf(UpdateThreadVisitStatCollectRankCommand::class, $command);

        $this->assertNotEmpty($command->getDescription());
    }
}
