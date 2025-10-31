<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\ThreadMediaType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadMediaType::class)]
final class ThreadMediaTypeTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(ThreadMediaType::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (ThreadMediaType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testCasesExist(): void
    {
        $this->assertNotSame(null, ThreadMediaType::tryFrom('image'));
        $this->assertNotSame(null, ThreadMediaType::tryFrom('video'));
        $this->assertNotSame(null, ThreadMediaType::tryFrom('article'));
        $this->assertNotSame(null, ThreadMediaType::tryFrom('ITEM'));
    }

    public function testToArray(): void
    {
        // Test IMAGE
        $imageArray = ThreadMediaType::IMAGE->toArray();
        $this->assertIsArray($imageArray);
        $this->assertEquals('image', $imageArray['value']);
        $this->assertEquals('图片', $imageArray['label']);

        // Test VIDEO
        $videoArray = ThreadMediaType::VIDEO->toArray();
        $this->assertEquals('video', $videoArray['value']);
        $this->assertEquals('视频', $videoArray['label']);

        // Test ARTICLE
        $articleArray = ThreadMediaType::ARTICLE->toArray();
        $this->assertEquals('article', $articleArray['value']);
        $this->assertEquals('article', $articleArray['label']);

        // Test ITEM
        $itemArray = ThreadMediaType::ITEM->toArray();
        $this->assertEquals('ITEM', $itemArray['value']);
        $this->assertEquals('item', $itemArray['label']);

        // Test that all cases can be converted to array
        $caseCount = 0;
        foreach (ThreadMediaType::cases() as $case) {
            $array = $case->toArray();
            $this->assertArrayHasKey('value', $array);
            $this->assertArrayHasKey('label', $array);
            ++$caseCount;
        }
        $this->assertEquals(4, $caseCount);
    }
}
