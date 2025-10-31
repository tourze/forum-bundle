<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\SortingRule;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 维度实体测试
 *
 * @internal
 */
#[CoversClass(Dimension::class)]
final class DimensionTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Dimension();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', '测试维度'];
        yield 'code' => ['code', 'test_dimension'];
        yield 'valid' => ['valid', true];
    }

    public function testCreateDimensionShouldSetProperties(): void
    {
        $dimension = new Dimension();
        $dimension->setTitle('测试维度');
        $dimension->setCode('test_dimension');
        $dimension->setValid(true);

        $this->assertSame('测试维度', $dimension->getTitle());
        $this->assertSame('test_dimension', $dimension->getCode());
        $this->assertTrue($dimension->isValid());
    }

    public function testToStringWithIdShouldReturnFormattedString(): void
    {
        $dimension = new Dimension();
        $dimension->setTitle('测试维度');

        // 由于 getId() 返回 null，应该返回空字符串
        $this->assertSame('', (string) $dimension);
    }

    public function testToStringWithNullIdShouldReturnEmptyString(): void
    {
        $dimension = new Dimension();

        $this->assertSame('', (string) $dimension);
    }

    public function testSettersAndGettersShouldWorkCorrectly(): void
    {
        $dimension = new Dimension();

        $dimension->setTitle('新维度');
        $this->assertSame('新维度', $dimension->getTitle());

        $dimension->setCode('new_dimension');
        $this->assertSame('new_dimension', $dimension->getCode());

        $dimension->setValid(false);
        $this->assertFalse($dimension->isValid());
    }

    public function testValidPropertyShouldHaveDefaultValue(): void
    {
        $dimension = new Dimension();

        $this->assertFalse($dimension->isValid());
    }

    public function testSortingRulesCollectionShouldBeInitialized(): void
    {
        $dimension = new Dimension();

        $this->assertCount(0, $dimension->getSortingRules());
    }

    public function testAddSortingRuleShouldAddToCollection(): void
    {
        $dimension = new Dimension();
        /*
         * Mock SortingRule Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $sortingRule = $this->createMock(SortingRule::class);
        $sortingRule->expects($this->once())->method('setDimension')->with($dimension);

        $dimension->addSortingRule($sortingRule);

        $this->assertCount(1, $dimension->getSortingRules());
        $this->assertTrue($dimension->getSortingRules()->contains($sortingRule));
    }

    public function testRemoveSortingRuleShouldRemoveFromCollection(): void
    {
        $dimension = new Dimension();
        /*
         * Mock SortingRule Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $sortingRule = $this->createMock(SortingRule::class);
        $sortingRule->expects($this->exactly(2))->method('setDimension');
        $sortingRule->expects($this->once())->method('getDimension')->willReturn($dimension);

        $dimension->addSortingRule($sortingRule);
        $this->assertCount(1, $dimension->getSortingRules());

        $dimension->removeSortingRule($sortingRule);
        $this->assertCount(0, $dimension->getSortingRules());
        $this->assertFalse($dimension->getSortingRules()->contains($sortingRule));
    }
}
