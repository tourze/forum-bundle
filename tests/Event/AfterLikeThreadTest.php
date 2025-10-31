<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Event;

use ForumBundle\Entity\ThreadLike;
use ForumBundle\Event\AfterLikeThread;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * 点赞帖子后事件测试
 *
 * @internal
 */
#[CoversClass(AfterLikeThread::class)]
final class AfterLikeThreadTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new AfterLikeThread();
        $this->assertInstanceOf(AfterLikeThread::class, $event);
    }

    public function testThreadLikeGetterAndSetter(): void
    {
        $event = new AfterLikeThread();
        /*
         * Mock ThreadLike Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $threadLike = $this->createMock(ThreadLike::class);

        $event->setThreadLike($threadLike);
        $this->assertSame($threadLike, $event->getThreadLike());
    }

    public function testResultGetterAndSetter(): void
    {
        $event = new AfterLikeThread();
        $result = ['key' => 'value'];

        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }

    public function testResultDefaultValue(): void
    {
        $event = new AfterLikeThread();
        $this->assertEmpty($event->getResult());
    }
}
