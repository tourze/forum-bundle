<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\ThreadComment;
use ForumBundle\Entity\ThreadCommentLike;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 主题评论点赞实体测试
 *
 * @internal
 */
#[CoversClass(ThreadCommentLike::class)]
final class ThreadCommentLikeTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ThreadCommentLike();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'status' => ['status', 1];
    }

    public function testThreadCommentLikeShouldBeInstantiable(): void
    {
        $like = new ThreadCommentLike();

        $this->assertInstanceOf(ThreadCommentLike::class, $like);
    }

    public function testThreadCommentRelationShouldWork(): void
    {
        $like = new ThreadCommentLike();
        /*
         * Mock ThreadComment Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $comment = $this->createMock(ThreadComment::class);

        $like->setThreadComment($comment);

        $this->assertSame($comment, $like->getThreadComment());
    }

    public function testUserRelationShouldWork(): void
    {
        $like = new ThreadCommentLike();
        $user = $this->createMock(UserInterface::class);

        $like->setUser($user);

        $this->assertSame($user, $like->getUser());
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $like = new ThreadCommentLike();

        $result = (string) $like;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $like = new ThreadCommentLike();

        $this->assertNotNull($like);
        $this->assertNull($like->getId());
    }
}
