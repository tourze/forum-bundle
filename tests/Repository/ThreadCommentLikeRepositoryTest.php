<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadComment;
use ForumBundle\Entity\ThreadCommentLike;
use ForumBundle\Enum\ThreadCommentState;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadCommentLikeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadCommentLikeRepository::class)]
#[RunTestsInSeparateProcesses]
final class ThreadCommentLikeRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    /**
     * @return ServiceEntityRepository<ThreadCommentLike>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ThreadCommentLikeRepository::class);
    }

    protected function createNewEntity(): ThreadCommentLike
    {
        // 创建并持久化关联实体以确保可序列化
        $em = self::getService(EntityManagerInterface::class);

        $thread = new Thread();
        $thread->setTitle('Test Thread ' . uniqid());
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);
        $em->persist($thread);

        $threadComment = new ThreadComment();
        $threadComment->setThread($thread);
        $threadComment->setContent('Test Comment ' . uniqid());
        $threadComment->setParentId('0');
        $threadComment->setRootParentId('0');
        $threadComment->setStatus(ThreadCommentState::AUDIT_PASS);
        $em->persist($threadComment);

        $user = $this->createNormalUser('test_user_' . uniqid());
        $em->flush(); // 确保关联实体被持久化

        $entity = new ThreadCommentLike();
        $entity->setThreadComment($threadComment);
        $entity->setUser($user);
        $entity->setStatus(1);

        return $entity;
    }
}
