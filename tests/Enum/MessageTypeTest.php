<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\MessageType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(MessageType::class)]
final class MessageTypeTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(MessageType::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (MessageType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testCasesExist(): void
    {
        $this->assertNotSame(null, MessageType::tryFrom('system_notification'));
        $this->assertNotSame(null, MessageType::tryFrom('reply'));
        $this->assertNotSame(null, MessageType::tryFrom('follow'));
        $this->assertNotSame(null, MessageType::tryFrom('private_letter'));
        $this->assertNotSame(null, MessageType::tryFrom('like_thread'));
        $this->assertNotSame(null, MessageType::tryFrom('like_thread_comment'));
        $this->assertNotSame(null, MessageType::tryFrom('collect_thread'));
    }

    public function testToArray(): void
    {
        // Test SYSTEM_NOTIFICATION
        $systemNotificationArray = MessageType::SYSTEM_NOTIFICATION->toArray();
        $this->assertIsArray($systemNotificationArray);
        $this->assertEquals('system_notification', $systemNotificationArray['value']);
        $this->assertEquals('系统通知', $systemNotificationArray['label']);

        // Test REPLY
        $replyArray = MessageType::REPLY->toArray();
        $this->assertEquals('reply', $replyArray['value']);
        $this->assertEquals('回复', $replyArray['label']);

        // Test FOLLOW
        $followArray = MessageType::FOLLOW->toArray();
        $this->assertEquals('follow', $followArray['value']);
        $this->assertEquals('关注', $followArray['label']);

        // Test PRIVATE_LETTER
        $privateLetterArray = MessageType::PRIVATE_LETTER->toArray();
        $this->assertEquals('private_letter', $privateLetterArray['value']);
        $this->assertEquals('私信', $privateLetterArray['label']);

        // Test LIKE_THREAD
        $likeThreadArray = MessageType::LIKE_THREAD->toArray();
        $this->assertEquals('like_thread', $likeThreadArray['value']);
        $this->assertEquals('点赞帖子', $likeThreadArray['label']);

        // Test LIKE_THREAD_COMMENT
        $likeThreadCommentArray = MessageType::LIKE_THREAD_COMMENT->toArray();
        $this->assertEquals('like_thread_comment', $likeThreadCommentArray['value']);
        $this->assertEquals('点赞评论', $likeThreadCommentArray['label']);

        // Test COLLECT_THREAD
        $collectThreadArray = MessageType::COLLECT_THREAD->toArray();
        $this->assertEquals('collect_thread', $collectThreadArray['value']);
        $this->assertEquals('收藏帖子', $collectThreadArray['label']);

        // Test that all cases can be converted to array
        $caseCount = 0;
        foreach (MessageType::cases() as $case) {
            $array = $case->toArray();
            $this->assertArrayHasKey('value', $array);
            $this->assertArrayHasKey('label', $array);
            ++$caseCount;
        }
        $this->assertEquals(7, $caseCount);
    }
}
