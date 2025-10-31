<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Channel;
use ForumBundle\Entity\ChannelSubscribe;
use ForumBundle\Repository\ChannelSubscribeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ChannelSubscribeRepository::class)]
#[RunTestsInSeparateProcesses]
final class ChannelSubscribeRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<ChannelSubscribe>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ChannelSubscribeRepository::class);
    }

    protected function createNewEntity(): object
    {
        $user = $this->createNormalUser('test-user-' . uniqid());

        // 直接在EntityManager中持久化Channel，确保在跨进程测试中可序列化
        $em = self::getService(EntityManagerInterface::class);
        $channel = new Channel();
        $channel->setTitle('Test Channel ' . uniqid());
        $channel->setValid(true);
        $em->persist($channel);
        $em->flush(); // 确保Channel被持久化并有ID

        $entity = new ChannelSubscribe();
        $entity->setUser($user);
        $entity->setChannel($channel);
        $entity->setValid(true);

        return $entity;
    }

    public function testFindOneByAssociationChannelShouldReturnMatchingEntity(): void
    {
        $user1 = $this->createNormalUser('user-1');
        $user2 = $this->createNormalUser('user-2');

        $channel1 = new Channel();
        $channel1->setTitle('Channel A');
        $channel1->setValid(true);

        $channel2 = new Channel();
        $channel2->setTitle('Channel B');
        $channel2->setValid(true);

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($channel1);
        $entityManager->persist($channel2);
        $entityManager->flush();

        $entity1 = new ChannelSubscribe();
        $entity1->setUser($user1);
        $entity1->setChannel($channel1);
        $entity1->setValid(true);

        $entity2 = new ChannelSubscribe();
        $entity2->setUser($user2);
        $entity2->setChannel($channel2);
        $entity2->setValid(true);

        $entityManager->persist($entity1);
        $entityManager->persist($entity2);
        $entityManager->flush();

        $repository = self::getService(ChannelSubscribeRepository::class);
        $result = $repository->findOneBy(['channel' => $channel1]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(ChannelSubscribe::class, $result);
        $this->assertEquals($channel1->getId(), $result->getChannel()?->getId());
    }

    public function testCountByAssociationChannelShouldReturnCorrectNumber(): void
    {
        $channel1 = new Channel();
        $channel1->setTitle('Channel A');
        $channel1->setValid(true);

        $channel2 = new Channel();
        $channel2->setTitle('Channel B');
        $channel2->setValid(true);

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($channel1);
        $entityManager->persist($channel2);
        $entityManager->flush();

        // 为 channel1 创建 4 个订阅（不同用户）
        for ($i = 0; $i < 4; ++$i) {
            $user = $this->createNormalUser('user-channel1-' . $i);
            $entity = new ChannelSubscribe();
            $entity->setUser($user);
            $entity->setChannel($channel1);
            $entity->setValid(0 === $i % 2); // 交替设置有效性
            $entityManager->persist($entity);
        }

        // 为 channel2 创建 2 个订阅（不同用户）
        for ($i = 0; $i < 2; ++$i) {
            $user = $this->createNormalUser('user-channel2-' . $i);
            $entity = new ChannelSubscribe();
            $entity->setUser($user);
            $entity->setChannel($channel2);
            $entity->setValid(true);
            $entityManager->persist($entity);
        }

        $entityManager->flush();

        $repository = self::getService(ChannelSubscribeRepository::class);
        $count = $repository->count(['channel' => $channel1]);

        $this->assertEquals(4, $count);
    }
}
