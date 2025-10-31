<?php

namespace ForumBundle\Tests\Event;

use ForumBundle\Event\ThreadAuditPassEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadAuditPassEvent::class)]
final class ThreadAuditPassEventTest extends AbstractEventTestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        $event = new ThreadAuditPassEvent();
        $this->assertInstanceOf(ThreadAuditPassEvent::class, $event);
    }
}
