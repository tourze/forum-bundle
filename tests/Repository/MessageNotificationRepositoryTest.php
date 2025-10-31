<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Enum\MessageType;
use ForumBundle\Repository\MessageNotificationRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(MessageNotificationRepository::class)]
#[RunTestsInSeparateProcesses]
final class MessageNotificationRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<MessageNotification>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(MessageNotificationRepository::class);
    }

    protected function createNewEntity(): object
    {
        $user = $this->createNormalUser('notification-user-' . uniqid());
        $sender = $this->createNormalUser('notification-sender-' . uniqid());

        $entity = new MessageNotification();
        $entity->setUser($user);
        $entity->setSender($sender);
        $entity->setContent('Test notification content ' . uniqid());
        $entity->setType(MessageType::SYSTEM_NOTIFICATION);
        $entity->setTargetId('target-' . uniqid());
        $entity->setReadStatus(0);

        return $entity;
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $entity1 = new MessageNotification();
        $entity1->setContent('B Notification');
        $entity1->setType(MessageType::SYSTEM_NOTIFICATION);
        $entity1->setTargetId('123');
        $entity1->setReadStatus(0);

        $entity2 = new MessageNotification();
        $entity2->setContent('A Notification');
        $entity2->setType(MessageType::SYSTEM_NOTIFICATION);
        $entity2->setTargetId('456');
        $entity2->setReadStatus(0);

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($entity1);
        $entityManager->persist($entity2);
        $entityManager->flush();

        $repository = self::getService(MessageNotificationRepository::class);
        $result = $repository->findOneBy(['readStatus' => 0], ['content' => 'ASC']);

        $this->assertNotNull($result);
        $this->assertEquals('A Notification', $result->getContent());
    }
}
