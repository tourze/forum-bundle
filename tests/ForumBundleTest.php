<?php

declare(strict_types=1);

namespace ForumBundle\Tests;

use ForumBundle\ForumBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(ForumBundle::class)]
#[RunTestsInSeparateProcesses]
final class ForumBundleTest extends AbstractBundleTestCase
{
}
