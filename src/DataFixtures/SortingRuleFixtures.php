<?php

namespace ForumBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\SortingRule;

class SortingRuleFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['forum'];
    }

    public function getDependencies(): array
    {
        return [
            DimensionFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 热度维度的排序规则
        $rule1 = new SortingRule();
        $rule1->setTitle('热度算法');
        $rule1->setDimension($this->getReference(DimensionFixtures::DIMENSION_HOT_REFERENCE, Dimension::class));
        $rule1->setFormula('(likes * 2 + comments * 3 + views * 0.1) / (hours_since_post + 2)');
        $manager->persist($rule1);

        // 时间维度的排序规则
        $rule2 = new SortingRule();
        $rule2->setTitle('最新优先');
        $rule2->setDimension($this->getReference(DimensionFixtures::DIMENSION_TIME_REFERENCE, Dimension::class));
        $rule2->setFormula('created_at DESC');
        $manager->persist($rule2);

        // 质量维度的排序规则
        $rule3 = new SortingRule();
        $rule3->setTitle('质量评分');
        $rule3->setDimension($this->getReference(DimensionFixtures::DIMENSION_QUALITY_REFERENCE, Dimension::class));
        $rule3->setFormula('(likes / views) * 100 + (comments / views) * 50');
        $manager->persist($rule3);

        $manager->flush();
    }
}
