<?php

declare(strict_types=1);

namespace ForumBundle\Tests\EventSubscriber;

use ForumBundle\EventSubscriber\JsonRpcSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(JsonRpcSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class JsonRpcSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 空实现
    }

    #[Test]
    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(JsonRpcSubscriber::class);
        $this->assertInstanceOf(JsonRpcSubscriber::class, $service);
    }

    #[Test]
    public function testHasEventListenerMethod(): void
    {
        $reflection = new \ReflectionClass(JsonRpcSubscriber::class);
        $this->assertTrue($reflection->hasMethod('onCodeToSessionResponseEvent'));

        $method = $reflection->getMethod('onCodeToSessionResponseEvent');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function testEventListenerMethodHasCorrectParameter(): void
    {
        $reflection = new \ReflectionClass(JsonRpcSubscriber::class);
        $method = $reflection->getMethod('onCodeToSessionResponseEvent');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('event', $parameters[0]->getName());
    }

    #[Test]
    public function testOnCodeToSessionResponseEvent(): void
    {
        $reflection = new \ReflectionClass(JsonRpcSubscriber::class);
        $method = $reflection->getMethod('onCodeToSessionResponseEvent');

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
        $this->assertSame('WechatMiniProgramAuthBundle\Event\CodeToSessionResponseEvent', (string) $firstParam->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);
    }
}
