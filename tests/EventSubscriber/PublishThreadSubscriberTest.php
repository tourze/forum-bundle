<?php

declare(strict_types=1);

namespace ForumBundle\Tests\EventSubscriber;

use ForumBundle\EventSubscriber\PublishThreadSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(PublishThreadSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class PublishThreadSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 空实现
    }

    #[Test]
    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(PublishThreadSubscriber::class);
        $this->assertInstanceOf(PublishThreadSubscriber::class, $service);
    }

    #[Test]
    public function testConstructorParameters(): void
    {
        $reflection = new \ReflectionClass(PublishThreadSubscriber::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertGreaterThan(0, count($constructor->getParameters()));
    }

    #[Test]
    public function testHasEventListenerMethod(): void
    {
        $reflection = new \ReflectionClass(PublishThreadSubscriber::class);
        $this->assertTrue($reflection->hasMethod('onAfterPublishThread'));

        $method = $reflection->getMethod('onAfterPublishThread');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function testOnAfterPublishThread(): void
    {
        $reflection = new \ReflectionClass(PublishThreadSubscriber::class);
        $method = $reflection->getMethod('onAfterPublishThread');

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
        $this->assertSame('ForumBundle\Event\AfterPublishThread', (string) $firstParam->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);
    }
}
