<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Entity;

use ForumBundle\Entity\ForumShareRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 论坛分享记录实体测试
 *
 * @internal
 */
#[CoversClass(ForumShareRecord::class)]
final class ForumShareRecordTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ForumShareRecord();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'user' => ['user', null];
        yield 'type' => ['type', 'thread'];
        yield 'sourceId' => ['sourceId', '123456'];
    }

    public function testForumShareRecordShouldBeInstantiable(): void
    {
        $record = new ForumShareRecord();

        $this->assertInstanceOf(ForumShareRecord::class, $record);
    }

    public function testUserRelationShouldWork(): void
    {
        $record = new ForumShareRecord();
        $user = $this->createMock(UserInterface::class);

        $record->setUser($user);

        $this->assertSame($user, $record->getUser());
    }

    public function testToStringShouldReturnStringRepresentation(): void
    {
        $record = new ForumShareRecord();

        $result = (string) $record;

        $this->assertNotEmpty($result);
    }

    public function testBasicPropertiesShouldWork(): void
    {
        $record = new ForumShareRecord();

        $this->assertNotNull($record);
        $this->assertNull($record->getId());
    }
}
