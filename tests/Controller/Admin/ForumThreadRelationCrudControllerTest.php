<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumThreadRelationCrudController;
use ForumBundle\Entity\ThreadRelation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumThreadRelationCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumThreadRelationCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            ThreadRelation::class,
            ForumThreadRelationCrudController::getEntityFqcn()
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
     * @return AbstractCrudController<ThreadRelation>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumThreadRelationCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '父帖子' => ['父帖子'];
        yield '子帖子' => ['子帖子'];
        yield '关系类型' => ['关系类型'];
        yield '创建时间' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'instanceId' => ['instanceId'];
        yield 'email' => ['email'];
        yield 'password' => ['password'];
        yield 'parentThread' => ['parentThread'];
        yield 'childThread' => ['childThread'];
        yield 'relationType' => ['relationType'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'content' => ['content'];
        yield 'status' => ['status'];
    }
}
