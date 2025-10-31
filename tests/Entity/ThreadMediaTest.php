<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadMedia;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 主题媒体实体测试
 *
 * @internal
 */
#[CoversClass(ThreadMedia::class)]
final class ThreadMediaTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ThreadMedia();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'type' => ['type', 'image'];
        yield 'path' => ['path', '/uploads/test.jpg'];
        yield 'thumbnail' => ['thumbnail', '/uploads/test_thumb.jpg'];
        yield 'size' => ['size', 1024];
    }

    public function testThreadMediaShouldBeInstantiable(): void
    {
        $media = new ThreadMedia();

        $this->assertInstanceOf(ThreadMedia::class, $media);
    }

    public function testThreadRelationShouldWork(): void
    {
        $media = new ThreadMedia();
        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);

        $media->setThread($thread);

        $this->assertSame($thread, $media->getThread());
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $media = new ThreadMedia();

        $result = (string) $media;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $media = new ThreadMedia();

        $this->assertNotNull($media);
        $this->assertNull($media->getId());
    }
}
