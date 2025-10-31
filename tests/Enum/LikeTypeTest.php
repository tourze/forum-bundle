<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\LikeType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(LikeType::class)]
final class LikeTypeTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(LikeType::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (LikeType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testSpecificCaseValues(): void
    {
        $this->assertEquals('thread', LikeType::THREAD->value);
        $this->assertEquals('comment', LikeType::COMMENT->value);
    }

    public function testSpecificCaseLabels(): void
    {
        $this->assertEquals('点赞帖子', LikeType::THREAD->getLabel());
        $this->assertEquals('点赞评论', LikeType::COMMENT->getLabel());
    }

    public function testToArray(): void
    {
        // Test THREAD case
        $threadArray = LikeType::THREAD->toArray();
        $this->assertIsArray($threadArray);
        $this->assertArrayHasKey('value', $threadArray);
        $this->assertArrayHasKey('label', $threadArray);
        $this->assertEquals('thread', $threadArray['value']);
        $this->assertEquals('点赞帖子', $threadArray['label']);

        // Test COMMENT case
        $commentArray = LikeType::COMMENT->toArray();
        $this->assertIsArray($commentArray);
        $this->assertArrayHasKey('value', $commentArray);
        $this->assertArrayHasKey('label', $commentArray);
        $this->assertEquals('comment', $commentArray['value']);
        $this->assertEquals('点赞评论', $commentArray['label']);
    }
}
