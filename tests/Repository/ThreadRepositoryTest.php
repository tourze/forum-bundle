<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use ForumBundle\Repository\ThreadRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CatalogBundle\Entity\CatalogType;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadRepository::class)]
#[RunTestsInSeparateProcesses]
final class ThreadRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<Thread>
     */
    /**
     * @return ServiceEntityRepository<Thread>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ThreadRepository::class);
    }

    protected function createNewEntity(): Thread
    {
        $entity = new Thread();
        $entity->setTitle('Test Thread ' . uniqid());
        $entity->setContent('Test content for thread ' . uniqid());
        $entity->setStatus(ThreadState::AUDIT_PASS);
        $entity->setType(ThreadType::USER_THREAD);

        return $entity;
    }

    public function testCountByAssociationCatalogShouldReturnCorrectNumber(): void
    {
        $entityManager = self::getService(EntityManagerInterface::class);

        // 清理已有数据
        $entityManager->createQuery('DELETE FROM ' . Thread::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Catalog::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . CatalogType::class)->execute();

        $catalogType = new CatalogType();
        $catalogType->setCode('test_type');
        $catalogType->setName('Test Type');
        $entityManager->persist($catalogType);

        $catalog1 = new Catalog();
        $catalog1->setName('Catalog A');
        $catalog1->setDescription('Content A');
        $catalog1->setType($catalogType);

        $catalog2 = new Catalog();
        $catalog2->setName('Catalog B');
        $catalog2->setDescription('Content B');
        $catalog2->setType($catalogType);

        $entityManager->persist($catalog1);
        $entityManager->persist($catalog2);

        // 为 catalog1 创建 4 个帖子
        for ($i = 0; $i < 4; ++$i) {
            $entity = new Thread();
            $entity->setTitle("Thread {$i} for Catalog A");
            $entity->setContent("Content {$i}");
            $entity->setCatalog($catalog1);
            $entity->setStatus(ThreadState::AUDIT_PASS);
            $entityManager->persist($entity);
        }

        // 为 catalog2 创建 2 个帖子
        for ($i = 0; $i < 2; ++$i) {
            $entity = new Thread();
            $entity->setTitle("Thread {$i} for Catalog B");
            $entity->setContent("Content {$i}");
            $entity->setCatalog($catalog2);
            $entity->setStatus(ThreadState::AUDIT_PASS);
            $entityManager->persist($entity);
        }

        $entityManager->flush();

        $count = $this->getRepository()->count(['catalog' => $catalog1]);

        $this->assertEquals(4, $count);
    }
}
