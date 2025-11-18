<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumVisitStatCrudController;
use ForumBundle\Entity\VisitStat;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumVisitStatCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumVisitStatCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            VisitStat::class,
            ForumVisitStatCrudController::getEntityFqcn()
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
     * @return AbstractCrudController<VisitStat>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumVisitStatCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'thread' => ['帖子'];
        yield 'like_total' => ['总点赞数'];
        yield 'share_total' => ['总分享数'];
        yield 'comment_total' => ['总评论数'];
        yield 'visit_total' => ['访问数'];
        yield 'collect_count' => ['收藏数'];
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
        yield 'like_total' => ['likeTotal'];
        yield 'share_total' => ['shareTotal'];
        yield 'comment_total' => ['commentTotal'];
        yield 'visit_total' => ['visitTotal'];
        yield 'collect_count' => ['collectCount'];
        yield 'like_rank' => ['likeRank'];
        yield 'share_rank' => ['shareRank'];
        yield 'comment_rank' => ['commentRank'];
        yield 'visit_rank' => ['visitRank'];
        yield 'collect_rank' => ['collectRank'];
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
