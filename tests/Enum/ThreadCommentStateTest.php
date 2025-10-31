<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\ThreadCommentState;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadCommentState::class)]
final class ThreadCommentStateTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(ThreadCommentState::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (ThreadCommentState::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testCasesExist(): void
    {
        $this->assertNotSame(null, ThreadCommentState::tryFrom('pass'));
        $this->assertNotSame(null, ThreadCommentState::tryFrom('system_delete'));
        $this->assertNotSame(null, ThreadCommentState::tryFrom('user_delete'));
    }

    public function testToArray(): void
    {
        // Test AUDIT_PASS
        $passArray = ThreadCommentState::AUDIT_PASS->toArray();
        $this->assertIsArray($passArray);
        $this->assertEquals('pass', $passArray['value']);
        $this->assertEquals('有效', $passArray['label']);

        // Test SYSTEM_DELETE
        $systemDeleteArray = ThreadCommentState::SYSTEM_DELETE->toArray();
        $this->assertEquals('system_delete', $systemDeleteArray['value']);
        $this->assertEquals('系统删除', $systemDeleteArray['label']);

        // Test USER_DELETE
        $userDeleteArray = ThreadCommentState::USER_DELETE->toArray();
        $this->assertEquals('user_delete', $userDeleteArray['value']);
        $this->assertEquals('用户删除', $userDeleteArray['label']);

        // Test that all cases can be converted to array
        $caseCount = 0;
        foreach (ThreadCommentState::cases() as $case) {
            $array = $case->toArray();
            $this->assertArrayHasKey('value', $array);
            $this->assertArrayHasKey('label', $array);
            ++$caseCount;
        }
        $this->assertEquals(3, $caseCount);
    }
}
