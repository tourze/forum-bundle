<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\VisitStat;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 访问统计实体测试
 *
 * @internal
 */
#[CoversClass(VisitStat::class)]
final class VisitStatTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new VisitStat();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'likeTotal' => ['likeTotal', 100];
        yield 'shareTotal' => ['shareTotal', 50];
        yield 'commentTotal' => ['commentTotal', 30];
        yield 'visitTotal' => ['visitTotal', 200];
        yield 'collectCount' => ['collectCount', 20];
    }

    public function testVisitStatShouldBeInstantiable(): void
    {
        $stat = new VisitStat();

        $this->assertInstanceOf(VisitStat::class, $stat);
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $stat = new VisitStat();

        $result = (string) $stat;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $stat = new VisitStat();

        $this->assertNotNull($stat);
        $this->assertNull($stat->getId());
    }
}
