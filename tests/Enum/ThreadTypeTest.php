<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\ThreadType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadType::class)]
final class ThreadTypeTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(ThreadType::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (ThreadType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testCasesExist(): void
    {
        $this->assertNotSame(null, ThreadType::tryFrom('user_thread'));
        $this->assertNotSame(null, ThreadType::tryFrom('topic_thread'));
        $this->assertNotSame(null, ThreadType::tryFrom('activity_thread'));
    }

    public function testToArray(): void
    {
        // Test USER_THREAD
        $userThreadArray = ThreadType::USER_THREAD->toArray();
        $this->assertIsArray($userThreadArray);
        $this->assertEquals('user_thread', $userThreadArray['value']);
        $this->assertEquals('用户帖子', $userThreadArray['label']);

        // Test TOPIC_THREAD
        $topicThreadArray = ThreadType::TOPIC_THREAD->toArray();
        $this->assertEquals('topic_thread', $topicThreadArray['value']);
        $this->assertEquals('话题主贴', $topicThreadArray['label']);

        // Test ACTIVITY_THREAD
        $activityThreadArray = ThreadType::ACTIVITY_THREAD->toArray();
        $this->assertEquals('activity_thread', $activityThreadArray['value']);
        $this->assertEquals('活动主贴', $activityThreadArray['label']);

        // Test that all cases can be converted to array
        $caseCount = 0;
        foreach (ThreadType::cases() as $case) {
            $array = $case->toArray();
            $this->assertArrayHasKey('value', $array);
            $this->assertArrayHasKey('label', $array);
            ++$caseCount;
        }
        $this->assertEquals(3, $caseCount);
    }
}
