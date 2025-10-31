<?php

declare(strict_types=1);

namespace ForumBundle\Tests\EventSubscriber;

use ForumBundle\EventSubscriber\LikeSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(LikeSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class LikeSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 空实现
    }

    #[Test]
    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(LikeSubscriber::class);
        $this->assertInstanceOf(LikeSubscriber::class, $service);
    }

    #[Test]
    public function testConstructorParameters(): void
    {
        $reflection = new \ReflectionClass(LikeSubscriber::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertGreaterThan(0, count($constructor->getParameters()));
    }

    #[Test]
    public function testHasEventListenerMethods(): void
    {
        $reflection = new \ReflectionClass(LikeSubscriber::class);

        $this->assertTrue($reflection->hasMethod('onAfterLikeThread'));
        $this->assertTrue($reflection->hasMethod('onAfterLikeThreadComment'));

        $method1 = $reflection->getMethod('onAfterLikeThread');
        $method2 = $reflection->getMethod('onAfterLikeThreadComment');

        $this->assertTrue($method1->isPublic());
        $this->assertTrue($method2->isPublic());
    }

    #[Test]
    public function testOnAfterLikeThread(): void
    {
        $reflection = new \ReflectionClass(LikeSubscriber::class);
        $method = $reflection->getMethod('onAfterLikeThread');

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
        $this->assertSame('ForumBundle\Event\AfterLikeThread', (string) $firstParam->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);
    }

    #[Test]
    public function testOnAfterLikeThreadComment(): void
    {
        $reflection = new \ReflectionClass(LikeSubscriber::class);
        $method = $reflection->getMethod('onAfterLikeThreadComment');

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
        $this->assertSame('ForumBundle\Event\AfterLikeThreadComment', (string) $firstParam->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);
    }
}
