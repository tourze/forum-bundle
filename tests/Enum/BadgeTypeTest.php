<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\BadgeType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(BadgeType::class)]
final class BadgeTypeTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(BadgeType::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (BadgeType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testSpecificCaseValues(): void
    {
        $this->assertEquals('thread', BadgeType::THREAD->value);
        $this->assertEquals('thread_comment', BadgeType::THREAD_COMMENT->value);
        $this->assertEquals('thread_like', BadgeType::THREAD_LIKE->value);
        $this->assertEquals('thread_collect', BadgeType::THREAD_COLLECT->value);
        $this->assertEquals('invite', BadgeType::INVITE->value);
        $this->assertEquals('share', BadgeType::SHARE->value);
        $this->assertEquals('checkin', BadgeType::CHECKIN->value);
        $this->assertEquals('fans', BadgeType::FANS->value);
    }

    public function testSpecificCaseLabels(): void
    {
        $this->assertEquals('发布帖子', BadgeType::THREAD->getLabel());
        $this->assertEquals('发布评论', BadgeType::THREAD_COMMENT->getLabel());
        $this->assertEquals('点赞', BadgeType::THREAD_LIKE->getLabel());
        $this->assertEquals('收藏', BadgeType::THREAD_COLLECT->getLabel());
        $this->assertEquals('邀请好友', BadgeType::INVITE->getLabel());
        $this->assertEquals('分享', BadgeType::SHARE->getLabel());
        $this->assertEquals('签到', BadgeType::CHECKIN->getLabel());
        $this->assertEquals('粉丝关注', BadgeType::FANS->getLabel());
    }

    public function testToArray(): void
    {
        foreach (BadgeType::cases() as $case) {
            $result = $case->toArray();
            $this->assertIsArray($result);
            $this->assertArrayHasKey('value', $result);
            $this->assertArrayHasKey('label', $result);
            $this->assertEquals($case->value, $result['value']);
            $this->assertEquals($case->getLabel(), $result['label']);
        }
    }
}
