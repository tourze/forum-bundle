<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\ForumShareRecord;
use ForumBundle\Repository\ForumShareRecordRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ForumShareRecordRepository::class)]
#[RunTestsInSeparateProcesses]
final class ForumShareRecordRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<ForumShareRecord>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ForumShareRecordRepository::class);
    }

    protected function createNewEntity(): object
    {
        $user = $this->createNormalUser('sharing-user-' . uniqid());

        $entity = new ForumShareRecord();
        $entity->setUser($user);
        $entity->setType('thread');
        $entity->setSourceId('source-' . uniqid());

        return $entity;
    }

    public function testFindByUserAsNullShouldReturnEntitiesWithNullField(): void
    {
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(ForumShareRecordRepository::class);

        // 创建一个测试记录，user保持为null
        $entity = new ForumShareRecord();
        $entity->setType('thread');
        $entity->setSourceId('test-null-user-' . uniqid());
        // user 保持为 null

        $entityManager->persist($entity);
        $entityManager->flush();

        // 查询user为null的记录
        $results = $repository->findBy(['user' => null]);

        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, $results); // 至少有一条，可能有其他null记录

        // 验证至少有一条是我们刚创建的记录
        $found = false;
        foreach ($results as $result) {
            if ($result->getSourceId() === $entity->getSourceId()) {
                $this->assertNull($result->getUser());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, '应该找到刚创建的测试记录');
    }
}
