<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Entity\MessageNotification;
use ForumBundle\Enum\MessageType;
use ForumBundle\Service\NotifyService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 通知服务测试
 *
 * @internal
 */
#[CoversClass(NotifyService::class)]
#[RunTestsInSeparateProcesses] final class NotifyServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getNotifyService(): NotifyService
    {
        // 从容器获取服务实例，符合集成测试规范
        return self::getService(NotifyService::class);
    }

    public function testSendWithNewNotification(): void
    {
        // 从容器获取服务实例
        $service = $this->getNotifyService();

        // 创建真实通知实体
        $notification = new MessageNotification();
        $notification->setContent('这是一个测试通知内容');
        $notification->setType(MessageType::SYSTEM_NOTIFICATION);
        $notification->setTargetId('test-target-123');
        $notification->setReadStatus(0);

        // 执行发送操作（集成测试会实际持久化到数据库）
        $service->send($notification);

        // 验证通知已被保存
        $this->assertNotNull($notification->getId());
    }

    public function testSendWithExistingNotification(): void
    {
        // 从容器获取服务实例
        $service = $this->getNotifyService();

        // 先创建一个通知并保存
        $notification = new MessageNotification();
        $notification->setContent('这是一个已存在的通知');
        $notification->setType(MessageType::REPLY);
        $notification->setTargetId('test-target-456');
        $notification->setReadStatus(0);
        $service->send($notification);

        $originalId = $notification->getId();
        $this->assertNotNull($originalId);

        // 再次发送同一通知
        $service->send($notification);

        // 验证ID没有变化（已存在的通知）
        $this->assertSame($originalId, $notification->getId());
    }
}
