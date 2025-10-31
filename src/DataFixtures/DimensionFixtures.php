<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Dimension;

class DimensionFixtures extends Fixture implements FixtureGroupInterface
{
    public const DIMENSION_HOT_REFERENCE = 'dimension-hot';
    public const DIMENSION_TIME_REFERENCE = 'dimension-time';
    public const DIMENSION_QUALITY_REFERENCE = 'dimension-quality';

    public static function getGroups(): array
    {
        return ['forum'];
    }

    public function load(ObjectManager $manager): void
    {
        $dimension1 = new Dimension();
        $dimension1->setTitle('热度维度');
        $dimension1->setCode('hot');
        $dimension1->setValid(true);
        $manager->persist($dimension1);
        $this->addReference(self::DIMENSION_HOT_REFERENCE, $dimension1);

        $dimension2 = new Dimension();
        $dimension2->setTitle('时间维度');
        $dimension2->setCode('time');
        $dimension2->setValid(true);
        $manager->persist($dimension2);
        $this->addReference(self::DIMENSION_TIME_REFERENCE, $dimension2);

        $dimension3 = new Dimension();
        $dimension3->setTitle('质量维度');
        $dimension3->setCode('quality');
        $dimension3->setValid(true);
        $manager->persist($dimension3);
        $this->addReference(self::DIMENSION_QUALITY_REFERENCE, $dimension3);

        $manager->flush();
    }
}
