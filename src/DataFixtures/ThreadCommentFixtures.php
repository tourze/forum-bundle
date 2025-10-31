<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadComment;
use ForumBundle\Enum\ThreadCommentState;
use Tourze\UserServiceContracts\UserManagerInterface;

class ThreadCommentFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const COMMENT_1_REFERENCE = 'comment-1';

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
        // 使用 loadUserByIdentifier 方法来获取用户
        try {
            $user1 = $this->userManager?->loadUserByIdentifier('1');
            $user2 = $this->userManager?->loadUserByIdentifier('2');
        } catch (\Exception $e) {
            $user1 = null;
            $user2 = null;
        }

        if (null !== $user1 && null !== $user2) {
            // 使用引用获取第一个帖子
            if ($this->hasReference(ThreadFixtures::THREAD_0_REFERENCE, Thread::class)) {
                $thread = $this->getReference(ThreadFixtures::THREAD_0_REFERENCE, Thread::class);
                // 创建更多评论
                $comment1 = new ThreadComment();
                $comment1->setThread($thread);
                $comment1->setUser($user2);
                $comment1->setParentId('0');
                $comment1->setRootParentId('0');
                $comment1->setStatus(ThreadCommentState::AUDIT_PASS);
                $comment1->setContent('这个话题很有意思！');
                $manager->persist($comment1);
                $this->addReference(self::COMMENT_1_REFERENCE, $comment1);

                // 创建回复评论
                $comment2 = new ThreadComment();
                $comment2->setThread($thread);
                $comment2->setUser($user1);
                $comment2->setParentId($comment1->getId() ?? '0');
                $comment2->setRootParentId($comment1->getId());
                $comment2->setStatus(ThreadCommentState::AUDIT_PASS);
                $comment2->setContent('同意你的观点！');
                $manager->persist($comment2);

                // 创建删除评论示例
                $comment3 = new ThreadComment();
                $comment3->setThread($thread);
                $comment3->setUser($user2);
                $comment3->setParentId('0');
                $comment3->setRootParentId('0');
                $comment3->setStatus(ThreadCommentState::SYSTEM_DELETE);
                $comment3->setContent('已删除的评论内容');
                $manager->persist($comment3);
            }
        }

        $manager->flush();
    }
}
