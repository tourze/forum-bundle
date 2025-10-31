<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\MessageNotification;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 消息通知实体测试
 *
 * @internal
 */
#[CoversClass(MessageNotification::class)]
final class MessageNotificationTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MessageNotification();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'user' => ['user', null];
        yield 'sender' => ['sender', null];
        yield 'content' => ['content', '测试消息内容'];
        yield 'targetId' => ['targetId', '123456'];
        yield 'readStatus' => ['readStatus', 0];
        yield 'deleted' => ['deleted', 0];
    }

    public function testMessageNotificationShouldBeInstantiable(): void
    {
        $notification = new MessageNotification();

        $this->assertInstanceOf(MessageNotification::class, $notification);
    }

    public function testUserRelationShouldWork(): void
    {
        $notification = new MessageNotification();
        $user = $this->createMock(UserInterface::class);

        $notification->setUser($user);

        $this->assertSame($user, $notification->getUser());
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $notification = new MessageNotification();

        $result = (string) $notification;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $notification = new MessageNotification();

        $this->assertNotNull($notification);
        $this->assertNull($notification->getId());
    }
}
