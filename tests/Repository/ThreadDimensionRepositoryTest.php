<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadDimension;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadDimensionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadDimensionRepository::class)]
#[RunTestsInSeparateProcesses]
final class ThreadDimensionRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<ThreadDimension>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ThreadDimensionRepository::class);
    }

    protected function createNewEntity(): ThreadDimension
    {
        $entityManager = self::getService(EntityManagerInterface::class);

        $thread = new Thread();
        $thread->setTitle('Test Thread ' . uniqid());
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);
        $entityManager->persist($thread);

        $dimension = new Dimension();
        $dimension->setTitle('Test Dimension ' . uniqid());
        $dimension->setCode('test_dim_' . uniqid());
        $dimension->setValid(true);
        $entityManager->persist($dimension);

        $entityManager->flush();

        $entity = new ThreadDimension();
        $entity->setThread($thread);
        $entity->setDimension($dimension);
        $entity->setValue(100);

        return $entity;
    }
}
