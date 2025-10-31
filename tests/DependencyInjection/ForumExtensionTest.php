<?php

namespace ForumBundle\Tests\DependencyInjection;

use ForumBundle\DependencyInjection\ForumExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(ForumExtension::class)]
final class ForumExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testExtensionImplementsExtensionInterface(): void
    {
        $extension = new ForumExtension();
        $this->assertInstanceOf(Extension::class, $extension);
    }
}
