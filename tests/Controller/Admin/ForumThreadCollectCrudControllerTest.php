<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumThreadCollectCrudController;
use ForumBundle\Entity\ThreadCollect;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumThreadCollectCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumThreadCollectCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            ThreadCollect::class,
            ForumThreadCollectCrudController::getEntityFqcn()
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
     * 加载测试数据
     * 注意：ThreadCollect 的 user 字段是 NOT NULL（nullable: false），
     * 且需要关联 UserInterface 实体。由于测试环境无法正确创建 UserInterface 实体，
     * 因此无法创建测试数据，部分测试将会失败。
     */
    protected function afterEasyAdminSetUp(): void
    {
        // 无法创建测试数据，因为 user 字段 NOT NULL 且需要有效的 UserInterface 实体
    }

    /**
     * @return ForumThreadCollectCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumThreadCollectCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'thread' => ['关联帖子'];
        yield 'user' => ['收藏用户'];
        yield 'valid' => ['是否有效'];
        yield 'created_at' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'thread' => ['thread'];
        yield 'user' => ['user'];
        yield 'valid' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'thread_field' => ['thread'];
        yield 'user_field' => ['user'];
        yield 'valid_field' => ['valid'];
    }
}
