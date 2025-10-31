<?php

declare(strict_types=1);

namespace ForumBundle\Tests\EventSubscriber;

use ForumBundle\EventSubscriber\ReplyCommentSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(ReplyCommentSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class ReplyCommentSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 空实现
    }

    #[Test]
    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(ReplyCommentSubscriber::class);
        $this->assertInstanceOf(ReplyCommentSubscriber::class, $service);
    }

    #[Test]
    public function testConstructorParameters(): void
    {
        $reflection = new \ReflectionClass(ReplyCommentSubscriber::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertGreaterThan(0, count($constructor->getParameters()));
    }

    #[Test]
    public function testHasEventListenerMethod(): void
    {
        $reflection = new \ReflectionClass(ReplyCommentSubscriber::class);
        $this->assertTrue($reflection->hasMethod('onAfterCommentThread'));

        $method = $reflection->getMethod('onAfterCommentThread');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function testOnAfterCommentThread(): void
    {
        $reflection = new \ReflectionClass(ReplyCommentSubscriber::class);
        $method = $reflection->getMethod('onAfterCommentThread');

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
        $this->assertSame('ForumBundle\Event\AfterCommentThread', (string) $firstParam->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);
    }
}
