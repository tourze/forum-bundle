<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Channel;
use ForumBundle\Entity\ChannelSubscribe;
use ForumBundle\Entity\Thread;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 频道实体测试
 *
 * @internal
 */
#[CoversClass(Channel::class)]
final class ChannelTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Channel();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', '测试标题'];
        yield 'valid' => ['valid', true];
    }

    public function testCreateChannelShouldSetProperties(): void
    {
        $channel = new Channel();
        $channel->setTitle('测试频道');
        $channel->setValid(true);

        $this->assertSame('测试频道', $channel->getTitle());
        $this->assertTrue($channel->isValid());
    }

    public function testToStringShouldReturnTitle(): void
    {
        $channel = new Channel();
        $channel->setTitle('测试频道');

        $this->assertSame('测试频道', (string) $channel);
    }

    public function testToStringWithNullTitleShouldReturnEmptyString(): void
    {
        $channel = new Channel();

        $this->assertSame('', (string) $channel);
    }

    public function testSettersAndGettersShouldWorkCorrectly(): void
    {
        $channel = new Channel();

        $channel->setTitle('新频道');
        $this->assertSame('新频道', $channel->getTitle());

        $channel->setValid(false);
        $this->assertFalse($channel->isValid());
    }

    public function testValidPropertyShouldHaveDefaultValue(): void
    {
        $channel = new Channel();

        $this->assertFalse($channel->isValid());
    }

    public function testThreadsCollectionShouldBeInitialized(): void
    {
        $channel = new Channel();

        $this->assertCount(0, $channel->getThreads());
    }

    public function testAddThreadShouldAddToCollection(): void
    {
        $channel = new Channel();
        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);
        $thread->expects($this->once())->method('addChannel')->with($channel);

        $channel->addThread($thread);

        $this->assertCount(1, $channel->getThreads());
        $this->assertTrue($channel->getThreads()->contains($thread));
    }

    public function testRemoveThreadShouldRemoveFromCollection(): void
    {
        $channel = new Channel();
        /*
         * Mock Thread Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $thread = $this->createMock(Thread::class);
        $thread->expects($this->once())->method('addChannel')->with($channel);
        $thread->expects($this->once())->method('removeChannel')->with($channel);

        $channel->addThread($thread);
        $channel->removeThread($thread);

        $this->assertCount(0, $channel->getThreads());
    }

    public function testSubscribesCollectionShouldBeInitialized(): void
    {
        $channel = new Channel();

        $this->assertCount(0, $channel->getSubscribes());
    }

    public function testAddSubscribeShouldAddToCollection(): void
    {
        $channel = new Channel();
        /*
         * Mock ChannelSubscribe Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $subscribe = $this->createMock(ChannelSubscribe::class);
        $subscribe->expects($this->once())->method('setChannel')->with($channel);

        $channel->addSubscribe($subscribe);

        $this->assertCount(1, $channel->getSubscribes());
        $this->assertTrue($channel->getSubscribes()->contains($subscribe));
    }

    public function testRetrieveApiArrayShouldReturnCorrectStructure(): void
    {
        $channel = new Channel();
        $channel->setTitle('测试频道');
        $channel->setValid(true);

        $array = $channel->retrieveApiArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('valid', $array);
        $this->assertSame('测试频道', $array['title']);
        $this->assertTrue($array['valid']);
    }

    public function testRetrieveAdminArrayShouldReturnCorrectStructure(): void
    {
        $channel = new Channel();
        $channel->setTitle('测试频道');
        $channel->setValid(true);

        $array = $channel->retrieveAdminArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('valid', $array);
        $this->assertSame('测试频道', $array['title']);
        $this->assertTrue($array['valid']);
    }
}
