<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadDimension;

class ThreadDimensionFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['forum'];
    }

    public function getDependencies(): array
    {
        return [
            ThreadFixtures::class,
            DimensionFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 使用引用获取帖子
        $threadReferences = [
            ThreadFixtures::THREAD_0_REFERENCE,
            ThreadFixtures::THREAD_1_REFERENCE,
            ThreadFixtures::THREAD_2_REFERENCE,
        ];

        foreach ($threadReferences as $index => $threadReference) {
            if (!$this->hasReference($threadReference, Thread::class)) {
                continue;
            }
            $thread = $this->getReference($threadReference, Thread::class);
            // 热度维度
            $hotDimension = new ThreadDimension();
            $hotDimension->setThread($thread);
            $hotDimension->setDimension($this->getReference(DimensionFixtures::DIMENSION_HOT_REFERENCE, Dimension::class));
            $hotDimension->setValue(100 + $index * 50); // 不同的热度值
            $hotDimension->setContext([
                'likes' => 10 + $index * 5,
                'comments' => 5 + $index * 2,
                'views' => 100 + $index * 20,
            ]);
            $manager->persist($hotDimension);

            // 时间维度
            $timeDimension = new ThreadDimension();
            $timeDimension->setThread($thread);
            $timeDimension->setDimension($this->getReference(DimensionFixtures::DIMENSION_TIME_REFERENCE, Dimension::class));
            $timeDimension->setValue(time() - $index * 3600); // 时间戳
            $timeDimension->setContext([
                'created_at' => $thread->getCreateTime()?->format('Y-m-d H:i:s'),
            ]);
            $manager->persist($timeDimension);

            // 质量维度
            $qualityDimension = new ThreadDimension();
            $qualityDimension->setThread($thread);
            $qualityDimension->setDimension($this->getReference(DimensionFixtures::DIMENSION_QUALITY_REFERENCE, Dimension::class));
            $qualityDimension->setValue(80 + $index * 10); // 质量分数
            $qualityDimension->setContext([
                'content_length' => mb_strlen($thread->getContent() ?? ''),
                'has_media' => $thread->getThreadMedia()->count() > 0,
            ]);
            $manager->persist($qualityDimension);
        }

        $manager->flush();
    }
}
