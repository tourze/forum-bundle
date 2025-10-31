<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\Channel;
use ForumBundle\Entity\ChannelSubscribe;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 频道订阅实体测试
 *
 * @internal
 */
#[CoversClass(ChannelSubscribe::class)]
final class ChannelSubscribeTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ChannelSubscribe();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'user' => ['user', null];
        yield 'channel' => ['channel', null];
    }

    public function testChannelSubscribeShouldBeInstantiable(): void
    {
        $subscribe = new ChannelSubscribe();

        $this->assertInstanceOf(ChannelSubscribe::class, $subscribe);
    }

    public function testChannelRelationShouldWork(): void
    {
        $subscribe = new ChannelSubscribe();
        /*
         * Mock Channel Entity的原因：
         * 1. Entity类通常没有对应的接口，直接以数据模型实体存在
         * 2. 测试需要控制Entity的属性和方法行为，避免数据库依赖
         * 3. 使用Entity具体类Mock是单元测试的标准做法
         */
        $channel = $this->createMock(Channel::class);

        $subscribe->setChannel($channel);

        $this->assertSame($channel, $subscribe->getChannel());
    }

    public function testUserRelationShouldWork(): void
    {
        $subscribe = new ChannelSubscribe();
        $user = $this->createMock(UserInterface::class);

        $subscribe->setUser($user);

        $this->assertSame($user, $subscribe->getUser());
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $subscribe = new ChannelSubscribe();

        $result = (string) $subscribe;

        $this->assertNotEmpty($result);
    }
}
