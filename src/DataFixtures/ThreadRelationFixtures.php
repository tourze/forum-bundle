<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadRelation;
use ForumBundle\Enum\ThreadRelationType;

class ThreadRelationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
        for ($index = 0; $index < 3; ++$index) {
            $threadReference = 'thread_' . $index;
            if (!$this->hasReference($threadReference, Thread::class)) {
                continue;
            }
            $thread = $this->getReference($threadReference, Thread::class);

            // 为每个帖子创建一个关联到CMS文章的关系
            $relation = new ThreadRelation();
            $relation->setThread($thread);
            $relation->setSourceType(ThreadRelationType::CMS_ENTITY);
            $relation->setSourceId('article_' . (1000 + $index)); // 模拟文章ID
            $manager->persist($relation);

            // 第一个帖子额外关联一个文章
            if (0 === $index) {
                $relation2 = new ThreadRelation();
                $relation2->setThread($thread);
                $relation2->setSourceType(ThreadRelationType::CMS_ENTITY);
                $relation2->setSourceId('article_2000');
                $manager->persist($relation2);
            }
        }

        $manager->flush();
    }
}
