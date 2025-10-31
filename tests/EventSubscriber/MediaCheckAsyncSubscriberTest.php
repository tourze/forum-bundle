<?php

declare(strict_types=1);

namespace ForumBundle\Tests\EventSubscriber;

use ForumBundle\EventSubscriber\MediaCheckAsyncSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(MediaCheckAsyncSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class MediaCheckAsyncSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 空实现
    }

    #[Test]
    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(MediaCheckAsyncSubscriber::class);
        $this->assertInstanceOf(MediaCheckAsyncSubscriber::class, $service);
    }

    #[Test]
    public function testConstructorParameters(): void
    {
        $reflection = new \ReflectionClass(MediaCheckAsyncSubscriber::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertGreaterThan(0, count($constructor->getParameters()));
    }

    #[Test]
    public function testHasEventListenerMethod(): void
    {
        $reflection = new \ReflectionClass(MediaCheckAsyncSubscriber::class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $eventListenerMethods = array_filter($methods, function ($method) {
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                if (str_contains($attribute->getName(), 'AsEventListener')) {
                    return true;
                }
            }

            return false;
        });

        $this->assertGreaterThan(0, count($eventListenerMethods));
    }

    #[Test]
    public function testOnAfterMediaCheckAsyncCallback(): void
    {
        $reflection = new \ReflectionClass(MediaCheckAsyncSubscriber::class);
        $method = $reflection->getMethod('onAfterMediaCheckAsyncCallback');

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
        $this->assertSame('WechatMiniProgramSecurityBundle\Event\MediaCheckAsyncEvent', (string) $firstParam->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);
    }
}
