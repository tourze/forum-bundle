<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCollect;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadCollectRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadCollectRepository::class)]
#[RunTestsInSeparateProcesses]
final class ThreadCollectRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<ThreadCollect>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ThreadCollectRepository::class);
    }

    protected function createNewEntity(): object
    {
        $thread = $this->createThread();
        $user = $this->createNormalUser('test_user');

        // 手动持久化关联的 Thread 实体
        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($thread);
        $entityManager->flush();

        $entity = new ThreadCollect();
        $entity->setThread($thread);
        $entity->setUser($user);
        $entity->setValid(true);

        return $entity;
    }

    public function testFindByAssociationShouldReturnEntitiesWithMatchingAssociation(): void
    {
        // 先清理可能存在的数据
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadCollectRepository::class);

        // 清除所有现有收藏记录
        $existingCollects = $repository->findAll();
        foreach ($existingCollects as $collect) {
            $entityManager->remove($collect);
        }
        $entityManager->flush();

        $thread1 = $this->createThread();
        $thread1->setTitle('Thread A');
        $thread2 = $this->createThread();
        $thread2->setTitle('Thread B');

        $user = $this->createNormalUser('test_user');

        $entityManager->persist($thread1);
        $entityManager->persist($thread2);

        // 为 thread1 创建 3 个收藏记录
        for ($i = 1; $i <= 3; ++$i) {
            $collect = new ThreadCollect();
            $collect->setThread($thread1);
            $collect->setUser($user);
            $collect->setValid(true);
            $entityManager->persist($collect);
        }

        // 为 thread2 创建 2 个收藏记录
        for ($i = 1; $i <= 2; ++$i) {
            $collect = new ThreadCollect();
            $collect->setThread($thread2);
            $collect->setUser($user);
            $collect->setValid(true);
            $entityManager->persist($collect);
        }

        $entityManager->flush();

        $results = $repository->findBy(['thread' => $thread1]);

        $this->assertIsArray($results);
        $this->assertCount(3, $results);
        $this->assertContainsOnlyInstancesOf(ThreadCollect::class, $results);

        foreach ($results as $result) {
            $this->assertEquals($thread1->getId(), $result->getThread()?->getId());
        }
    }

    public function testCountByAssociationShouldReturnCorrectCount(): void
    {
        // 先清理可能存在的数据
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ThreadCollectRepository::class);

        // 清除所有现有收藏记录
        $existingCollects = $repository->findAll();
        foreach ($existingCollects as $collect) {
            $entityManager->remove($collect);
        }
        $entityManager->flush();

        $thread1 = $this->createThread();
        $thread1->setTitle('Thread A');
        $thread2 = $this->createThread();
        $thread2->setTitle('Thread B');

        $user = $this->createNormalUser('test_user');

        $entityManager->persist($thread1);
        $entityManager->persist($thread2);

        // 为 thread1 创建 4 个收藏记录
        for ($i = 0; $i < 4; ++$i) {
            $collect = new ThreadCollect();
            $collect->setThread($thread1);
            $collect->setUser($user);
            $collect->setValid(true);
            $entityManager->persist($collect);
        }

        // 为 thread2 创建 2 个收藏记录
        for ($i = 0; $i < 2; ++$i) {
            $collect = new ThreadCollect();
            $collect->setThread($thread2);
            $collect->setUser($user);
            $collect->setValid(true);
            $entityManager->persist($collect);
        }

        $entityManager->flush();

        $count = $repository->count(['thread' => $thread1]);

        $this->assertEquals(4, $count);
    }

    private function createThread(): Thread
    {
        $thread = new Thread();
        $thread->setTitle('Test Thread');
        $thread->setContent('Test Content');
        $thread->setStatus(ThreadState::AUDIT_PASS);

        return $thread;
    }
}
