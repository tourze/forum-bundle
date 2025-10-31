<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadRelation;
use ForumBundle\Enum\ThreadRelationType;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 主题关系实体测试
 *
 * @internal
 */
#[CoversClass(ThreadRelation::class)]
final class ThreadRelationTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ThreadRelation();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'sourceId' => ['sourceId', '123456'];
        yield 'sourceType' => ['sourceType', ThreadRelationType::CMS_ENTITY];
    }

    public function testThreadRelationShouldBeInstantiable(): void
    {
        $relation = new ThreadRelation();

        $this->assertInstanceOf(ThreadRelation::class, $relation);
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $relation = new ThreadRelation();

        $result = (string) $relation;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $relation = new ThreadRelation();

        $this->assertNotNull($relation);
        $this->assertNull($relation->getId());
    }
}
