<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Event;

use ForumBundle\Entity\Thread;
use ForumBundle\Event\AfterThreadAddEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * 添加帖子后事件测试
 *
 * @internal
 */
#[CoversClass(AfterThreadAddEvent::class)]
final class AfterThreadAddEventTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new AfterThreadAddEvent();
        $this->assertInstanceOf(AfterThreadAddEvent::class, $event);
    }

    public function testThreadGetterAndSetter(): void
    {
        $event = new AfterThreadAddEvent();
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

    public function testUserGetterAndSetter(): void
    {
        $event = new AfterThreadAddEvent();
        $user = $this->createMock(UserInterface::class);

        $event->setUser($user);
        $this->assertSame($user, $event->getUser());
    }

    public function testResultGetterAndSetter(): void
    {
        $event = new AfterThreadAddEvent();
        $result = ['key' => 'value'];

        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }

    public function testResultDefaultValue(): void
    {
        $event = new AfterThreadAddEvent();
        $this->assertEmpty($event->getResult());
    }
}
