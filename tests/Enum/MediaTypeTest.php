<?php

namespace ForumBundle\Tests\Enum;

use ForumBundle\Enum\MediaType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(MediaType::class)]
final class MediaTypeTest extends AbstractEnumTestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(enum_exists(MediaType::class));
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (MediaType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function testSpecificCaseValues(): void
    {
        $this->assertEquals('image', MediaType::IMAGE->value);
        $this->assertEquals('video', MediaType::VIDEO->value);
    }

    public function testToArray(): void
    {
        // 测试 IMAGE 的 toArray
        $imageArray = MediaType::IMAGE->toArray();
        $this->assertIsArray($imageArray);
        $this->assertEquals('image', $imageArray['value']);
        $this->assertEquals('图片', $imageArray['label']);

        // 测试 VIDEO 的 toArray
        $videoArray = MediaType::VIDEO->toArray();
        $this->assertIsArray($videoArray);
        $this->assertEquals('video', $videoArray['value']);
        $this->assertEquals('视频', $videoArray['label']);
    }

    public function testGenOptions(): void
    {
        $items = MediaType::genOptions();

        $this->assertIsArray($items);
        $this->assertCount(2, $items);

        // 验证第一个元素 (图片)
        $this->assertArrayHasKey('value', $items[0]);
        $this->assertArrayHasKey('label', $items[0]);
        $this->assertEquals('image', $items[0]['value']);
        $this->assertEquals('图片', $items[0]['label']);

        // 验证第二个元素 (视频)
        $this->assertArrayHasKey('value', $items[1]);
        $this->assertArrayHasKey('label', $items[1]);
        $this->assertEquals('video', $items[1]['value']);
        $this->assertEquals('视频', $items[1]['label']);
    }
}
