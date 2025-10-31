<?php

declare(strict_types=1);

namespace ForumBundle\Tests\EventSubscriber;

use ForumBundle\EventSubscriber\InviteUserSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(InviteUserSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class InviteUserSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 空实现
    }

    #[Test]
    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(InviteUserSubscriber::class);
        $this->assertInstanceOf(InviteUserSubscriber::class, $service);
    }

    #[Test]
    public function testConstructorParameters(): void
    {
        $reflection = new \ReflectionClass(InviteUserSubscriber::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertGreaterThan(0, count($constructor->getParameters()));
    }

    #[Test]
    public function testHasEventSubscriberMethods(): void
    {
        $reflection = new \ReflectionClass(InviteUserSubscriber::class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $this->assertGreaterThan(0, count($methods));
    }

    #[Test]
    public function testOnInviteUserEvent(): void
    {
        $reflection = new \ReflectionClass(InviteUserSubscriber::class);
        $method = $reflection->getMethod('onInviteUserEvent');

        // 验证方法存在
        $this->assertNotNull($method);

        // 验证方法是公开的
        $this->assertTrue($method->isPublic());

        // 验证参数数量
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters);

        // 验证第一个参数类型
        $firstParam = $parameters[0];
        $this->assertNotNull($firstParam->getType());
        $this->assertSame('WechatMiniProgramShareBundle\Event\InviteUserEvent', (string) $firstParam->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);
    }
}
