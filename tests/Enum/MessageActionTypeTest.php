<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\MessageActionType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(MessageActionType::class)]
final class MessageActionTypeTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(MessageActionType::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (MessageActionType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testSpecificCaseValues(): void
    {
        $this->assertEquals('draw', MessageActionType::DRAW->value);
        $this->assertEquals('exit', MessageActionType::EXIT->value);
        $this->assertEquals('kick', MessageActionType::KICK->value);
    }

    public function testToArray(): void
    {
        // Test instance method toArray() from ItemTrait
        $drawArray = MessageActionType::DRAW->toArray();
        $this->assertIsArray($drawArray);
        $this->assertArrayHasKey('value', $drawArray);
        $this->assertArrayHasKey('label', $drawArray);
        $this->assertEquals('draw', $drawArray['value']);
        $this->assertEquals('抽奖', $drawArray['label']);

        $exitArray = MessageActionType::EXIT->toArray();
        $this->assertEquals('exit', $exitArray['value']);
        $this->assertEquals('退出', $exitArray['label']);

        $kickArray = MessageActionType::KICK->toArray();
        $this->assertEquals('kick', $kickArray['value']);
        $this->assertEquals('踢出', $kickArray['label']);
    }
}
