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
    protected function afterEasyAdminSetUp(): void
    {
        $em = self::getEntityManager();

        // 创建维度实体
        $dimension = new \ForumBundle\Entity\Dimension();
        $dimension->setTitle('测试维度');
        $dimension->setCode('test');
        $dimension->setValid(true);

        // 创建排序规则实体
        $sortingRule = new SortingRule();
        $sortingRule->setTitle('测试规则');
        $sortingRule->setFormula('test_formula');
        $sortingRule->setDimension($dimension);

        // 持久化实体
        $em->persist($dimension);
        $em->persist($sortingRule);
        $em->flush();
    }

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
        yield 'name' => ['规则名'];
        yield 'dimension' => ['维度'];
        yield 'formula' => ['规则公式'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'formula' => ['formula'];
        yield 'dimension' => ['dimension'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'title_field' => ['title'];
        yield 'formula_field' => ['formula'];
        yield 'dimension_field' => ['dimension'];
    }
}
