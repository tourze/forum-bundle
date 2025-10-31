<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\ThreadComment;
use ForumBundle\Entity\ThreadCommentLike;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;

class ThreadCommentLikeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
            ThreadCommentFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->loadUsers();
        if ([] === $users) {
            return;
        }

        $this->createCommentLikes($manager, $users);
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

            if (null !== $user1 && null !== $user2) {
                return [$user1, $user2];
            }
        } catch (\Exception $e) {
            // 用户加载失败
        }

        return [];
    }

    /**
     * @param UserInterface[] $users
     */
    private function createCommentLikes(ObjectManager $manager, array $users): void
    {
        // 使用引用获取评论
        if ($this->hasReference(ThreadCommentFixtures::COMMENT_1_REFERENCE, ThreadComment::class)) {
            $comment = $this->getReference(ThreadCommentFixtures::COMMENT_1_REFERENCE, ThreadComment::class);

            // 用户1点赞
            $like1 = new ThreadCommentLike();
            $like1->setThreadComment($comment);
            $like1->setUser($users[0]);
            $like1->setStatus(1);
            $manager->persist($like1);

            // 用户2也点赞
            $like2 = new ThreadCommentLike();
            $like2->setThreadComment($comment);
            $like2->setUser($users[1]);
            $like2->setStatus(1);
            $manager->persist($like2);
        }
    }
}
