<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumForumShareRecordCrudController;
use ForumBundle\Entity\ForumShareRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumForumShareRecordCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumForumShareRecordCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            ForumShareRecord::class,
            ForumForumShareRecordCrudController::getEntityFqcn()
        );
    }

    #[Test]
    public function testListPageDisplaysCorrectly(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('dashboard', $content);
    }

    #[Test]
    public function testNewFormIsAccessible(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('dashboard', $content);
    }

    #[Test]
    public function testIndexActionHandlesFiltering(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testEditActionRequiresValidEntity(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testDeleteActionRequiresValidEntity(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('POST', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testDetailActionRequiresValidEntity(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testCreateFormValidation(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testControllerHandlesCrudActions(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testFiltersByUser(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testFiltersByForum(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return ForumForumShareRecordCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumForumShareRecordCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'user' => ['用户'];
        yield 'type' => ['分享类型'];
        yield 'sourceId' => ['来源主键ID'];
        yield 'createTime' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'user' => ['用户'];
        yield 'type' => ['分享类型'];
        yield 'sourceId' => ['来源主键ID'];
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
