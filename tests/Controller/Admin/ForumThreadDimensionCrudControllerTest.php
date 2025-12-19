<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumThreadDimensionCrudController;
use ForumBundle\Entity\ThreadDimension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumThreadDimensionCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumThreadDimensionCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            ThreadDimension::class,
            ForumThreadDimensionCrudController::getEntityFqcn()
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
     * @return ForumThreadDimensionCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumThreadDimensionCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'thread' => ['帖子'];
        yield 'dimension' => ['维度'];
        yield 'value' => ['值'];
        yield 'created_at' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'id' => ['id'];
        yield 'thread' => ['thread'];
        yield 'dimension' => ['dimension'];
        yield 'value' => ['value'];
        yield 'context' => ['context'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'id' => ['id'];
        yield 'thread' => ['thread'];
        yield 'dimension' => ['dimension'];
        yield 'value' => ['value'];
        yield 'context' => ['context'];
    }
}
