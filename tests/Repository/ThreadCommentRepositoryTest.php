<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadComment;
use ForumBundle\Enum\ThreadCommentState;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadCommentRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadCommentRepository::class)]
#[RunTestsInSeparateProcesses]
final class ThreadCommentRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<ThreadComment>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ThreadCommentRepository::class);
    }

    protected function createNewEntity(): object
    {
        $thread = new Thread();
        $thread->setTitle('Test Thread ' . uniqid());
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);

        $entity = new ThreadComment();
        $entity->setThread($thread);
        $entity->setContent('Test Comment ' . uniqid());
        $entity->setParentId('0');
        $entity->setRootParentId('0');
        $entity->setStatus(ThreadCommentState::AUDIT_PASS);

        return $entity;
    }

    public function testFindByUserIsNullShouldReturnEntitiesWithNullField(): void
    {
        // 先清理可能存在的数据
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadCommentRepository::class);

        // 清除所有现有评论
        $existingComments = $repository->findAll();
        foreach ($existingComments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->flush();

        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);

        $comment1 = new ThreadComment();
        $comment1->setThread($thread);
        $comment1->setContent('Comment without user');
        $comment1->setParentId('0');
        // user 保持为 null

        $entityManager->persist($thread);
        $entityManager->persist($comment1);
        $entityManager->flush();

        $results = $repository->findBy(['user' => null]);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals('Comment without user', $results[0]->getContent());
        $this->assertNull($results[0]->getUser());
    }

    public function testCountByUserIsNullShouldReturnCorrectCount(): void
    {
        // 先清理可能存在的数据
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadCommentRepository::class);

        // 清除所有现有评论
        $existingComments = $repository->findAll();
        foreach ($existingComments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->flush();

        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);

        $comment1 = new ThreadComment();
        $comment1->setThread($thread);
        $comment1->setContent('Comment without user 1');
        $comment1->setParentId('0');

        $comment2 = new ThreadComment();
        $comment2->setThread($thread);
        $comment2->setContent('Comment without user 2');
        $comment2->setParentId('0');

        $entityManager->persist($thread);
        $entityManager->persist($comment1);
        $entityManager->persist($comment2);
        $entityManager->flush();

        $count = $repository->count(['user' => null]);

        $this->assertEquals(2, $count);
    }

    public function testFindByReplyUserIsNullShouldReturnEntitiesWithNullField(): void
    {
        // 先清理可能存在的数据
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadCommentRepository::class);

        // 清除所有现有评论
        $existingComments = $repository->findAll();
        foreach ($existingComments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->flush();

        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);

        $comment1 = new ThreadComment();
        $comment1->setThread($thread);
        $comment1->setContent('Comment without reply user');
        $comment1->setParentId('0');
        // replyUser 保持为 null

        $entityManager->persist($thread);
        $entityManager->persist($comment1);
        $entityManager->flush();

        $results = $repository->findBy(['replyUser' => null]);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals('Comment without reply user', $results[0]->getContent());
        $this->assertNull($results[0]->getReplyUser());
    }

    public function testCountByReplyUserIsNullShouldReturnCorrectCount(): void
    {
        // 先清理可能存在的数据
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadCommentRepository::class);

        // 清除所有现有评论
        $existingComments = $repository->findAll();
        foreach ($existingComments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->flush();

        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);

        $comment1 = new ThreadComment();
        $comment1->setThread($thread);
        $comment1->setContent('Comment without reply user 1');
        $comment1->setParentId('0');

        $comment2 = new ThreadComment();
        $comment2->setThread($thread);
        $comment2->setContent('Comment without reply user 2');
        $comment2->setParentId('0');

        $entityManager->persist($thread);
        $entityManager->persist($comment1);
        $entityManager->persist($comment2);
        $entityManager->flush();

        $count = $repository->count(['replyUser' => null]);

        $this->assertEquals(2, $count);
    }

    public function testFindByAssociationShouldReturnEntitiesWithMatchingAssociation(): void
    {
        // 先清理可能存在的数据
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadCommentRepository::class);

        // 清除所有现有评论
        $existingComments = $repository->findAll();
        foreach ($existingComments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->flush();

        $thread1 = new Thread();
        $thread1->setTitle('Thread A');
        $thread1->setContent('Content A');
        $thread1->setStatus(ThreadState::AUDIT_PASS);

        $thread2 = new Thread();
        $thread2->setTitle('Thread B');
        $thread2->setContent('Content B');
        $thread2->setStatus(ThreadState::AUDIT_PASS);

        $entityManager->persist($thread1);
        $entityManager->persist($thread2);

        // 为 thread1 创建 3 个评论
        for ($i = 1; $i <= 3; ++$i) {
            $comment = new ThreadComment();
            $comment->setThread($thread1);
            $comment->setContent("Comment {$i} for Thread A");
            $comment->setParentId('0');
            $entityManager->persist($comment);
        }

        // 为 thread2 创建 2 个评论
        for ($i = 1; $i <= 2; ++$i) {
            $comment = new ThreadComment();
            $comment->setThread($thread2);
            $comment->setContent("Comment {$i} for Thread B");
            $comment->setParentId('0');
            $entityManager->persist($comment);
        }

        $entityManager->flush();

        $results = $repository->findBy(['thread' => $thread1]);

        $this->assertIsArray($results);
        $this->assertCount(3, $results);
        $this->assertContainsOnlyInstancesOf(ThreadComment::class, $results);

        foreach ($results as $result) {
            $this->assertEquals($thread1->getId(), $result->getThread()?->getId());
            $this->assertStringContainsString('Thread A', $result->getContent() ?? '');
        }
    }

    public function testCountByAssociationShouldReturnCorrectCount(): void
    {
        // 先清理可能存在的数据
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadCommentRepository::class);

        // 清除所有现有评论
        $existingComments = $repository->findAll();
        foreach ($existingComments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->flush();

        $thread1 = new Thread();
        $thread1->setTitle('Thread A');
        $thread1->setContent('Content A');
        $thread1->setStatus(ThreadState::AUDIT_PASS);

        $thread2 = new Thread();
        $thread2->setTitle('Thread B');
        $thread2->setContent('Content B');
        $thread2->setStatus(ThreadState::AUDIT_PASS);

        $entityManager->persist($thread1);
        $entityManager->persist($thread2);

        // 为 thread1 创建 4 个评论
        for ($i = 0; $i < 4; ++$i) {
            $comment = new ThreadComment();
            $comment->setThread($thread1);
            $comment->setContent("Comment {$i} for Thread A");
            $comment->setParentId('0');
            $entityManager->persist($comment);
        }

        // 为 thread2 创建 2 个评论
        for ($i = 0; $i < 2; ++$i) {
            $comment = new ThreadComment();
            $comment->setThread($thread2);
            $comment->setContent("Comment {$i} for Thread B");
            $comment->setParentId('0');
            $entityManager->persist($comment);
        }

        $entityManager->flush();

        $count = $repository->count(['thread' => $thread1]);

        $this->assertEquals(4, $count);
    }
}
