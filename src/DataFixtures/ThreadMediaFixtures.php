<?php

namespace ForumBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadMedia;

class ThreadMediaFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
        $mediaUrls = [
            [
                'type' => 'image',
                'path' => 'https://img10.360buyimg.com/n1/jfs/t1/197122/8/12345/123456/62a1b2c3E1234567a/1234567890abcdef.jpg',
                'thumbnail' => 'https://img10.360buyimg.com/n1/jfs/t1/197122/8/12345/123456/62a1b2c3E1234567a/1234567890abcdef.jpg',
            ],
            [
                'type' => 'image',
                'path' => 'https://img11.360buyimg.com/n1/jfs/t1/198765/9/23456/234567/62b2c3d4E2345678b/2345678901bcdefg.jpg',
                'thumbnail' => 'https://img11.360buyimg.com/n1/jfs/t1/198765/9/23456/234567/62b2c3d4E2345678b/2345678901bcdefg.jpg',
            ],
            [
                'type' => 'video',
                'path' => 'https://img12.360buyimg.com/n1/jfs/t1/example/video/sample-video-1.mp4',
                'thumbnail' => 'https://img12.360buyimg.com/n1/jfs/t1/example/video/sample-video-1-thumb.jpg',
            ],
        ];

        // 使用引用获取帖子
        for ($threadIndex = 0; $threadIndex < 3; ++$threadIndex) {
            $threadReference = 'thread_' . $threadIndex;
            if (!$this->hasReference($threadReference, Thread::class)) {
                continue;
            }
            $thread = $this->getReference($threadReference, Thread::class);
            // 为每个帖子添加额外的媒体文件
            $mediaCount = ($threadIndex % 3) + 1; // 1-3个媒体文件

            for ($i = 0; $i < $mediaCount; ++$i) {
                $mediaData = $mediaUrls[$i % count($mediaUrls)];

                $threadMedia = new ThreadMedia();
                $threadMedia->setCreateTime(CarbonImmutable::now()->subMinutes($i * 10));
                $threadMedia->setType($mediaData['type']);
                $threadMedia->setPath($mediaData['path']);
                $threadMedia->setThumbnail($mediaData['thumbnail']);

                $thread->addThreadMedium($threadMedia);
                $manager->persist($threadMedia);
            }
        }

        $manager->flush();
    }
}
