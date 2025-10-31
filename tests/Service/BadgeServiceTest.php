<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Enum\BadgeType;
use ForumBundle\Service\BadgeService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 徽章服务测试
 *
 * @internal
 */
#[CoversClass(BadgeService::class)]
#[RunTestsInSeparateProcesses] final class BadgeServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getBadgeService(): BadgeService
    {
        return self::getService(BadgeService::class);
    }

    public function testUpgrade(): void
    {
        // 创建测试用户
        $user = $this->createMock(UserInterface::class);
        $user->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('test_user_123')
        ;

        $service = $this->getBadgeService();

        // 验证方法存在和签名
        $classReflection = new \ReflectionClass($service);
        $this->assertTrue($classReflection->hasMethod('upgrade'));

        $reflection = new \ReflectionMethod($service, 'upgrade');
        $parameters = $reflection->getParameters();
        $this->assertCount(2, $parameters);

        // 验证参数类型
        $this->assertSame('user', $parameters[0]->getName());
        $userType = $parameters[0]->getType();
        $this->assertNotNull($userType);
        $this->assertSame(UserInterface::class, (string) $userType);

        $this->assertSame('type', $parameters[1]->getName());
        $badgeType = $parameters[1]->getType();
        $this->assertNotNull($badgeType);
        $this->assertSame(BadgeType::class, (string) $badgeType);

        // 验证返回类型（void）
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);

        // 执行方法 - 验证方法正常执行（无异常抛出即可）
        $service->upgrade($user, BadgeType::THREAD);

        // 如果到达这里，说明方法正常执行完成
    }
}
