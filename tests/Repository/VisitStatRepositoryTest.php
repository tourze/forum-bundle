<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use ForumBundle\Entity\VisitStat;
use ForumBundle\Repository\VisitStatRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(VisitStatRepository::class)]
#[RunTestsInSeparateProcesses]
final class VisitStatRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    protected function createNewEntity(): object
    {
        // 确保不创建关联的Thread，避免复杂的双向关联在跨进程测试中引起序列化问题
        $visitStat = new VisitStat();
        $visitStat->setLikeTotal(10);
        $visitStat->setShareTotal(5);
        $visitStat->setCommentTotal(20);
        $visitStat->setVisitTotal(100);
        $visitStat->setCollectCount(15);
        // 不设置thread关联，保持为null

        return $visitStat;
    }

    /**
     * @return ServiceEntityRepository<VisitStat>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(VisitStatRepository::class);
    }
}
