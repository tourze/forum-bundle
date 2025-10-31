<?php

namespace ForumBundle\Tests\Message;

use ForumBundle\Message\CalcDimensionValueMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CalcDimensionValueMessage::class)]
final class CalcDimensionValueMessageTest extends TestCase
{
    public function testConstructorWorksWithoutParameters(): void
    {
        // Message 类是轻量级的数据传输对象，可以直接实例化
        $message = new CalcDimensionValueMessage();
        $this->assertInstanceOf(CalcDimensionValueMessage::class, $message);
    }
}
