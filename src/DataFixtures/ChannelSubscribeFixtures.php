<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Channel;
use ForumBundle\Entity\ChannelSubscribe;
use Tourze\UserServiceContracts\UserManagerInterface;

class ChannelSubscribeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
            ChannelFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 使用 loadUserByIdentifier 方法来获取用户
        try {
            $bizUser = $this->userManager?->loadUserByIdentifier('1');
        } catch (\Exception $e) {
            $bizUser = null;
        }

        if (null !== $bizUser) {
            $subscribe1 = new ChannelSubscribe();
            $subscribe1->setUser($bizUser);
            $subscribe1->setChannel($this->getReference(ChannelFixtures::CHANNEL_TECH_REFERENCE, Channel::class));
            $subscribe1->setValid(true);
            $manager->persist($subscribe1);

            $subscribe2 = new ChannelSubscribe();
            $subscribe2->setUser($bizUser);
            $subscribe2->setChannel($this->getReference(ChannelFixtures::CHANNEL_LIFE_REFERENCE, Channel::class));
            $subscribe2->setValid(true);
            $manager->persist($subscribe2);
        }

        $manager->flush();
    }
}
