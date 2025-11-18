<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumSortingRuleCrudController;
use ForumBundle\Entity\SortingRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumSortingRuleCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumSortingRuleCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            SortingRule::class,
            ForumSortingRuleCrudController::getEntityFqcn()
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
     * @return ForumSortingRuleCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumSortingRuleCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'name' => ['名称'];
        yield 'rule' => ['规则'];
        yield 'valid' => ['有效'];
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
        yield 'name' => ['name'];
        yield 'rule' => ['rule'];
        yield 'valid' => ['valid'];
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
