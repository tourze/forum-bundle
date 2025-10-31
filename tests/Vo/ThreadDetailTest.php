<?php

namespace ForumBundle\Tests\Vo;

use ForumBundle\Vo\ThreadDetail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ThreadDetail::class)]
final class ThreadDetailTest extends TestCase
{
    public function testHasRequiredProperties(): void
    {
        $reflection = new \ReflectionClass(ThreadDetail::class);
        $properties = $reflection->getProperties();
        $this->assertGreaterThan(0, count($properties));
    }

    public function testConstructorWorksWithoutParameters(): void
    {
        // 直接实例化 ThreadDetail VO
        $threadDetail = new ThreadDetail();
        $this->assertInstanceOf(ThreadDetail::class, $threadDetail);
    }

    public function testSettersAndGetters(): void
    {
        $threadDetail = new ThreadDetail();

        // 测试字符串属性
        $threadDetail->setThreadId('123');
        $this->assertEquals('123', $threadDetail->getThreadId());

        $threadDetail->setTitle('测试标题');
        $this->assertEquals('测试标题', $threadDetail->getTitle());

        $threadDetail->setContent('测试内容');
        $this->assertEquals('测试内容', $threadDetail->getContent());

        $threadDetail->setUserName('测试用户');
        $this->assertEquals('测试用户', $threadDetail->getUserName());

        $threadDetail->setUserAvatar('avatar.jpg');
        $this->assertEquals('avatar.jpg', $threadDetail->getUserAvatar());

        // 测试布尔属性
        $threadDetail->setLike(true);
        $this->assertTrue($threadDetail->getLike());

        $threadDetail->setCollect(true);
        $this->assertTrue($threadDetail->getCollect());

        $threadDetail->setFollow(true);
        $this->assertTrue($threadDetail->isFollow());

        $threadDetail->setMine(true);
        $this->assertTrue($threadDetail->getMine());

        // 测试数值属性
        $threadDetail->setLikeCount(100);
        $this->assertEquals(100, $threadDetail->getLikeCount());

        $threadDetail->setCommentCount(50);
        $this->assertEquals(50, $threadDetail->getCommentCount());

        // 测试数组属性
        $mediaFiles = ['image1.jpg', 'image2.jpg'];
        $threadDetail->setMediaFiles($mediaFiles);
        $this->assertEquals($mediaFiles, $threadDetail->getMediaFiles());

        $extraInfo = ['key' => 'value'];
        $threadDetail->setExtraInfo($extraInfo);
        $this->assertEquals($extraInfo, $threadDetail->getExtraInfo());
    }

    public function testThreadIdStringConversion(): void
    {
        $threadDetail = new ThreadDetail();

        // 测试整数ID被转换为字符串
        $threadDetail->setThreadId(123);
        $this->assertEquals('123', $threadDetail->getThreadId());

        // 测试字符串ID保持不变
        $threadDetail->setThreadId('abc123');
        $this->assertEquals('abc123', $threadDetail->getThreadId());
    }

    public function testDefaultValues(): void
    {
        $threadDetail = new ThreadDetail();

        // 测试默认布尔值
        $this->assertFalse($threadDetail->getLike());
        $this->assertFalse($threadDetail->getCollect());
        $this->assertFalse($threadDetail->isFollow());
        $this->assertFalse($threadDetail->getMine());
        $this->assertFalse($threadDetail->isOfficial());
        $this->assertFalse($threadDetail->isTop());
        $this->assertFalse($threadDetail->isCloseComment());
        $this->assertFalse($threadDetail->isHot());

        // 测试默认数值
        $this->assertEquals(0, $threadDetail->getLikeCount());
        $this->assertEquals(0, $threadDetail->getCollectCount());
        $this->assertEquals(0, $threadDetail->getShareCount());
        $this->assertEquals(0, $threadDetail->getCommentCount());
        $this->assertEquals(0, $threadDetail->getTopicId());

        // 测试默认字符串
        $this->assertEquals('', $threadDetail->getTopicName());
        $this->assertEquals('', $threadDetail->getCoverPicture());

        // 测试默认数组
        $this->assertEquals([], $threadDetail->getMediaFiles());
        $this->assertEquals([], $threadDetail->getExtraInfo());

        // 注意：$rejectReason 属性未初始化，不能直接访问
        // 需要通过setter设置后才能测试
    }

    public function testNullableProperties(): void
    {
        $threadDetail = new ThreadDetail();

        // 测试可空属性的设置和获取
        $threadDetail->setRejectReason(null);
        $this->assertNull($threadDetail->getRejectReason());

        $threadDetail->setRejectReason('驳回原因');
        $this->assertEquals('驳回原因', $threadDetail->getRejectReason());
    }
}
