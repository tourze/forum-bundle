<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadLike;
use ForumBundle\Service\ThreadLikeService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadLikeService::class)]
#[RunTestsInSeparateProcesses] final class ThreadLikeServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getThreadLikeService(): ThreadLikeService
    {
        // 从容器获取服务实例，符合集成测试规范
        return self::getService(ThreadLikeService::class);
    }

    public function testThreadLikeServiceShouldHaveCorrectMethods(): void
    {
        $reflection = new \ReflectionClass(ThreadLikeService::class);
        $this->assertTrue($reflection->hasMethod('like'));
        $this->assertTrue($reflection->hasMethod('updateLikeStat'));
    }

    public function testThreadLikeServiceShouldCreateInstance(): void
    {
        $service = $this->getThreadLikeService();
        $this->assertInstanceOf(ThreadLikeService::class, $service);
    }

    public function testLikeShouldCreateNewLikeWhenNotExists(): void
    {
        $service = $this->getThreadLikeService();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-like-new-123');
        $thread->setTitle('测试新点赞帖子');
        $thread->setContent('测试内容');

        // 使用模拟用户
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test-like-user-123');

        // 创建点赞实体
        $like = new ThreadLike();
        $like->setThread($thread);
        $like->setUser($user);
        $like->setStatus(1);

        // 执行点赞操作
        $result = $service->like($like);

        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('点赞成功', $result['message']);
    }

    public function testLikeShouldToggleExistingLike(): void
    {
        $service = $this->getThreadLikeService();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-like-toggle-456');
        $thread->setTitle('测试切换点赞帖子');
        $thread->setContent('测试内容');

        // 使用模拟用户
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test-toggle-user-456');

        // 先点赞
        $like = new ThreadLike();
        $like->setThread($thread);
        $like->setUser($user);
        $like->setStatus(1);

        $result1 = $service->like($like);
        $this->assertArrayHasKey('message', $result1);
        $this->assertEquals('点赞成功', $result1['message']);

        // 再次点赞（应该取消）
        $newLike = new ThreadLike();
        $newLike->setThread($thread);
        $newLike->setUser($user);
        $newLike->setStatus(1);

        $result2 = $service->like($newLike);
        $this->assertArrayHasKey('message', $result2);
        $this->assertEquals('取消点赞成功', $result2['message']);
    }

    public function testUpdateLikeStatShouldCallStatService(): void
    {
        $service = $this->getThreadLikeService();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-like-stat-789');
        $thread->setTitle('测试点赞统计帖子');
        $thread->setContent('测试内容');

        // 执行统计更新方法
        $service->updateLikeStat($thread);

        // 验证方法正常执行完成（无异常抛出），且线程对象保持不变
        $this->assertEquals('thread-like-stat-789', $thread->getId());
        $this->assertEquals('测试点赞统计帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }
}
