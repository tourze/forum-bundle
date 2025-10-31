<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\ThreadRelationType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadRelationType::class)]
final class ThreadRelationTypeTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(ThreadRelationType::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (ThreadRelationType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testSpecificCaseValues(): void
    {
        $this->assertEquals('cms_entity', ThreadRelationType::CMS_ENTITY->value);
    }

    public function testToArray(): void
    {
        // Test CMS_ENTITY
        $cmsEntityArray = ThreadRelationType::CMS_ENTITY->toArray();
        $this->assertIsArray($cmsEntityArray);
        $this->assertEquals('cms_entity', $cmsEntityArray['value']);
        $this->assertEquals('文章', $cmsEntityArray['label']);

        // Test that all cases can be converted to array
        $caseCount = 0;
        foreach (ThreadRelationType::cases() as $case) {
            $array = $case->toArray();
            $this->assertArrayHasKey('value', $array);
            $this->assertArrayHasKey('label', $array);
            ++$caseCount;
        }
        $this->assertEquals(1, $caseCount);
    }
}
