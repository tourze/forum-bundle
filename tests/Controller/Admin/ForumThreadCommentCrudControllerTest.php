<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumThreadCommentCrudController;
use ForumBundle\Entity\ThreadComment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumThreadCommentCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumThreadCommentCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            ThreadComment::class,
            ForumThreadCommentCrudController::getEntityFqcn()
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
     * @return ForumThreadCommentCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumThreadCommentCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'thread' => ['关联帖子'];
        yield 'user' => ['评论用户'];
        yield 'content' => ['评论内容'];
        yield 'status' => ['审核状态'];
        yield 'best' => ['是否最佳'];
        yield 'createTime' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'id' => ['id'];
        yield 'thread' => ['thread'];
        yield 'user' => ['user'];
        yield 'replyUser' => ['replyUser'];
        yield 'content' => ['content'];
        yield 'status' => ['status'];
        yield 'parentId' => ['parentId'];
        yield 'rootParentId' => ['rootParentId'];
        yield 'best' => ['best'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'id' => ['id'];
        yield 'thread' => ['thread'];
        yield 'user' => ['user'];
        yield 'replyUser' => ['replyUser'];
        yield 'content' => ['content'];
        yield 'status' => ['status'];
        yield 'parentId' => ['parentId'];
        yield 'rootParentId' => ['rootParentId'];
        yield 'best' => ['best'];
    }

    #[Test]
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', '/admin?crudAction=new&crudControllerFqcn=' . urlencode(ForumThreadCommentCrudController::class));
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Create')->form();
        // 提交空表单以触发验证错误
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('required', $content);
    }
}
