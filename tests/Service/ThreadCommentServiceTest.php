<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Service\ThreadCommentService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadCommentService::class)]
#[RunTestsInSeparateProcesses] final class ThreadCommentServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getThreadCommentService(): ThreadCommentService
    {
        // 从容器获取服务实例，符合集成测试规范
        return self::getService(ThreadCommentService::class);
    }

    public function testThreadCommentServiceShouldHaveCorrectMethods(): void
    {
        $reflection = new \ReflectionClass(ThreadCommentService::class);
        $this->assertTrue($reflection->hasMethod('getCommentListByThreadId'));
    }

    public function testThreadCommentServiceShouldCreateInstance(): void
    {
        $service = $this->getThreadCommentService();
        $this->assertInstanceOf(ThreadCommentService::class, $service);
    }
}
