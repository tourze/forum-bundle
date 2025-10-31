<?php

namespace ForumBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadComment;
use ForumBundle\Entity\ThreadLike;
use ForumBundle\Entity\ThreadMedia;
use ForumBundle\Enum\ThreadCommentState;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use Tourze\UserServiceContracts\UserManagerInterface;

class ThreadFixtures extends Fixture implements FixtureGroupInterface
{
    public const THREAD_0_REFERENCE = 'thread-0';
    public const THREAD_1_REFERENCE = 'thread-1';
    public const THREAD_2_REFERENCE = 'thread-2';

    public function __construct(
        private readonly ?UserManagerInterface $userManager = null,
    ) {
    }

    public static function getGroups(): array
    {
        return ['forum'];
    }

    public function load(ObjectManager $manager): void
    {
        // 使用 loadUserByIdentifier 方法来获取用户
        try {
            $bizUser = $this->userManager?->loadUserByIdentifier('1');
        } catch (\Exception $e) {
            $bizUser = null;
        }

        // 第一个帖子
        $thread1 = new Thread();
        $thread1->setContent('端午节（屈原故里端午习俗），流行于湖北省宜昌市、秭归县的传统民俗，国家级非物质文化遗产之一。"五月五（农历），过端午。"端午节是中华民族的传统节日');
        if (null !== $bizUser) {
            $thread1->setUser($bizUser);
        }
        $thread1->setCoverPicture('https://img10.360buyimg.com/n1/jfs/t1/129094/15/27735/215398/62621705Ec75b3a2a/932bba946a3f5521.jpg');
        $thread1->setCreateTime(CarbonImmutable::now());
        $thread1->setUpdateTime(CarbonImmutable::now());
        $thread1->setSortNumber(0);
        $thread1->setIdentify('');
        $thread1->setType(ThreadType::USER_THREAD);
        $thread1->setStatus(ThreadState::AUDIT_PASS);

        $threadMedia1 = new ThreadMedia();
        $threadMedia1->setCreateTime(CarbonImmutable::now());
        $threadMedia1->setType('image');
        $threadMedia1->setPath('https://img10.360buyimg.com/n1/jfs/t1/129094/15/27735/215398/62621705Ec75b3a2a/932bba946a3f5521.jpg');
        $threadMedia1->setThumbnail('https://img10.360buyimg.com/n1/jfs/t1/129094/15/27735/215398/62621705Ec75b3a2a/932bba946a3f5521.jpg');
        $thread1->addThreadMedium($threadMedia1);

        // 第一个帖子的点赞
        if (null !== $bizUser) {
            $threadLike1 = new ThreadLike();
            $threadLike1->setStatus(1);
            $threadLike1->setUser($bizUser);
            $threadLike1->setThread($thread1);
            $threadLike1->setCreateTime(CarbonImmutable::now());
            $manager->persist($threadLike1);
        }

        // 第一个帖子的评论
        if (null !== $bizUser) {
            $threadComment1 = new ThreadComment();
            $threadComment1->setThread($thread1);
            $threadComment1->setUser($bizUser);
            $threadComment1->setParentId('0');
            $threadComment1->setRootParentId('0');
            $threadComment1->setStatus(ThreadCommentState::AUDIT_PASS);
            $threadComment1->setContent('端午安康！');
            $manager->persist($threadComment1);

            $threadComment2 = new ThreadComment();
            $threadComment2->setThread($thread1);
            $threadComment2->setUser($bizUser);
            $threadComment2->setParentId('0');
            $threadComment2->setRootParentId('0');
            $threadComment2->setStatus(ThreadCommentState::AUDIT_PASS);
            $threadComment2->setContent('节日快乐！');
            $manager->persist($threadComment2);
        }

        $manager->persist($thread1);
        $this->addReference(self::THREAD_0_REFERENCE, $thread1);

        // 第二个帖子
        $thread2 = new Thread();
        $thread2->setContent('这是可爱的小猫咪');
        if (null !== $bizUser) {
            $thread2->setUser($bizUser);
        }
        $thread2->setCoverPicture('https://img13.360buyimg.com/n1/jfs/t1/217032/28/31706/47944/647da85aF2eae913f/5584b23380c22119.jpg');
        $thread2->setCreateTime(CarbonImmutable::now());
        $thread2->setUpdateTime(CarbonImmutable::now());
        $thread2->setSortNumber(0);
        $thread2->setIdentify('');
        $thread2->setType(ThreadType::USER_THREAD);
        $thread2->setStatus(ThreadState::AUDIT_PASS);

        $threadMedia2 = new ThreadMedia();
        $threadMedia2->setCreateTime(CarbonImmutable::now());
        $threadMedia2->setType('image');
        $threadMedia2->setPath('https://img13.360buyimg.com/n1/jfs/t1/217032/28/31706/47944/647da85aF2eae913f/5584b23380c22119.jpg');
        $threadMedia2->setThumbnail('https://img13.360buyimg.com/n1/jfs/t1/217032/28/31706/47944/647da85aF2eae913f/5584b23380c22119.jpg');
        $thread2->addThreadMedium($threadMedia2);

        // 第二个帖子的点赞
        if (null !== $bizUser) {
            $threadLike2 = new ThreadLike();
            $threadLike2->setStatus(1);
            $threadLike2->setUser($bizUser);
            $threadLike2->setThread($thread2);
            $threadLike2->setCreateTime(CarbonImmutable::now());
            $manager->persist($threadLike2);
        }

        // 第二个帖子的评论
        if (null !== $bizUser) {
            $threadComment3 = new ThreadComment();
            $threadComment3->setThread($thread2);
            $threadComment3->setUser($bizUser);
            $threadComment3->setParentId('0');
            $threadComment3->setRootParentId('0');
            $threadComment3->setStatus(ThreadCommentState::AUDIT_PASS);
            $threadComment3->setContent('很可爱！');
            $manager->persist($threadComment3);

            $threadComment4 = new ThreadComment();
            $threadComment4->setThread($thread2);
            $threadComment4->setUser($bizUser);
            $threadComment4->setParentId('0');
            $threadComment4->setRootParentId('0');
            $threadComment4->setStatus(ThreadCommentState::AUDIT_PASS);
            $threadComment4->setContent('小猫咪！');
            $manager->persist($threadComment4);
        }

        $manager->persist($thread2);
        $this->addReference(self::THREAD_1_REFERENCE, $thread2);

        $manager->flush();
    }
}
