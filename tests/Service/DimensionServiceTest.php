<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\Thread;
use ForumBundle\Service\DimensionService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 维度服务测试
 *
 * @internal
 */
#[CoversClass(DimensionService::class)]
#[RunTestsInSeparateProcesses] final class DimensionServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getDimensionService(): DimensionService
    {
        // 从容器获取服务实例，符合集成测试规范
        return self::getService(DimensionService::class);
    }

    public function testCalcThreadDimension(): void
    {
        // 从容器获取服务实例
        $service = $this->getDimensionService();

        // 验证方法存在
        $classReflection = new \ReflectionClass($service);
        $this->assertTrue($classReflection->hasMethod('calcThreadDimension'));

        // 验证方法参数
        $reflection = new \ReflectionMethod($service, 'calcThreadDimension');
        $parameters = $reflection->getParameters();
        $this->assertCount(2, $parameters);

        // 验证第一个参数类型 (Thread)
        $this->assertSame('thread', $parameters[0]->getName());
        $threadType = $parameters[0]->getType();
        $this->assertNotNull($threadType);
        $this->assertSame(Thread::class, (string) $threadType);

        // 验证第二个参数类型 (Dimension)
        $this->assertSame('dimension', $parameters[1]->getName());
        $dimensionType = $parameters[1]->getType();
        $this->assertNotNull($dimensionType);
        $this->assertSame(Dimension::class, (string) $dimensionType);

        // 验证返回类型 (array)
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('array', (string) $returnType);

        // 创建真实实体进行测试
        $thread = new Thread();
        $thread->setId('test-thread-123');

        $dimension = new Dimension();
        $dimension->setId('test-dimension-456');

        // 执行方法（集成测试使用真实依赖）
        $result = $service->calcThreadDimension($thread, $dimension);

        // 验证返回值是数组
        $this->assertIsArray($result);
        $this->assertEmpty($result); // 因为没有排序规则，所以结果应该是空数组
    }
}
