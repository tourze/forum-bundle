<?php

namespace ForumBundle\Tests\Util;

use ForumBundle\Util\BadgeUtil;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(BadgeUtil::class)]
#[RunTestsInSeparateProcesses] final class BadgeUtilTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    public function testHasStaticMethod(): void
    {
        $reflection = new \ReflectionClass(BadgeUtil::class);
        $this->assertTrue($reflection->hasMethod('genConditionText'));

        $method = $reflection->getMethod('genConditionText');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }
}
