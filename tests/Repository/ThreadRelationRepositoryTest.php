<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadRelation;
use ForumBundle\Enum\ThreadRelationType;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadRelationRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadRelationRepository::class)]
#[RunTestsInSeparateProcesses]
final class ThreadRelationRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 确保测试数据存在，因为这个实体依赖关联实体
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadRelationRepository::class);

        // 如果数据库中没有数据，创建一些基础数据
        if (0 === $repository->count()) {
            $entity = $this->createNewEntity();
            $entityManager->persist($entity);
            $entityManager->flush();
        }
    }

    /**
     * @return ServiceEntityRepository<ThreadRelation>
     */
    /**
     * @return ServiceEntityRepository<ThreadRelation>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ThreadRelationRepository::class);
    }

    protected function createNewEntity(): ThreadRelation
    {
        $thread = new Thread();
        $thread->setTitle('Test Thread ' . uniqid());
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);

        // 先持久化关联的 Thread 实体以避免级联问题
        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($thread);
        $entityManager->flush();

        $entity = new ThreadRelation();
        $entity->setThread($thread);
        $entity->setSourceId('123');
        $entity->setSourceType(ThreadRelationType::CMS_ENTITY);

        return $entity;
    }
}
