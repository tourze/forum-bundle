<?php

namespace ForumBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadLike;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;

class ThreadLikeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
        $users = $this->loadUsers();
        if ([] === $users) {
            return;
        }

        $this->createThreadLikes($manager, $users);
        $manager->flush();
    }

    /**
     * @return UserInterface[]
     */
    private function loadUsers(): array
    {
        try {
            $user1 = $this->userManager?->loadUserByIdentifier('1');
            $user2 = $this->userManager?->loadUserByIdentifier('2');
            $user3 = $this->userManager?->loadUserByIdentifier('3');

            $users = [];
            if (null !== $user1) {
                $users[] = $user1;
            }
            if (null !== $user2) {
                $users[] = $user2;
            }
            if (null !== $user3) {
                $users[] = $user3;
            }

            return $users;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param UserInterface[] $users
     */
    private function createThreadLikes(ObjectManager $manager, array $users): void
    {
        // 使用引用获取帖子
        for ($index = 0; $index < 3; ++$index) {
            $threadReference = 'thread_' . $index;
            if (!$this->hasReference($threadReference, Thread::class)) {
                continue;
            }

            $thread = $this->getReference($threadReference, Thread::class);

            // 用户1点赞所有帖子
            if (isset($users[0])) {
                $like1 = new ThreadLike();
                $like1->setStatus(1);
                $like1->setUser($users[0]);
                $like1->setThread($thread);
                $like1->setCreateTime(CarbonImmutable::now()->subHours($index));
                $manager->persist($like1);
            }

            // 用户2点赞偶数索引帖子
            if (isset($users[1]) && 0 === $index % 2) {
                $like2 = new ThreadLike();
                $like2->setStatus(1);
                $like2->setUser($users[1]);
                $like2->setThread($thread);
                $like2->setCreateTime(CarbonImmutable::now()->subHours($index * 2));
                $manager->persist($like2);
            }

            // 用户3只点赞第一个帖子
            if (isset($users[2]) && 0 === $index) {
                $like3 = new ThreadLike();
                $like3->setStatus(1);
                $like3->setUser($users[2]);
                $like3->setThread($thread);
                $like3->setCreateTime(CarbonImmutable::now());
                $manager->persist($like3);
            }
        }
    }
}
