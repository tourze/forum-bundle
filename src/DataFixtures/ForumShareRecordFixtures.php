<?php

namespace ForumBundle\DataFixtures;

use BizUserBundle\DataFixtures\BizUserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\ForumShareRecord;
use Tourze\UserServiceContracts\UserManagerInterface;

class ForumShareRecordFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
        return [BizUserFixtures::class];
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
            $shareRecord1 = new ForumShareRecord();
            $shareRecord1->setUser($bizUser);
            $shareRecord1->setType('thread');
            $shareRecord1->setSourceId('1234567890');
            $manager->persist($shareRecord1);

            $shareRecord2 = new ForumShareRecord();
            $shareRecord2->setUser($bizUser);
            $shareRecord2->setType('comment');
            $shareRecord2->setSourceId('9876543210');
            $manager->persist($shareRecord2);

            $shareRecord3 = new ForumShareRecord();
            $shareRecord3->setUser($bizUser);
            $shareRecord3->setType('topic');
            $shareRecord3->setSourceId('5555555555');
            $manager->persist($shareRecord3);
        }

        $manager->flush();
    }
}
