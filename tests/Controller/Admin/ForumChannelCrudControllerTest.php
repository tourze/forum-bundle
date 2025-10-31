<?php

namespace ForumBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use ForumBundle\Controller\Admin\ForumChannelCrudController;
use ForumBundle\Entity\Channel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ForumChannelCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ForumChannelCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertEquals(
            Channel::class,
            ForumChannelCrudController::getEntityFqcn()
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
        $result = ForumChannelCrudController::getEntityFqcn();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function testGetEntityFqcnReturnsChannelClass(): void
    {
        $expectedClass = Channel::class;
        $actualClass = ForumChannelCrudController::getEntityFqcn();

        $this->assertSame($expectedClass, $actualClass);

        // 测试类是否可以实例化
        $instance = new $actualClass();
        $this->assertInstanceOf($expectedClass, $instance);
    }

    /**
     * @return ForumChannelCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ForumChannelCrudController::class);
    }

    #[Test]
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', '/admin?crudAction=new&crudControllerFqcn=' . urlencode(ForumChannelCrudController::class));
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

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'title' => ['标题'];
        yield 'valid' => ['是否有效'];
        yield 'created_at' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'id' => ['id'];
        yield 'title' => ['title'];
        yield 'valid' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'title_field' => ['title'];
        yield 'valid_field' => ['valid'];
    }
}
