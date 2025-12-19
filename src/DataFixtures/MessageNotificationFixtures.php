<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Enum\MessageType;
use Tourze\UserServiceContracts\UserManagerInterface;

class MessageNotificationFixtures extends Fixture implements FixtureGroupInterface
{
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
        // 使用 loadUserByIdentifier 方法来获取用户，使用确定存在的用户
        try {
            $user1 = $this->userManager?->loadUserByIdentifier('admin');
            $user2 = $this->userManager?->loadUserByIdentifier('user1');
        } catch (\Exception $e) {
            $user1 = null;
            $user2 = null;
        }

        if (null !== $user1 && null !== $user2) {
            // 系统通知
            $notification1 = new MessageNotification();
            $notification1->setUser($user1);
            $notification1->setContent('欢迎加入论坛社区！');
            $notification1->setType(MessageType::SYSTEM_NOTIFICATION);
            $notification1->setReadStatus(0);
            $notification1->setDeleted(0);
            $notification1->setTargetId('0');
            $manager->persist($notification1);

            // 回复通知
            $notification2 = new MessageNotification();
            $notification2->setUser($user1);
            $notification2->setSender($user2);
            $notification2->setContent('回复了你的评论');
            $notification2->setType(MessageType::REPLY);
            $notification2->setReadStatus(0);
            $notification2->setDeleted(0);
            $notification2->setTargetId('1234567890');
            $manager->persist($notification2);

            // 关注通知
            $notification3 = new MessageNotification();
            $notification3->setUser($user1);
            $notification3->setSender($user2);
            $notification3->setContent('关注了你');
            $notification3->setType(MessageType::FOLLOW);
            $notification3->setReadStatus(1);
            $notification3->setDeleted(0);
            $notification3->setTargetId('0');
            $manager->persist($notification3);

            // 点赞帖子通知
            $notification4 = new MessageNotification();
            $notification4->setUser($user1);
            $notification4->setSender($user2);
            $notification4->setContent('赞了你的帖子');
            $notification4->setType(MessageType::LIKE_THREAD);
            $notification4->setReadStatus(0);
            $notification4->setDeleted(0);
            $notification4->setTargetId('9876543210');
            $manager->persist($notification4);
        }

        $manager->flush();
    }
}
