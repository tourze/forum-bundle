<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumMessageNotificationCrudController;
use ForumBundle\Entity\MessageNotification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumMessageNotificationCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumMessageNotificationCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            MessageNotification::class,
            ForumMessageNotificationCrudController::getEntityFqcn()
        );
    }

    #[Test]
    public function testListPageDisplaysCorrectly(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('dashboard', $content);
    }

    #[Test]
    public function testGetEntityFqcnReturnsStringType(): void
    {
        $result = ForumMessageNotificationCrudController::getEntityFqcn();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function testGetEntityFqcnReturnsMessageNotificationClass(): void
    {
        $expectedClass = MessageNotification::class;
        $actualClass = ForumMessageNotificationCrudController::getEntityFqcn();

        $this->assertSame($expectedClass, $actualClass);

        // 测试类是否可以实例化
        $instance = new $actualClass();
        $this->assertInstanceOf($expectedClass, $instance);
    }

    /**
     * @return ForumMessageNotificationCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumMessageNotificationCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'receiver' => ['接收用户'];
        yield 'sender' => ['发送者'];
        yield 'content' => ['消息内容'];
        yield 'type' => ['通知类型'];
        yield 'action' => ['操作类型'];
        yield 'path' => ['跳转路径'];
        yield 'pathType' => ['路径类型'];
        yield 'targetId' => ['目标ID'];
        yield 'readStatus' => ['已读状态'];
        yield 'deleteStatus' => ['删除状态'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'id' => ['id'];
        yield 'user' => ['user'];
        yield 'sender' => ['sender'];
        yield 'content' => ['content'];
        yield 'type' => ['type'];
        yield 'action' => ['action'];
        yield 'path' => ['path'];
        yield 'pathType' => ['pathType'];
        yield 'targetId' => ['targetId'];
        yield 'readStatus' => ['readStatus'];
        yield 'deleted' => ['deleted'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'content_field' => ['content'];
        yield 'readStatus_field' => ['readStatus'];
    }
}
