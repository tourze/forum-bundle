<?php

namespace ForumBundle\Tests\Entity;

use Doctrine\Common\Collections\Collection;
use ForumBundle\Entity\Thread;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Thread::class)]
final class ThreadTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Thread();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', 'Test Thread'];
        yield 'content' => ['content', 'Test Content'];
        yield 'status' => ['status', ThreadState::AUDIT_PASS];
        yield 'type' => ['type', ThreadType::USER_THREAD];
    }

    public function testConstructorInitializesCollections(): void
    {
        $thread = new Thread();

        $this->assertInstanceOf(Collection::class, $thread->getThreadLikes());
        $this->assertInstanceOf(Collection::class, $thread->getThreadComments());
        $this->assertInstanceOf(Collection::class, $thread->getThreadMedia());
        $this->assertInstanceOf(Collection::class, $thread->getChannels());
        $this->assertInstanceOf(Collection::class, $thread->getThreadCollect());
        $this->assertInstanceOf(Collection::class, $thread->getDimensions());
    }

    public function testSettersAndGettersWorkCorrectly(): void
    {
        $thread = new Thread();
        $title = 'Test Thread';
        $content = 'Test Content';
        $status = ThreadState::AUDIT_PASS;
        $type = ThreadType::USER_THREAD;

        $thread->setTitle($title);
        $thread->setContent($content);
        $thread->setStatus($status);
        $thread->setType($type);

        $this->assertEquals($title, $thread->getTitle());
        $this->assertEquals($content, $thread->getContent());
        $this->assertEquals($status, $thread->getStatus());
        $this->assertEquals($type, $thread->getType());
    }

    public function testToStringReturnsTitle(): void
    {
        $thread = new Thread();
        $title = 'Test Thread';
        $thread->setTitle($title);

        $this->assertEquals($title, (string) $thread);
    }

    public function testToStringWithNullTitleReturnsEmptyString(): void
    {
        $thread = new Thread();

        $this->assertEquals('', (string) $thread);
    }

    public function testGetLikeCountReturnsCorrectCount(): void
    {
        $thread = new Thread();

        $this->assertEquals(0, $thread->getLikeCount());
    }

    public function testGetCollectCountReturnsCorrectCount(): void
    {
        $thread = new Thread();

        $this->assertEquals(0, $thread->getCollectCount());
    }

    public function testGetCommentCountReturnsCorrectCount(): void
    {
        $thread = new Thread();

        $this->assertEquals(0, $thread->getCommentCount());
    }

    public function testRetrieveLockResourceReturnsCorrectFormat(): void
    {
        $thread = new Thread();
        $thread->setId('123');

        $expectedResource = sprintf('lock_forum_thread_%s', '123');
        $this->assertEquals($expectedResource, $thread->retrieveLockResource());
    }
}
