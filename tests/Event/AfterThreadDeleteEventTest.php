<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Event;

use ForumBundle\Entity\Thread;
use ForumBundle\Event\AfterThreadDeleteEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * 删除帖子后事件测试
 *
 * @internal
 */
#[CoversClass(AfterThreadDeleteEvent::class)]
final class AfterThreadDeleteEventTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new AfterThreadDeleteEvent();
        $this->assertInstanceOf(AfterThreadDeleteEvent::class, $event);
    }

    public function testThreadGetterAndSetter(): void
    {
        $event = new AfterThreadDeleteEvent();
        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);

        $event->setThread($thread);
        $this->assertSame($thread, $event->getThread());
    }
}
