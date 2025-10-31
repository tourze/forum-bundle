<?php

namespace ForumBundle\Tests\Event;

use ForumBundle\Event\ThreadAuditRejectEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadAuditRejectEvent::class)]
final class ThreadAuditRejectEventTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new ThreadAuditRejectEvent();
        $this->assertInstanceOf(ThreadAuditRejectEvent::class, $event);
    }
}
