<?php

declare(strict_types=1);

namespace ForumBundle\Tests\MessageHandler;

use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\Thread;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use ForumBundle\Message\CalcDimensionValueMessage;
use ForumBundle\MessageHandler\CalcDimensionValueHandler;
use ForumBundle\Repository\DimensionRepository;
use ForumBundle\Repository\ThreadRepository;
use ForumBundle\Service\DimensionService;
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
#[RunTestsInSeparateProcesses]
final class CalcDimensionValueHandlerTest extends AbstractIntegrationTestCase
{
    private CalcDimensionValueHandler $handler;

    private ThreadRepository $threadRepository;

    private DimensionRepository $dimensionRepository;

    /** @var DimensionService&MockObject */
    private DimensionService $dimensionService;

    protected function onSetUp(): void
    {
        $this->threadRepository = self::getContainer()->get(ThreadRepository::class);
        $this->dimensionRepository = self::getContainer()->get(DimensionRepository::class);

        // Mock DimensionService 具体类的原因：
        // 1. 这是一个业务服务类，没有定义相应的接口
        // 2. 测试需要隔离复杂的维度计算逻辑实现，只关注当前测试的逻辑
        // 3. 使用具体类Mock是集成复杂业务服务的标准做法
        $this->dimensionService = $this->createMock(DimensionService::class);

        // 将 Mock 服务设置到容器
        self::getContainer()->set(DimensionService::class, $this->dimensionService);

        // 从容器获取处理器实例
        $this->handler = self::getService(CalcDimensionValueHandler::class);

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

    public function testInvokeWithNonExistentThreadShouldReturnEarly(): void
    {
        $message = new CalcDimensionValueMessage();
        $message->setThreadId('999999999'); // 不存在的ID

        // 不应该抛出异常，应该正常处理
        $this->expectNotToPerformAssertions();
        ($this->handler)($message);
    }

    public function testInvokeWithValidThreadShouldProcess(): void
    {
        // 创建一个测试帖子
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test content');
        $thread->setType(ThreadType::USER_THREAD);
        $thread->setStatus(ThreadState::AUDIT_PASS);

        self::getEntityManager()->persist($thread);
        self::getEntityManager()->flush();

        $message = new CalcDimensionValueMessage();
        $message->setThreadId($thread->getId());

        // Mock DimensionService - 可能有多个维度，所以至少调用一次
        $this->dimensionService->expects($this->atLeastOnce())
            ->method('calcThreadDimension')
            ->with($thread)
        ;

        ($this->handler)($message);
    }

    public function testHandlerShouldBeInstantiatable(): void
    {
        $this->assertInstanceOf(CalcDimensionValueHandler::class, $this->handler);
    }
}
