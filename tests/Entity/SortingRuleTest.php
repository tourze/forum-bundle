<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\SortingRule;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 排序规则实体测试
 *
 * @internal
 */
#[CoversClass(SortingRule::class)]
final class SortingRuleTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new SortingRule();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', '测试排序规则'];
        yield 'formula' => ['formula', 'like_count * 2 + comment_count'];
    }

    public function testSortingRuleShouldBeInstantiable(): void
    {
        $rule = new SortingRule();

        $this->assertInstanceOf(SortingRule::class, $rule);
    }

    public function testDimensionRelationShouldWork(): void
    {
        $rule = new SortingRule();
        /*
         * Mock Dimension Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $dimension = $this->createMock(Dimension::class);

        $rule->setDimension($dimension);

        $this->assertSame($dimension, $rule->getDimension());
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $rule = new SortingRule();
        $rule->setId('1');
        $rule->setTitle('测试规则');
        $rule->setFormula('test_formula');

        $result = (string) $rule;

        $this->assertEquals('测试规则 test_formula', $result);
    }

    public function testToStringWithNullIdShouldReturnEmptyString(): void
    {
        $rule = new SortingRule();

        $result = (string) $rule;

        $this->assertEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $rule = new SortingRule();

        $this->assertNotNull($rule);
        $this->assertNull($rule->getId());
    }
}
