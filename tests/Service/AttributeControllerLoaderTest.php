<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 属性控制器加载器测试
 *
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses] final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    public function testAutoload(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $reflection = new \ReflectionMethod($service, 'autoload');

        $this->assertTrue($reflection->isPublic());
        $this->assertCount(0, $reflection->getParameters());
        $this->assertEquals('Symfony\Component\Routing\RouteCollection', (string) $reflection->getReturnType());

        $result = $service->autoload();
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupports(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $reflection = new \ReflectionMethod($service, 'supports');

        $this->assertTrue($reflection->isPublic());
        $this->assertCount(2, $reflection->getParameters());
        $this->assertEquals('bool', (string) $reflection->getReturnType());

        $result = $service->supports('test', 'test');
        $this->assertFalse($result);
    }

    public function testLoad(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $reflection = new \ReflectionMethod($service, 'load');

        $this->assertTrue($reflection->isPublic());
        $this->assertCount(2, $reflection->getParameters());
        $this->assertEquals('Symfony\Component\Routing\RouteCollection', (string) $reflection->getReturnType());

        $result = $service->load('test-resource');
        $this->assertInstanceOf(RouteCollection::class, $result);

        // load() 方法应该调用 autoload()，所以结果应该与 autoload() 相同
        $autoloadResult = $service->autoload();
        $this->assertEquals($autoloadResult->count(), $result->count());
    }
}
