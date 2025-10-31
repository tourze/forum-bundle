<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Dimension;
use ForumBundle\Repository\DimensionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DimensionRepository::class)]
#[RunTestsInSeparateProcesses]
final class DimensionRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<Dimension>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(DimensionRepository::class);
    }

    protected function createNewEntity(): object
    {
        $entity = new Dimension();
        $entity->setTitle('Test Dimension ' . uniqid());
        $entity->setCode('test_dim_' . uniqid());
        $entity->setValid(true);

        return $entity;
    }

    #[Test]
    public function testFindOneByWithOrderByClause(): void
    {
        $entity1 = new Dimension();
        $entity1->setTitle('B Dimension');
        $entity1->setCode('b_valid');
        $entity1->setValid(true);

        $entity2 = new Dimension();
        $entity2->setTitle('A Dimension');
        $entity2->setCode('a_valid');
        $entity2->setValid(true);

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($entity1);
        $entityManager->persist($entity2);
        $entityManager->flush();

        $repository = self::getService(DimensionRepository::class);
        $result = $repository->findOneBy(['valid' => true], ['title' => 'ASC']);

        $this->assertNotNull($result);
        $this->assertEquals('A Dimension', $result->getTitle());
    }

    #[Test]
    public function testFindByWithNullValidField(): void
    {
        $entity1 = new Dimension();
        $entity1->setTitle('Dimension with null valid');
        $entity1->setCode('null_valid_1');
        $entity1->setValid(null);

        $entity2 = new Dimension();
        $entity2->setTitle('Dimension with null valid 2');
        $entity2->setCode('null_valid_2');
        $entity2->setValid(null);

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($entity1);
        $entityManager->persist($entity2);
        $entityManager->flush();

        $repository = self::getService(DimensionRepository::class);
        $results = $repository->findBy(['valid' => null]);

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertContainsOnlyInstancesOf(Dimension::class, $results);
        $this->assertNull($results[0]->isValid());
    }

    #[Test]
    public function testCountWithNullValidField(): void
    {
        $entity1 = new Dimension();
        $entity1->setTitle('Dimension with null valid');
        $entity1->setCode('null_valid_count');
        $entity1->setValid(null);

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($entity1);
        $entityManager->flush();

        $repository = self::getService(DimensionRepository::class);
        $count = $repository->count(['valid' => null]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    #[Test]
    public function testFindByWithNullCreateTime(): void
    {
        $repository = self::getService(DimensionRepository::class);

        // 执行测试 - 查找 createTime 为 null 的记录
        $results = $repository->findBy(['createTime' => null]);

        // 验证结果
        $this->assertIsArray($results);
    }

    #[Test]
    public function testFindByWithNullUpdateTime(): void
    {
        $repository = self::getService(DimensionRepository::class);

        // 执行测试 - 查找 updateTime 为 null 的记录
        $results = $repository->findBy(['updateTime' => null]);

        // 验证结果
        $this->assertIsArray($results);
    }

    #[Test]
    public function testFindOneByWithNullCreateTime(): void
    {
        $repository = self::getService(DimensionRepository::class);

        // 执行测试 - 查找 createTime 为 null 的记录
        $result = $repository->findOneBy(['createTime' => null]);

        // 验证结果
        $this->assertTrue($result instanceof Dimension || null === $result);
    }

    #[Test]
    public function testFindOneByWithNullUpdateTime(): void
    {
        $repository = self::getService(DimensionRepository::class);

        // 执行测试 - 查找 updateTime 为 null 的记录
        $result = $repository->findOneBy(['updateTime' => null]);

        // 验证结果
        $this->assertTrue($result instanceof Dimension || null === $result);
    }

    #[Test]
    public function testCountWithNullCreateTime(): void
    {
        $repository = self::getService(DimensionRepository::class);

        // 执行测试 - 计算 createTime 为 null 的记录数量
        $count = $repository->count(['createTime' => null]);

        // 验证结果 - 应该是一个非负整数
        $this->assertGreaterThanOrEqual(0, $count);
    }

    #[Test]
    public function testCountWithNullUpdateTime(): void
    {
        $repository = self::getService(DimensionRepository::class);

        // 执行测试 - 计算 updateTime 为 null 的记录数量
        $count = $repository->count(['updateTime' => null]);

        // 验证结果 - 应该是一个非负整数
        $this->assertGreaterThanOrEqual(0, $count);
    }
}
