<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Event;

use ForumBundle\Entity\ThreadComment;
use ForumBundle\Event\AfterCommentThread;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * 评论帖子后事件测试
 *
 * @internal
 */
#[CoversClass(AfterCommentThread::class)]
final class AfterCommentThreadTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new AfterCommentThread();
        $this->assertInstanceOf(AfterCommentThread::class, $event);
    }

    public function testThreadCommentGetterAndSetter(): void
    {
        $event = new AfterCommentThread();
        /*
         * Mock ThreadComment Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $threadComment = $this->createMock(ThreadComment::class);

        $event->setThreadComment($threadComment);
        $this->assertSame($threadComment, $event->getThreadComment());
    }

    public function testResultGetterAndSetter(): void
    {
        $event = new AfterCommentThread();
        $result = ['key' => 'value'];

        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }

    public function testResultDefaultValue(): void
    {
        $event = new AfterCommentThread();
        $this->assertEmpty($event->getResult());
    }
}
