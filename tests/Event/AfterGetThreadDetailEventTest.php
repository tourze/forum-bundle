<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Event;

use ForumBundle\Entity\Thread;
use ForumBundle\Event\AfterGetThreadDetailEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * 获取帖子详情后事件测试
 *
 * @internal
 */
#[CoversClass(AfterGetThreadDetailEvent::class)]
final class AfterGetThreadDetailEventTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new AfterGetThreadDetailEvent();
        $this->assertInstanceOf(AfterGetThreadDetailEvent::class, $event);
    }

    public function testThreadGetterAndSetter(): void
    {
        $event = new AfterGetThreadDetailEvent();
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

    public function testExtraInfoGetterAndSetter(): void
    {
        $event = new AfterGetThreadDetailEvent();
        $extraInfo = ['key' => 'value'];

        $event->setExtraInfo($extraInfo);
        $this->assertSame($extraInfo, $event->getExtraInfo());
    }

    public function testExtraInfoDefaultValue(): void
    {
        $event = new AfterGetThreadDetailEvent();
        $this->assertEmpty($event->getExtraInfo());
    }
}
