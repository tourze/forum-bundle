<?php

namespace ForumBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\CatalogBundle\CatalogBundle;
use Tourze\DoctrineEntityLockBundle\DoctrineEntityLockBundle;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\EcolBundle\EcolBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use Tourze\SensitiveTextDetectBundle\SensitiveTextDetectBundle;
use Tourze\Symfony\CronJob\CronJobBundle;
use Tourze\UserFollowBundle\UserFollowBundle;

class ForumBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            EasyAdminBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
            CatalogBundle::class => ['all' => true],
            DoctrineIndexedBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineEntityLockBundle::class => ['all' => true],
            CronJobBundle::class => ['all' => true],
            EcolBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
            SensitiveTextDetectBundle::class => ['all' => true],
            UserFollowBundle::class => ['all' => true],
        ];
    }
}
