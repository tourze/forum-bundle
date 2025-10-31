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

    #[Test]
    public function testGetEntityFqcnReturnsStringType(): void
    {
        $result = ForumThreadCollectCrudController::getEntityFqcn();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function testGetEntityFqcnReturnsThreadCollectClass(): void
    {
        $expectedClass = ThreadCollect::class;
        $actualClass = ForumThreadCollectCrudController::getEntityFqcn();

        $this->assertSame($expectedClass, $actualClass);

        // 测试类是否可以实例化
        $instance = new $actualClass();
        $this->assertInstanceOf($expectedClass, $instance);
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
        yield 'thread' => ['帖子'];
        yield 'user' => ['用户'];
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
        yield 'thread' => ['thread'];
        yield 'user' => ['user'];
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
}
