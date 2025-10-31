<?php

namespace ForumBundle\Tests\Vo;

use ForumBundle\Vo\UserInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(UserInfo::class)]
final class UserInfoTest extends TestCase
{
    public function testHasRequiredProperties(): void
    {
        $reflection = new \ReflectionClass(UserInfo::class);
        $properties = $reflection->getProperties();
        $this->assertGreaterThan(0, count($properties));
    }

    public function testConstructorWorksWithoutParameters(): void
    {
        // VO 应该直接实例化，不依赖容器
        $userInfo = new UserInfo();
        $this->assertInstanceOf(UserInfo::class, $userInfo);
    }

    public function testSettersAndGettersWork(): void
    {
        $userInfo = new UserInfo();

        // 测试 userId
        $userInfo->setUserId(123);
        $this->assertSame(123, $userInfo->getUserId());

        // 测试 nickname
        $userInfo->setNickname('测试用户');
        $this->assertSame('测试用户', $userInfo->getNickname());

        // 测试 avatar
        $userInfo->setAvatar('/path/to/avatar.jpg');
        $this->assertSame('/path/to/avatar.jpg', $userInfo->getAvatar());

        // 测试 sign
        $userInfo->setSign('这是我的个性签名');
        $this->assertSame('这是我的个性签名', $userInfo->getSign());

        // 测试数值类型属性
        $userInfo->setLikeCount(100);
        $this->assertSame(100, $userInfo->getLikeCount());

        $userInfo->setFansTotal(50);
        $this->assertSame(50, $userInfo->getFansTotal());

        $userInfo->setFollowTotal(75);
        $this->assertSame(75, $userInfo->getFollowTotal());

        $userInfo->setMessageTotal(200);
        $this->assertSame(200, $userInfo->getMessageTotal());

        $userInfo->setMedalTotal(10);
        $this->assertSame(10, $userInfo->getMedalTotal());
    }

    public function testDefaultStringValues(): void
    {
        $userInfo = new UserInfo();

        // 验证默认的字符串值
        $this->assertSame('', $userInfo->getNickname());
        $this->assertSame('', $userInfo->getAvatar());
        $this->assertSame('', $userInfo->getSign());
    }
}
