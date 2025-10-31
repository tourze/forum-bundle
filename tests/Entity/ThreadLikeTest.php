<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadLike;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 主题点赞实体测试
 *
 * @internal
 */
#[CoversClass(ThreadLike::class)]
final class ThreadLikeTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ThreadLike();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'status' => ['status', 1];
    }

    public function testThreadLikeShouldBeInstantiable(): void
    {
        $like = new ThreadLike();

        $this->assertInstanceOf(ThreadLike::class, $like);
    }

    public function testThreadRelationShouldWork(): void
    {
        $like = new ThreadLike();
        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);

        $like->setThread($thread);

        $this->assertSame($thread, $like->getThread());
    }

    public function testUserRelationShouldWork(): void
    {
        $like = new ThreadLike();
        $user = $this->createMock(UserInterface::class);

        $like->setUser($user);

        $this->assertSame($user, $like->getUser());
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $like = new ThreadLike();

        $result = (string) $like;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $like = new ThreadLike();

        $this->assertNotNull($like);
        $this->assertNull($like->getId());
    }
}
