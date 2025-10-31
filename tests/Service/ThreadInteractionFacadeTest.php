<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCollect;
use ForumBundle\Entity\ThreadLike;
use ForumBundle\Service\ThreadInteractionFacade;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 帖子交互操作门面测试
 *
 * @internal
 */
#[CoversClass(ThreadInteractionFacade::class)]
#[RunTestsInSeparateProcesses] final class ThreadInteractionFacadeTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试需要初始化容器
    }

    private function getThreadInteractionFacade(): ThreadInteractionFacade
    {
        // 从容器获取服务实例，符合集成测试规范
        return self::getService(ThreadInteractionFacade::class);
    }

    public function testLikeWithNewLikeShouldReturnSuccessMessage(): void
    {
        $facade = $this->getThreadInteractionFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-like-test-123');
        $thread->setTitle('测试点赞帖子');
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
        $result = $facade->like($like);

        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('点赞成功', $result['message']);
    }

    public function testCollectWithNewCollectShouldReturnSuccessMessage(): void
    {
        $facade = $this->getThreadInteractionFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-collect-facade-test-456');
        $thread->setTitle('测试门面收藏帖子');
        $thread->setContent('测试内容');

        // 使用模拟用户
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test-collect-user-456');

        // 创建收藏实体
        $collect = new ThreadCollect();
        $collect->setThread($thread);
        $collect->setUser($user);
        $collect->setValid(true);

        // 执行收藏操作
        $result = $facade->collect($collect);

        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('收藏成功', $result['message']);
    }

    public function testLikeAndCollectShouldBothWorkIndependently(): void
    {
        $facade = $this->getThreadInteractionFacade();

        // 创建真实的帖子实体
        $thread = new Thread();
        $thread->setId('thread-both-actions-789');
        $thread->setTitle('测试点赞收藏帖子');
        $thread->setContent('测试内容');

        // 使用模拟用户
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test-both-user-789');

        // 创建点赞实体
        $like = new ThreadLike();
        $like->setThread($thread);
        $like->setUser($user);
        $like->setStatus(1);

        // 创建收藏实体
        $collect = new ThreadCollect();
        $collect->setThread($thread);
        $collect->setUser($user);
        $collect->setValid(true);

        // 执行点赞和收藏操作
        $likeResult = $facade->like($like);
        $collectResult = $facade->collect($collect);

        // 验证两个操作都正确执行
        $this->assertArrayHasKey('message', $likeResult);
        $this->assertEquals('点赞成功', $likeResult['message']);

        $this->assertArrayHasKey('message', $collectResult);
        $this->assertEquals('收藏成功', $collectResult['message']);
    }
}
