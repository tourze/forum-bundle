<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\ThreadState;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadState::class)]
final class ThreadStateTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(ThreadState::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (ThreadState::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testCasesExist(): void
    {
        $this->assertNotSame(null, ThreadState::tryFrom('auditing'));
        $this->assertNotSame(null, ThreadState::tryFrom('audit_pass'));
        $this->assertNotSame(null, ThreadState::tryFrom('audit_reject'));
        $this->assertNotSame(null, ThreadState::tryFrom('user_delete'));
    }

    public function testToArray(): void
    {
        // Test AUDITING
        $auditingArray = ThreadState::AUDITING->toArray();
        $this->assertIsArray($auditingArray);
        $this->assertEquals('auditing', $auditingArray['value']);
        $this->assertEquals('审核中', $auditingArray['label']);

        // Test AUDIT_PASS
        $auditPassArray = ThreadState::AUDIT_PASS->toArray();
        $this->assertEquals('audit_pass', $auditPassArray['value']);
        $this->assertEquals('审核通过', $auditPassArray['label']);

        // Test AUDIT_REJECT
        $auditRejectArray = ThreadState::AUDIT_REJECT->toArray();
        $this->assertEquals('audit_reject', $auditRejectArray['value']);
        $this->assertEquals('驳回', $auditRejectArray['label']);

        // Test USER_DELETE
        $userDeleteArray = ThreadState::USER_DELETE->toArray();
        $this->assertEquals('user_delete', $userDeleteArray['value']);
        $this->assertEquals('用户删除', $userDeleteArray['label']);

        // Test that all cases can be converted to array
        $caseCount = 0;
        foreach (ThreadState::cases() as $case) {
            $array = $case->toArray();
            $this->assertArrayHasKey('value', $array);
            $this->assertArrayHasKey('label', $array);
            ++$caseCount;
        }
        $this->assertEquals(4, $caseCount);
    }
}
