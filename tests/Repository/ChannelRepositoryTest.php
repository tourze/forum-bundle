<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use ForumBundle\Entity\Channel;
use ForumBundle\Repository\ChannelRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ChannelRepository::class)]
#[RunTestsInSeparateProcesses]
final class ChannelRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<Channel>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ChannelRepository::class);
    }

    protected function createNewEntity(): object
    {
        $entity = new Channel();
        $entity->setTitle('Test Channel ' . uniqid());
        $entity->setValid(true);

        return $entity;
    }
}
