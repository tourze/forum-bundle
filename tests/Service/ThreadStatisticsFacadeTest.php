<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Service\ThreadStatisticsFacade;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadStatisticsFacade::class)]
#[RunTestsInSeparateProcesses] final class ThreadStatisticsFacadeTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getThreadStatisticsFacade(): ThreadStatisticsFacade
    {
        // 从容器获取服务实例，符合集成测试规范
        return self::getService(ThreadStatisticsFacade::class);
    }

    public function testThreadStatisticsFacadeShouldHaveCorrectMethods(): void
    {
        $reflection = new \ReflectionClass(ThreadStatisticsFacade::class);
        $this->assertTrue($reflection->hasMethod('updateAllStat'));
        $this->assertTrue($reflection->hasMethod('updateLikeStat'));
        $this->assertTrue($reflection->hasMethod('updateShareStat'));
        $this->assertTrue($reflection->hasMethod('updateCommentStat'));
    }

    public function testThreadStatisticsFacadeShouldCreateInstance(): void
    {
        $service = $this->getThreadStatisticsFacade();
        $this->assertInstanceOf(ThreadStatisticsFacade::class, $service);
    }

    public function testExecuteStatUpdateByType(): void
    {
        $service = $this->getThreadStatisticsFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-stat-type-test-123');
        $thread->setTitle('测试统计类型更新帖子');
        $thread->setContent('测试内容');

        // 执行统计更新方法
        $service->executeStatUpdateByType($thread, 'like');

        // 验证方法正常执行完成，线程对象保持不变
        $this->assertEquals('thread-stat-type-test-123', $thread->getId());
        $this->assertEquals('测试统计类型更新帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }

    public function testUpdateAllStat(): void
    {
        $service = $this->getThreadStatisticsFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-stat-all-test-456');
        $thread->setTitle('测试全部统计更新帖子');
        $thread->setContent('测试内容');

        // 执行统计更新
        $service->updateAllStat($thread);

        // 验证方法正常执行完成，线程对象保持不变
        $this->assertEquals('thread-stat-all-test-456', $thread->getId());
        $this->assertEquals('测试全部统计更新帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }

    public function testUpdateCollectStat(): void
    {
        $service = $this->getThreadStatisticsFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-stat-collect-test-789');
        $thread->setTitle('测试收藏统计更新帖子');
        $thread->setContent('测试内容');

        // 执行收藏统计更新
        $service->updateCollectStat($thread);

        // 验证方法正常执行完成，线程对象保持不变
        $this->assertEquals('thread-stat-collect-test-789', $thread->getId());
        $this->assertEquals('测试收藏统计更新帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }

    public function testUpdateCommentStat(): void
    {
        $service = $this->getThreadStatisticsFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-stat-comment-test-101');
        $thread->setTitle('测试评论统计更新帖子');
        $thread->setContent('测试内容');

        // 执行评论统计更新
        $service->updateCommentStat($thread);

        // 验证方法正常执行完成，线程对象保持不变
        $this->assertEquals('thread-stat-comment-test-101', $thread->getId());
        $this->assertEquals('测试评论统计更新帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }

    public function testUpdateLikeStat(): void
    {
        $service = $this->getThreadStatisticsFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-stat-like-test-202');
        $thread->setTitle('测试点赞统计更新帖子');
        $thread->setContent('测试内容');

        // 执行点赞统计更新
        $service->updateLikeStat($thread);

        // 验证方法正常执行完成，线程对象保持不变
        $this->assertEquals('thread-stat-like-test-202', $thread->getId());
        $this->assertEquals('测试点赞统计更新帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }

    public function testUpdateShareStat(): void
    {
        $service = $this->getThreadStatisticsFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-stat-share-test-303');
        $thread->setTitle('测试分享统计更新帖子');
        $thread->setContent('测试内容');

        // 执行分享统计更新
        $service->updateShareStat($thread);

        // 验证方法正常执行完成，线程对象保持不变
        $this->assertEquals('thread-stat-share-test-303', $thread->getId());
        $this->assertEquals('测试分享统计更新帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }

    public function testUpdateVisitStat(): void
    {
        $service = $this->getThreadStatisticsFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-stat-visit-test-404');
        $thread->setTitle('测试访问统计更新帖子');
        $thread->setContent('测试内容');

        // 执行访问统计更新
        $service->updateVisitStat($thread);

        // 验证方法正常执行完成，线程对象保持不变
        $this->assertEquals('thread-stat-visit-test-404', $thread->getId());
        $this->assertEquals('测试访问统计更新帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }
}
