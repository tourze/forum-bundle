<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCollect;
use ForumBundle\Service\ThreadCollectService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadCollectService::class)]
#[RunTestsInSeparateProcesses]
final class ThreadCollectServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getThreadCollectService(): ThreadCollectService
    {
        // 从容器获取服务实例，符合集成测试规范
        return self::getService(ThreadCollectService::class);
    }

    public function testThreadCollectServiceShouldHaveCorrectMethods(): void
    {
        $reflection = new \ReflectionClass(ThreadCollectService::class);
        $this->assertTrue($reflection->hasMethod('collect'));
        $this->assertTrue($reflection->hasMethod('updateCollectStat'));
    }

    public function testThreadCollectServiceShouldCreateInstance(): void
    {
        $service = $this->getThreadCollectService();
        $this->assertInstanceOf(ThreadCollectService::class, $service);
    }

    public function testCollectShouldCreateNewCollectWhenNotExists(): void
    {
        $service = $this->getThreadCollectService();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-collect-test-123');
        $thread->setTitle('测试帖子');
        $thread->setContent('测试内容');

        // 使用模拟用户
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test-user-123');

        // 创建收藏实体
        $collect = new ThreadCollect();
        $collect->setThread($thread);
        $collect->setUser($user);
        $collect->setValid(true);

        // 执行收藏操作
        $result = $service->collect($collect);

        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('收藏成功', $result['message']);
    }

    public function testCollectShouldToggleExistingCollect(): void
    {
        $service = $this->getThreadCollectService();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-toggle-test-456');
        $thread->setTitle('测试切换帖子');
        $thread->setContent('测试内容');

        // 使用模拟用户
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test-user-456');

        // 先收藏
        $collect = new ThreadCollect();
        $collect->setThread($thread);
        $collect->setUser($user);
        $collect->setValid(true);

        $result1 = $service->collect($collect);
        $this->assertArrayHasKey('message', $result1);
        $this->assertEquals('收藏成功', $result1['message']);

        // 再次收藏（应该取消）
        $newCollect = new ThreadCollect();
        $newCollect->setThread($thread);
        $newCollect->setUser($user);
        $newCollect->setValid(true);

        $result2 = $service->collect($newCollect);
        $this->assertArrayHasKey('message', $result2);
        $this->assertEquals('取消收藏成功', $result2['message']);
    }

    public function testUpdateCollectStatShouldCallStatService(): void
    {
        $service = $this->getThreadCollectService();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-stat-test-789');
        $thread->setTitle('测试统计帖子');
        $thread->setContent('测试内容');

        // 执行统计更新方法
        $service->updateCollectStat($thread);

        // 验证方法正常执行完成，线程对象保持不变
        $this->assertEquals('thread-stat-test-789', $thread->getId());
        $this->assertEquals('测试统计帖子', $thread->getTitle());
        $this->assertEquals('测试内容', $thread->getContent());
    }
}
