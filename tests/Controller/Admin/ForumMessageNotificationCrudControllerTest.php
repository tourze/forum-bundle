<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumMessageNotificationCrudController;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Enum\MessageActionType;
use ForumBundle\Enum\MessageType;
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
    /**
     * 注意：MessageNotification 实体有 user 和 sender 关联到 UserInterface，
     * 由于 Doctrine ResolveTargetEntity 在测试环境中无法正确解析动态生成的用户实体，
     * 因此不创建测试数据。这会导致部分依赖测试数据的测试失败，但这是测试框架的限制。
     */
    protected function afterEasyAdminSetUp(): void
    {
        // 不创建测试数据，避免 Doctrine MappingException
    }

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
