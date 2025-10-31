<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Service\ThreadManagementService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 帖子管理服务测试
 *
 * @internal
 */
#[CoversClass(ThreadManagementService::class)]
#[RunTestsInSeparateProcesses] final class ThreadManagementServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试需要初始化容器
    }

    private function getThreadManagementService(): ThreadManagementService
    {
        // 从容器获取服务实例，符合集成测试规范
        return self::getService(ThreadManagementService::class);
    }

    public function testAddNewThreadShouldPersistAndReturnResult(): void
    {
        $service = $this->getThreadManagementService();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-add-test-123');
        $thread->setTitle('测试新增帖子');
        $thread->setContent('测试新增帖子内容');

        // 执行新增帖子操作
        $result = $service->add($thread);

        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('thread-add-test-123', $result['id']);
    }

    public function testFindThreadByIdWithInvalidIdShouldThrowException(): void
    {
        $service = $this->getThreadManagementService();

        // 使用不存在的ID测试异常
        $threadId = 'non-existent-thread-id-999999';

        // 执行查找应该抛出异常
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('帖子不存在~');

        $service->findThreadById($threadId);
    }

    public function testThreadVisitStat(): void
    {
        $service = $this->getThreadManagementService();

        // 创建测试帖子
        $thread = new Thread();
        $thread->setId('thread-visit-stat-test-123');
        $thread->setTitle('测试访问统计帖子');
        $thread->setContent('测试访问统计帖子内容');

        // 先保存帖子到数据库
        $em = self::getEntityManager();
        $em->persist($thread);
        $em->flush();

        // 测试各种统计类型
        $validTypes = ['like', 'share', 'comment', 'visit', 'collect'];

        foreach ($validTypes as $type) {
            // 这里主要测试方法不会抛出异常
            $threadId = $thread->getId();
            if (null !== $threadId) {
                $service->threadVisitStat($threadId, $type);
                $this->assertTrue(true); // 断言执行成功
            }
        }

        // 测试无效类型（默认分支）
        $threadId = $thread->getId();
        if (null !== $threadId) {
            $service->threadVisitStat($threadId, 'invalid-type');
            $this->assertTrue(true); // 断言执行成功
        }

        // 测试不存在的帖子ID
        $service->threadVisitStat('non-existent-thread-id', 'visit');
        $this->assertTrue(true); // 方法应该静默返回，不抛出异常
    }
}
