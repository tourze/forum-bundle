<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadLike;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadLikeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadLikeRepository::class)]
#[RunTestsInSeparateProcesses]
final class ThreadLikeRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 为数据库创建一些基本数据，确保 count 测试能通过
        $entity = $this->createNewEntity();
        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    /**
     * @return ServiceEntityRepository<ThreadLike>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ThreadLikeRepository::class);
    }

    protected function createNewEntity(): ThreadLike
    {
        // 持久化Thread实体以确保在跨进程测试中可正确序列化
        $em = self::getService(EntityManagerInterface::class);

        $thread = new Thread();
        $thread->setTitle('Test Thread ' . uniqid());
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);
        $em->persist($thread);

        $user = $this->createNormalUser('test_user_' . uniqid());
        $em->flush(); // 确保Thread被持久化

        $entity = new ThreadLike();
        $entity->setThread($thread);
        $entity->setUser($user);
        $entity->setStatus(1);

        return $entity;
    }
}
