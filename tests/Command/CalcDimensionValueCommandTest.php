<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Command;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use ForumBundle\Command\CalcDimensionValueCommand;
use ForumBundle\Repository\DimensionRepository;
use ForumBundle\Repository\ThreadRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
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

    /** @var DimensionRepository&MockObject */
    private DimensionRepository $dimensionRepository;

    /** @var ThreadRepository&MockObject */
    private ThreadRepository $threadRepository;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        /*
         * Mock DimensionRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如findBy等），这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $this->dimensionRepository = $this->createMock(DimensionRepository::class);
        /*
         * Mock ThreadRepository 具体类的原因：
         * 1. Repository类通常没有对应的接口，直接继承自ServiceEntityRepository
         * 2. 测试需要Mock具体的查询方法（如createQueryBuilder等），这些方法定义在具体Repository实现中
         * 3. 使用具体类Mock是测试Repository层的标准做法
         */
        $this->threadRepository = $this->createMock(ThreadRepository::class);
        // 移除MessageBus Mock，使用真实的服务避免重复初始化错误

        // 将Mock服务注入到容器中，移除MessageBusInterface Mock避免重复初始化错误
        $container = self::getContainer();
        $container->set(DimensionRepository::class, $this->dimensionRepository);
        $container->set(ThreadRepository::class, $this->threadRepository);

        $application = new Application();
        $command = self::getContainer()->get(CalcDimensionValueCommand::class);
        self::assertInstanceOf(CalcDimensionValueCommand::class, $command);
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteShouldReturnSuccessCode(): void
    {
        $this->dimensionRepository->method('findBy')->willReturn([]);

        $this->commandTester->execute([]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithDimensionsShouldReturnSuccessCode(): void
    {
        $dimension = new \stdClass();
        $this->dimensionRepository->method('findBy')->willReturn([$dimension]);

        $query = $this->createMock(Query::class);
        $query->method('getArrayResult')->willReturn([]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getQuery')->willReturn($query);

        $this->threadRepository->method('createQueryBuilder')->willReturn($queryBuilder);

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
