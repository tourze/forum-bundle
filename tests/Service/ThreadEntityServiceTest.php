<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Service\ThreadEntityService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\UserServiceContracts\UserManagerInterface;

/**
 * @internal
 */
#[CoversClass(ThreadEntityService::class)]
#[RunTestsInSeparateProcesses]
final class ThreadEntityServiceTest extends AbstractIntegrationTestCase
{
    private ThreadEntityService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(ThreadEntityService::class);
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ThreadEntityService::class, $this->service);
    }

    public function testRenderEntityCountReturnsCorrectStructure(): void
    {
        $thread = new Thread();
        // Thread 实体的 getter 方法会通过关联关系计算数量
        // 我们测试方法是否返回正确的结构，而不是具体的数值
        $result = $this->service->renderEntityCount($thread);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('comments', $result);
        $this->assertArrayHasKey('likes', $result);
        $this->assertArrayHasKey('collects', $result);
        $this->assertIsInt($result['comments']);
        $this->assertIsInt($result['likes']);
        $this->assertIsInt($result['collects']);
    }

    public function testGenerateLockResourceReturnsCorrectFormat(): void
    {
        $thread = new Thread();
        $thread->setId('456');

        $result = $this->service->generateLockResource($thread);

        $this->assertEquals('lock_forum_thread_456', $result);
    }

    public function testHandleBeforeCreate(): void
    {
        $thread = new Thread();
        $userManager = $this->createMock(UserManagerInterface::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('需要实现获取官方用户的逻辑');

        $this->service->handleBeforeCreate($thread, $userManager);
    }

    public function testHandleAfterEditWithSameStatus(): void
    {
        $thread = new Thread();
        $form = ['status' => 'audit_pass'];
        $record = ['status' => 'audit_pass'];

        // 应该不抛出异常，也不分发事件
        $this->service->handleAfterEdit($thread, $form, $record);

        // 如果没有异常，测试通过
        $this->assertTrue(true);
    }

    public function testRenderVideoColumnWithEmptyMedia(): void
    {
        $thread = new Thread();

        $result = $this->service->renderVideoColumn($thread);

        $this->assertEquals('', $result);
    }

    public function testRenderUserPhoneColumn(): void
    {
        $thread = new Thread();

        $result = $this->service->renderUserPhoneColumn($thread);

        $this->assertIsString($result);
        $this->assertJson($result);

        $data = json_decode($result, true);
        $this->assertArrayHasKey('value', $data);
        $this->assertEquals('', $data['value']);
    }

    public function testGetOtherData(): void
    {
        $thread = new Thread();

        $result = $this->service->getOtherData($thread);

        $this->assertEquals('', $result);
    }
}
