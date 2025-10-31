<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Event;

use ForumBundle\Entity\ThreadCommentLike;
use ForumBundle\Event\AfterLikeThreadComment;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * 点赞帖子评论后事件测试
 *
 * @internal
 */
#[CoversClass(AfterLikeThreadComment::class)]
final class AfterLikeThreadCommentTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new AfterLikeThreadComment();
        $this->assertInstanceOf(AfterLikeThreadComment::class, $event);
    }

    public function testThreadCommentLikeGetterAndSetter(): void
    {
        $event = new AfterLikeThreadComment();
        /*
         * Mock ThreadCommentLike Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $threadCommentLike = $this->createMock(ThreadCommentLike::class);

        $event->setThreadCommentLike($threadCommentLike);
        $this->assertSame($threadCommentLike, $event->getThreadCommentLike());
    }

    public function testResultGetterAndSetter(): void
    {
        $event = new AfterLikeThreadComment();
        $result = ['key' => 'value'];

        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }

    public function testResultDefaultValue(): void
    {
        $event = new AfterLikeThreadComment();
        $this->assertEmpty($event->getResult());
    }
}
