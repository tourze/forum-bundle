<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Channel;

class ChannelFixtures extends Fixture
{
    public const CHANNEL_TECH_REFERENCE = 'channel-tech';
    public const CHANNEL_LIFE_REFERENCE = 'channel-life';
    public const CHANNEL_QA_REFERENCE = 'channel-qa';

    public function load(ObjectManager $manager): void
    {
        $channel1 = new Channel();
        $channel1->setTitle('技术交流');
        $channel1->setValid(true);
        $manager->persist($channel1);
        $this->addReference(self::CHANNEL_TECH_REFERENCE, $channel1);

        $channel2 = new Channel();
        $channel2->setTitle('生活分享');
        $channel2->setValid(true);
        $manager->persist($channel2);
        $this->addReference(self::CHANNEL_LIFE_REFERENCE, $channel2);

        $channel3 = new Channel();
        $channel3->setTitle('问答求助');
        $channel3->setValid(true);
        $manager->persist($channel3);
        $this->addReference(self::CHANNEL_QA_REFERENCE, $channel3);

        $manager->flush();
    }
}
