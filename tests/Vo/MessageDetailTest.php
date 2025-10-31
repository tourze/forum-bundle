<?php

namespace ForumBundle\Tests\Vo;

use ForumBundle\Vo\MessageDetail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(MessageDetail::class)]
final class MessageDetailTest extends TestCase
{
    public function testHasRequiredProperties(): void
    {
        $reflection = new \ReflectionClass(MessageDetail::class);
        $this->assertTrue($reflection->hasProperty('id'));
        $this->assertTrue($reflection->hasProperty('content'));
        $this->assertTrue($reflection->hasProperty('type'));
        $this->assertTrue($reflection->hasProperty('targetId'));
        $this->assertTrue($reflection->hasProperty('userNickname'));
        $this->assertTrue($reflection->hasProperty('userAvatar'));
        $this->assertTrue($reflection->hasProperty('userId'));
        $this->assertTrue($reflection->hasProperty('path'));
    }

    public function testConstructorWorksWithoutParameters(): void
    {
        // 直接实例化 MessageDetail VO
        $messageDetail = new MessageDetail();
        $this->assertInstanceOf(MessageDetail::class, $messageDetail);
    }

    public function testIdGetterAndSetter(): void
    {
        $messageDetail = new MessageDetail();
        $testValue = 123;

        $messageDetail->setId($testValue);
        $this->assertSame($testValue, $messageDetail->getId());
    }

    public function testContentGetterAndSetter(): void
    {
        $messageDetail = new MessageDetail();
        $testValue = '测试消息内容';

        $messageDetail->setContent($testValue);
        $this->assertSame($testValue, $messageDetail->getContent());
    }

    public function testTypeGetterAndSetter(): void
    {
        $messageDetail = new MessageDetail();
        $testValue = 'reply';

        $messageDetail->setType($testValue);
        $this->assertSame($testValue, $messageDetail->getType());
    }

    public function testTargetIdGetterAndSetter(): void
    {
        $messageDetail = new MessageDetail();
        $testValue = 'target-123';

        $messageDetail->setTargetId($testValue);
        $this->assertSame($testValue, $messageDetail->getTargetId());
    }

    public function testUserNicknameGetterAndSetter(): void
    {
        $messageDetail = new MessageDetail();
        $testValue = '测试用户';

        $messageDetail->setUserNickname($testValue);
        $this->assertSame($testValue, $messageDetail->getUserNickname());
    }

    public function testUserAvatarGetterAndSetter(): void
    {
        $messageDetail = new MessageDetail();
        $testValue = '/path/to/avatar.jpg';

        $messageDetail->setUserAvatar($testValue);
        $this->assertSame($testValue, $messageDetail->getUserAvatar());
    }

    public function testUserIdGetterAndSetter(): void
    {
        $messageDetail = new MessageDetail();
        $testValue = 456;

        $messageDetail->setUserId($testValue);
        $this->assertSame($testValue, $messageDetail->getUserId());
    }

    public function testPathGetterAndSetter(): void
    {
        $messageDetail = new MessageDetail();
        $testValue = '/forum/thread/123';

        $messageDetail->setPath($testValue);
        $this->assertSame($testValue, $messageDetail->getPath());
    }
}
