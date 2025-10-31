<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadComment;
use ForumBundle\Entity\ThreadCommentLike;
use ForumBundle\Enum\ThreadCommentState;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 主题评论实体测试
 *
 * @internal
 */
#[CoversClass(ThreadComment::class)]
final class ThreadCommentTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ThreadComment();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'content' => ['content', '测试评论内容'];
        yield 'parentId' => ['parentId', '123456'];
        yield 'rootParentId' => ['rootParentId', '0'];
        yield 'best' => ['best', false];
    }

    public function testCreateThreadCommentShouldSetProperties(): void
    {
        $comment = new ThreadComment();
        $comment->setContent('测试评论内容');
        $comment->setStatus(ThreadCommentState::AUDIT_PASS);

        $this->assertSame('测试评论内容', $comment->getContent());
        $this->assertSame(ThreadCommentState::AUDIT_PASS, $comment->getStatus());
    }

    public function testToStringShouldReturnContent(): void
    {
        $comment = new ThreadComment();
        $comment->setContent('测试评论');

        $this->assertSame('Comment: 测试评论...', (string) $comment);
    }

    public function testToStringWithNullContentShouldReturnEmptyString(): void
    {
        $comment = new ThreadComment();

        $this->assertSame('Comment: Empty...', (string) $comment);
    }

    public function testSettersAndGettersShouldWorkCorrectly(): void
    {
        $comment = new ThreadComment();

        $comment->setContent('新评论内容');
        $this->assertSame('新评论内容', $comment->getContent());

        $comment->setStatus(ThreadCommentState::SYSTEM_DELETE);
        $this->assertSame(ThreadCommentState::SYSTEM_DELETE, $comment->getStatus());
    }

    public function testStatusPropertyShouldHaveDefaultValue(): void
    {
        $comment = new ThreadComment();

        $this->assertSame(ThreadCommentState::AUDIT_PASS, $comment->getStatus());
    }

    public function testThreadRelationShouldWork(): void
    {
        $comment = new ThreadComment();
        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);

        $comment->setThread($thread);

        $this->assertSame($thread, $comment->getThread());
    }

    public function testUserRelationShouldWork(): void
    {
        $comment = new ThreadComment();
        $user = $this->createMock(UserInterface::class);

        $comment->setUser($user);

        $this->assertSame($user, $comment->getUser());
    }

    public function testReplyUserRelationShouldWork(): void
    {
        $comment = new ThreadComment();
        $user = $this->createMock(UserInterface::class);

        $comment->setReplyUser($user);

        $this->assertSame($user, $comment->getReplyUser());
    }

    public function testParentIdPropertyShouldWork(): void
    {
        $comment = new ThreadComment();

        $comment->setParentId('123456');

        $this->assertSame('123456', $comment->getParentId());
    }

    public function testRootParentIdPropertyShouldWork(): void
    {
        $comment = new ThreadComment();

        $comment->setRootParentId('789012');

        $this->assertSame('789012', $comment->getRootParentId());
    }

    public function testBestPropertyShouldWork(): void
    {
        $comment = new ThreadComment();

        $comment->setBest(true);

        $this->assertTrue($comment->isBest());
    }

    public function testThreadCommentLikesCollectionShouldBeInitialized(): void
    {
        $comment = new ThreadComment();

        $this->assertCount(0, $comment->getThreadCommentLikes());
    }

    public function testAddThreadCommentLikeShouldAddToCollection(): void
    {
        $comment = new ThreadComment();
        /*
         * Mock ThreadCommentLike Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $like = $this->createMock(ThreadCommentLike::class);
        $like->expects($this->once())->method('setThreadComment')->with($comment);

        $comment->addThreadCommentLike($like);

        $this->assertCount(1, $comment->getThreadCommentLikes());
        $this->assertTrue($comment->getThreadCommentLikes()->contains($like));
    }

    public function testRetrieveAdminArrayShouldReturnCorrectStructure(): void
    {
        $comment = new ThreadComment();
        $comment->setContent('测试内容');
        $comment->setBest(true);

        $array = $comment->retrieveAdminArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('content', $array);
        $this->assertArrayHasKey('best', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertSame('测试内容', $array['content']);
        $this->assertTrue($array['best']);
    }
}
