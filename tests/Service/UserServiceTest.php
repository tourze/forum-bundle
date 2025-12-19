<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use ForumBundle\Enum\MessageType;
use ForumBundle\Service\UserService;
use ForumBundle\Vo\UserInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(UserService::class)]
#[RunTestsInSeparateProcesses]
final class UserServiceTest extends AbstractIntegrationTestCase
{
    private UserService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(UserService::class);
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UserService::class, $this->service);
    }

    public function testGetUserInfoWithNumericUserId(): void
    {
        // 创建一个测试用户标识符
        $userId = 456;

        // 测试在没有数据的情况下返回默认值
        $userInfo = $this->service->getUserInfo($userId);

        $this->assertInstanceOf(UserInfo::class, $userInfo);
        $this->assertEquals($userId, $userInfo->getUserId());
        $this->assertEquals('', $userInfo->getAvatar());
        $this->assertEquals('', $userInfo->getNickname());
        $this->assertEquals(0, $userInfo->getLikeCount());
        $this->assertEquals(0, $userInfo->getFansTotal());
        $this->assertEquals(0, $userInfo->getFollowTotal());
        $this->assertEquals(0, $userInfo->getMessageTotal());
        $this->assertEquals(0, $userInfo->getMedalTotal());
    }
}
