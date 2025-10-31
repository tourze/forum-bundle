<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\VisitStat;

class VisitStatFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['forum'];
    }

    public function getDependencies(): array
    {
        return [
            ThreadFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 使用引用获取帖子
        for ($index = 0; $index < 2; ++$index) {
            $threadReference = 'thread-' . $index;
            if (!$this->hasReference($threadReference, Thread::class)) {
                continue;
            }
            $thread = $this->getReference($threadReference, Thread::class);
            $visitStat = new VisitStat();
            $visitStat->setThread($thread);

            // 设置不同的统计数据
            $visitStat->setLikeTotal(10 + $index * 5);
            $visitStat->setShareTotal(5 + $index * 2);
            $visitStat->setCommentTotal(15 + $index * 3);
            $visitStat->setVisitTotal(100 + $index * 50);
            $visitStat->setCollectCount(8 + $index * 2);
            $visitStat->setLikeRank($index + 1);
            $visitStat->setShareRank($index + 1);
            $visitStat->setCommentRank($index + 1);
            $visitStat->setVisitRank($index + 1);
            $visitStat->setCollectRank($index + 1);

            $manager->persist($visitStat);
        }

        $manager->flush();
    }
}
