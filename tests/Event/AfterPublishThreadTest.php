<?php

namespace ForumBundle\Tests\Event;

use ForumBundle\Event\AfterPublishThread;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(AfterPublishThread::class)]
final class AfterPublishThreadTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new AfterPublishThread();
        $this->assertInstanceOf(AfterPublishThread::class, $event);
    }
}
