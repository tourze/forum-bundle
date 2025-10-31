<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\SortingRule;
use ForumBundle\Repository\SortingRuleRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SortingRuleRepository::class)]
#[RunTestsInSeparateProcesses]
final class SortingRuleRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 没有特殊设置需求
    }

    /**
     * @return ServiceEntityRepository<SortingRule>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(SortingRuleRepository::class);
    }

    protected function createNewEntity(): object
    {
        $dimension = new Dimension();
        $dimension->setTitle('Test Dimension ' . uniqid());
        $dimension->setCode('test_dim_' . uniqid());
        $dimension->setValid(true);

        $entity = new SortingRule();
        $entity->setDimension($dimension);
        $entity->setTitle('Test Sorting Rule ' . uniqid());
        $entity->setFormula('score * 2 + views');

        return $entity;
    }
}
