<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadDimension;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 主题维度实体测试
 *
 * @internal
 */
#[CoversClass(ThreadDimension::class)]
final class ThreadDimensionTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ThreadDimension();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'value' => ['value', 100];
        yield 'context' => ['context', ['key' => 'value']];
    }

    public function testThreadDimensionShouldBeInstantiable(): void
    {
        $dimension = new ThreadDimension();

        $this->assertInstanceOf(ThreadDimension::class, $dimension);
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $dimension = new ThreadDimension();

        $result = (string) $dimension;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $dimension = new ThreadDimension();

        $this->assertNotNull($dimension);
        $this->assertNull($dimension->getId());
    }
}
