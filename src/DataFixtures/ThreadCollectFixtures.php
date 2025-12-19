<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCollect;
use Tourze\UserServiceContracts\UserManagerInterface;

class ThreadCollectFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct(
        private readonly ?UserManagerInterface $userManager = null,
    ) {
    }

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
        // 使用 loadUserByIdentifier 方法来获取用户，使用确定存在的管理员用户
        try {
            $bizUser = $this->userManager?->loadUserByIdentifier('admin');
        } catch (\Exception $e) {
            $bizUser = null;
        }

        if (null !== $bizUser) {
            // 使用引用而不是查询数据库
            for ($i = 0; $i < 2; ++$i) {
                $threadReference = 'thread-' . $i;
                if ($this->hasReference($threadReference, Thread::class)) {
                    $thread = $this->getReference($threadReference, Thread::class);

                    $collect = new ThreadCollect();
                    $collect->setThread($thread);
                    $collect->setUser($bizUser);
                    $collect->setValid(true);
                    $manager->persist($collect);
                }
            }
        }

        $manager->flush();
    }
}
