<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCollect;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 主题收藏实体测试
 *
 * @internal
 */
#[CoversClass(ThreadCollect::class)]
final class ThreadCollectTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ThreadCollect();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
    }

    public function testThreadCollectShouldBeInstantiable(): void
    {
        $collect = new ThreadCollect();

        $this->assertInstanceOf(ThreadCollect::class, $collect);
    }

    public function testThreadRelationShouldWork(): void
    {
        $collect = new ThreadCollect();
        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);

        $collect->setThread($thread);

        $this->assertSame($thread, $collect->getThread());
    }

    public function testUserRelationShouldWork(): void
    {
        $collect = new ThreadCollect();
        $user = $this->createMock(UserInterface::class);

        $collect->setUser($user);

        $this->assertSame($user, $collect->getUser());
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $collect = new ThreadCollect();

        $result = (string) $collect;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $collect = new ThreadCollect();

        $this->assertNotNull($collect);
        $this->assertNull($collect->getId());
    }
}
