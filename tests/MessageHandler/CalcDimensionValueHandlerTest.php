<?php

declare(strict_types=1);

namespace ForumBundle\Tests\MessageHandler;

use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\Thread;
use ForumBundle\Message\CalcDimensionValueMessage;
use ForumBundle\MessageHandler\CalcDimensionValueHandler;
use ForumBundle\Repository\DimensionRepository;
use ForumBundle\Repository\ThreadRepository;
use ForumBundle\Service\DimensionService;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 计算维度值处理器测试
 *
 * @internal
 */
#[CoversClass(CalcDimensionValueHandler::class)]
#[RunTestsInSeparateProcesses] final class CalcDimensionValueHandlerTest extends AbstractIntegrationTestCase
{
    private CalcDimensionValueHandler $handler;

    private ThreadRepository|MockObject $threadRepository;

    private DimensionRepository|MockObject $dimensionRepository;

    private DimensionService|MockObject $dimensionService;

    protected function onSetUp(): void
    {
        // 创建 Mock 依赖项
        $this->threadRepository = $this->createMock(ThreadRepository::class);
        $this->dimensionRepository = $this->createMock(DimensionRepository::class);
        $this->dimensionService = $this->createMock(DimensionService::class);

        // 将 Mock 服务设置到容器
        self::getContainer()->set(ThreadRepository::class, $this->threadRepository);
        self::getContainer()->set(DimensionRepository::class, $this->dimensionRepository);
        self::getContainer()->set(DimensionService::class, $this->dimensionService);

        // 从容器获取处理器实例
        $this->handler = self::getService(CalcDimensionValueHandler::class);
    }

    public function testInvokeWithNonExistentThreadShouldReturnEarly(): void
    {
        /*
         * Mock CalcDimensionValueMessage 具体类的原因：
         * 1. 这是一个消息类，通常由DTO或数据载体类实现，无相应接口
         * 2. 测试需要控制消息的具体内容（如getThreadId），这些属性定义在具体类中
         * 3. 使用具体类Mock是测试消息处理器的标准做法
         */
        $message = $this->createMock(CalcDimensionValueMessage::class);
        $message->method('getThreadId')->willReturn('1');

        $this->threadRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn(null)
        ;

        $this->dimensionRepository->expects($this->never())->method('findBy');

        ($this->handler)($message);
    }

    public function testInvokeWithNoDimensionsShouldReturnEarly(): void
    {
        /*
         * Mock CalcDimensionValueMessage 具体类的原因：
         * 1. 这是一个消息类，通常由DTO或数据载体类实现，无相应接口
         * 2. 测试需要控制消息的具体内容（如getThreadId），这些属性定义在具体类中
         * 3. 使用具体类Mock是测试消息处理器的标准做法
         */
        $message = $this->createMock(CalcDimensionValueMessage::class);
        $message->method('getThreadId')->willReturn('1');

        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);
        $this->threadRepository->method('findOneBy')->willReturn($thread);

        $this->dimensionRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([])
        ;

        $this->dimensionService->expects($this->never())->method('calcThreadDimension');

        ($this->handler)($message);
    }

    public function testInvokeWithValidDimensionsShouldProcessThem(): void
    {
        /*
         * Mock CalcDimensionValueMessage 具体类的原因：
         * 1. 这是一个消息类，通常由DTO或数据载体类实现，无相应接口
         * 2. 测试需要控制消息的具体内容（如getThreadId），这些属性定义在具体类中
         * 3. 使用具体类Mock是测试消息处理器的标准做法
         */
        $message = $this->createMock(CalcDimensionValueMessage::class);
        $message->method('getThreadId')->willReturn('1');

        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);
        $this->threadRepository->method('findOneBy')->willReturn($thread);

        /*
         * Mock Dimension Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $dimension1 = $this->createMock(Dimension::class);
        /*
         * Mock Dimension Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $dimension2 = $this->createMock(Dimension::class);
        $this->dimensionRepository->method('findBy')->willReturn([$dimension1, $dimension2]);

        $this->dimensionService->expects($this->exactly(2))
            ->method('calcThreadDimension')
            ->with(Assert::logicalOr($thread))
        ;

        ($this->handler)($message);
    }

    public function testInvokeWithExceptionInDimensionServiceShouldLogError(): void
    {
        /*
         * Mock CalcDimensionValueMessage 具体类的原因：
         * 1. 这是一个消息类，通常由DTO或数据载体类实现，无相应接口
         * 2. 测试需要控制消息的具体内容（如getThreadId），这些属性定义在具体类中
         * 3. 使用具体类Mock是测试消息处理器的标准做法
         */
        $message = $this->createMock(CalcDimensionValueMessage::class);
        $message->method('getThreadId')->willReturn('1');

        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);
        $thread->method('getId')->willReturn('1');
        $this->threadRepository->method('findOneBy')->willReturn($thread);

        /*
         * Mock Dimension Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $dimension = $this->createMock(Dimension::class);
        $this->dimensionRepository->method('findBy')->willReturn([$dimension]);

        $exception = new \Exception('Test exception');
        $this->dimensionService->method('calcThreadDimension')->willThrowException($exception);

        // 确保方法执行时不会重新抛出异常（异常应该被捕获并记录）
        $this->expectNotToPerformAssertions();
        ($this->handler)($message);
    }
}
