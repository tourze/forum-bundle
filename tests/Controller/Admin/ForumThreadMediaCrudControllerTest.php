<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumThreadMediaCrudController;
use ForumBundle\Entity\ThreadMedia;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumThreadMediaCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumThreadMediaCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            ThreadMedia::class,
            ForumThreadMediaCrudController::getEntityFqcn()
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
     * @return ForumThreadMediaCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumThreadMediaCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'thread' => ['帖子'];
        yield 'type' => ['媒体类型'];
        yield 'path' => ['来源路径'];
        yield 'size' => ['大小'];
        yield 'created_by' => ['创建人'];
        yield 'updated_by' => ['更新人'];
        yield 'created_at' => ['创建时间'];
        yield 'updated_at' => ['更新时间'];
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
        yield 'type' => ['type'];
        yield 'path' => ['path'];
        yield 'thumbnail' => ['thumbnail'];
        yield 'size' => ['size'];
        yield 'options' => ['options'];
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
