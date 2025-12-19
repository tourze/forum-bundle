<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumThreadCommentLikeCrudController;
use ForumBundle\Entity\ThreadCommentLike;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumThreadCommentLikeCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumThreadCommentLikeCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            ThreadCommentLike::class,
            ForumThreadCommentLikeCrudController::getEntityFqcn()
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
     * @return ForumThreadCommentLikeCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumThreadCommentLikeCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'comment' => ['评论'];
        yield 'user' => ['用户'];
        yield 'created_at' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'id' => ['id'];
        yield 'threadComment' => ['threadComment'];
        yield 'user' => ['user'];
        yield 'status' => ['status'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'id' => ['id'];
        yield 'threadComment' => ['threadComment'];
        yield 'user' => ['user'];
        yield 'status' => ['status'];
    }
}
