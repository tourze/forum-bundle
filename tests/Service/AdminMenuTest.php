<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * 管理菜单测试
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses] final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    public function testAdminMenuShouldBeInstantiable(): void
    {
        $adminMenu = self::getService(AdminMenu::class);

        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testAdminMenuShouldHaveCorrectMethods(): void
    {
        $reflection = new \ReflectionClass(AdminMenu::class);

        $this->assertTrue($reflection->hasMethod('__construct'));
    }
}
