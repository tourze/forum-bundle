<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadMedia;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadMediaRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadMediaRepository::class)]
#[RunTestsInSeparateProcesses]
final class ThreadMediaRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<ThreadMedia>
     */
    /**
     * @return ServiceEntityRepository<ThreadMedia>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ThreadMediaRepository::class);
    }

    protected function createNewEntity(): ThreadMedia
    {
        $thread = new Thread();
        $thread->setTitle('Test Thread ' . uniqid());
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);

        // 需要先持久化 Thread，因为 ThreadMedia 依赖于它
        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($thread);
        $entityManager->flush();

        $entity = new ThreadMedia();
        $entity->setThread($thread);
        $entity->setType('image');
        $entity->setPath('/test/path/image.jpg');
        $entity->setSize(1024);

        return $entity;
    }
}
