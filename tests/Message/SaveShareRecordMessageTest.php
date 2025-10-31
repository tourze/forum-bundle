<?php

namespace ForumBundle\Tests\Message;

use ForumBundle\Message\SaveShareRecordMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SaveShareRecordMessage::class)]
final class SaveShareRecordMessageTest extends TestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        // Message 类是轻量级的数据传输对象，可以直接实例化
        $message = new SaveShareRecordMessage();
        $this->assertInstanceOf(SaveShareRecordMessage::class, $message);
    }
}
