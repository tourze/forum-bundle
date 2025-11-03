<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumThreadCrudController;
use ForumBundle\Entity\Thread;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumThreadCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumThreadCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            Thread::class,
            ForumThreadCrudController::getEntityFqcn()
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
        $result = ForumThreadCrudController::getEntityFqcn();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function testGetEntityFqcnReturnsThreadClass(): void
    {
        $expectedClass = Thread::class;
        $actualClass = ForumThreadCrudController::getEntityFqcn();

        $this->assertSame($expectedClass, $actualClass);

        // 测试类是否可以实例化
        $instance = new $actualClass();
        $this->assertInstanceOf($expectedClass, $instance);
    }

    /**
     * @return ForumThreadCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumThreadCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'title' => ['标题'];
        yield 'user' => ['发布用户'];
        yield 'status' => ['审核状态'];
        yield 'type' => ['帖子类型'];
        yield 'cover_picture' => ['封面图'];
        yield 'created_at' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'instance_id' => ['instanceId'];
        yield 'email' => ['email'];
        yield 'password' => ['password'];
        yield 'title' => ['title'];
        yield 'content' => ['content'];
        yield 'status' => ['status'];
        yield 'type' => ['type'];
        yield 'channels' => ['channels'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'title_field' => ['title'];
        yield 'content_field' => ['content'];
        yield 'status_field' => ['status'];
    }

    #[Test]
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', '/admin?crudAction=new&crudControllerFqcn=' . urlencode(ForumThreadCrudController::class));
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
