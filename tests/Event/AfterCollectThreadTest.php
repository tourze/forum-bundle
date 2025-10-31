<?php

namespace ForumBundle\Tests\Event;

use ForumBundle\Event\AfterCollectThread;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(AfterCollectThread::class)]
final class AfterCollectThreadTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new AfterCollectThread();
        $this->assertInstanceOf(AfterCollectThread::class, $event);
    }
}
