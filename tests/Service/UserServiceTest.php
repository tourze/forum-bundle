<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use ForumBundle\Enum\MessageType;
use ForumBundle\Repository\MessageNotificationRepository;
use ForumBundle\Repository\ThreadCommentLikeRepository;
use ForumBundle\Repository\ThreadLikeRepository;
use ForumBundle\Service\UserService;
use ForumBundle\Vo\UserInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserFollowBundle\Service\FollowService;

/**
 * @internal
 */
#[CoversClass(UserService::class)]
final class UserServiceTest extends TestCase
{
    private UserService $service;

    /** @var ThreadCommentLikeRepository&MockObject */
    private ThreadCommentLikeRepository $threadCommentLikeRepository;

    /** @var ThreadLikeRepository&MockObject */
    private ThreadLikeRepository $threadLikeRepository;

    /** @var FollowService&MockObject */
    private FollowService $followService;

    /** @var MessageNotificationRepository&MockObject */
    private MessageNotificationRepository $messageNotificationRepository;

    protected function setUp(): void
    {
        $this->threadCommentLikeRepository = $this->createMock(ThreadCommentLikeRepository::class);
        $this->threadLikeRepository = $this->createMock(ThreadLikeRepository::class);
        $this->followService = $this->createMock(FollowService::class);
        $this->messageNotificationRepository = $this->createMock(MessageNotificationRepository::class);

        $this->service = new UserService(
            $this->threadCommentLikeRepository,
            $this->threadLikeRepository,
            $this->followService,
            $this->messageNotificationRepository
        );
    }

    public function testGetUserInfoWithBasicUser(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test_user');

        // Mock query builders and queries
        $threadCommentLikeQuery = $this->createMock(Query::class);
        $threadCommentLikeQuery->method('getSingleScalarResult')->willReturn('5');

        $threadCommentLikeQb = $this->createMock(QueryBuilder::class);
        $threadCommentLikeQb->method('select')->willReturn($threadCommentLikeQb);
        $threadCommentLikeQb->method('leftJoin')->willReturn($threadCommentLikeQb);
        $threadCommentLikeQb->method('where')->willReturn($threadCommentLikeQb);
        $threadCommentLikeQb->method('setParameter')->willReturn($threadCommentLikeQb);
        $threadCommentLikeQb->method('andWhere')->willReturn($threadCommentLikeQb);
        $threadCommentLikeQb->method('getQuery')->willReturn($threadCommentLikeQuery);

        $this->threadCommentLikeRepository->method('createQueryBuilder')
            ->willReturn($threadCommentLikeQb)
        ;

        $threadLikeQuery = $this->createMock(Query::class);
        $threadLikeQuery->method('getSingleScalarResult')->willReturn('3');

        $threadLikeQb = $this->createMock(QueryBuilder::class);
        $threadLikeQb->method('select')->willReturn($threadLikeQb);
        $threadLikeQb->method('leftJoin')->willReturn($threadLikeQb);
        $threadLikeQb->method('where')->willReturn($threadLikeQb);
        $threadLikeQb->method('setParameter')->willReturn($threadLikeQb);
        $threadLikeQb->method('andWhere')->willReturn($threadLikeQb);
        $threadLikeQb->method('getQuery')->willReturn($threadLikeQuery);

        $this->threadLikeRepository->method('createQueryBuilder')
            ->willReturn($threadLikeQb)
        ;

        $messageQuery = $this->createMock(Query::class);
        $messageQuery->method('getSingleScalarResult')->willReturn('2');

        $messageQb = $this->createMock(QueryBuilder::class);
        $messageQb->method('select')->willReturn($messageQb);
        $messageQb->method('where')->willReturn($messageQb);
        $messageQb->method('andWhere')->willReturn($messageQb);
        $messageQb->method('setParameter')->willReturn($messageQb);
        $messageQb->method('getQuery')->willReturn($messageQuery);

        $this->messageNotificationRepository->method('createQueryBuilder')
            ->willReturn($messageQb)
        ;

        // Mock follow service methods
        $this->followService->method('getFansCount')->willReturn(10);
        $this->followService->method('getFollowCount')->willReturn(15);

        $userInfo = $this->service->getUserInfo($user);

        $this->assertInstanceOf(UserInfo::class, $userInfo);
        $this->assertEquals(0, $userInfo->getUserId()); // 'test_user' converted to int is 0
        $this->assertEquals('', $userInfo->getAvatar());
        $this->assertEquals('', $userInfo->getNickname());
        $this->assertEquals(8, $userInfo->getLikeCount()); // 5 + 3
        $this->assertEquals(10, $userInfo->getFansTotal());
        $this->assertEquals(14, $userInfo->getFollowTotal()); // 15 - 1
        $this->assertEquals(2, $userInfo->getMessageTotal());
        $this->assertEquals(0, $userInfo->getMedalTotal());
    }

    public function testGetUserInfoWithNumericUserId(): void
    {
        // Test basic functionality without deprecated methods
        $userMock = $this->createMock(UserInterface::class);
        $userMock->method('getUserIdentifier')->willReturn('456');

        // Mock query builders with zero results
        $zeroQuery = $this->createMock(Query::class);
        $zeroQuery->method('getSingleScalarResult')->willReturn('0');

        $zeroQb = $this->createMock(QueryBuilder::class);
        $zeroQb->method('select')->willReturn($zeroQb);
        $zeroQb->method('leftJoin')->willReturn($zeroQb);
        $zeroQb->method('where')->willReturn($zeroQb);
        $zeroQb->method('andWhere')->willReturn($zeroQb);
        $zeroQb->method('setParameter')->willReturn($zeroQb);
        $zeroQb->method('getQuery')->willReturn($zeroQuery);

        $this->threadCommentLikeRepository->method('createQueryBuilder')
            ->willReturn($zeroQb)
        ;
        $this->threadLikeRepository->method('createQueryBuilder')
            ->willReturn($zeroQb)
        ;
        $this->messageNotificationRepository->method('createQueryBuilder')
            ->willReturn($zeroQb)
        ;

        $this->followService->method('getFansCount')->willReturn(0);
        $this->followService->method('getFollowCount')->willReturn(0);

        $userInfo = $this->service->getUserInfo($userMock);

        $this->assertEquals(456, $userInfo->getUserId());
        $this->assertEquals('', $userInfo->getAvatar());
        $this->assertEquals('', $userInfo->getNickname());
    }

    public function testGetUserInfoFollowTotalNotNegative(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test_user');

        // Mock zero results for all queries
        $zeroQuery = $this->createMock(Query::class);
        $zeroQuery->method('getSingleScalarResult')->willReturn('0');

        $zeroQb = $this->createMock(QueryBuilder::class);
        $zeroQb->method('select')->willReturn($zeroQb);
        $zeroQb->method('leftJoin')->willReturn($zeroQb);
        $zeroQb->method('where')->willReturn($zeroQb);
        $zeroQb->method('andWhere')->willReturn($zeroQb);
        $zeroQb->method('setParameter')->willReturn($zeroQb);
        $zeroQb->method('getQuery')->willReturn($zeroQuery);

        $this->threadCommentLikeRepository->method('createQueryBuilder')->willReturn($zeroQb);
        $this->threadLikeRepository->method('createQueryBuilder')->willReturn($zeroQb);
        $this->messageNotificationRepository->method('createQueryBuilder')->willReturn($zeroQb);

        // Test case where followTotal would be negative without max(0, ...)
        $this->followService->method('getFansCount')->willReturn(5);
        $this->followService->method('getFollowCount')->willReturn(0);

        $userInfo = $this->service->getUserInfo($user);

        $this->assertEquals(0, $userInfo->getFollowTotal()); // max(0, 0-1) = 0
    }

    public function testGetUserInfoMessageTypeFilter(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test_user');

        // Mock other queries with zero
        $zeroQuery = $this->createMock(Query::class);
        $zeroQuery->method('getSingleScalarResult')->willReturn('0');

        $zeroQb = $this->createMock(QueryBuilder::class);
        $zeroQb->method('select')->willReturn($zeroQb);
        $zeroQb->method('leftJoin')->willReturn($zeroQb);
        $zeroQb->method('where')->willReturn($zeroQb);
        $zeroQb->method('andWhere')->willReturn($zeroQb);
        $zeroQb->method('setParameter')->willReturn($zeroQb);
        $zeroQb->method('getQuery')->willReturn($zeroQuery);

        $this->threadCommentLikeRepository->method('createQueryBuilder')->willReturn($zeroQb);
        $this->threadLikeRepository->method('createQueryBuilder')->willReturn($zeroQb);
        $this->followService->method('getFansCount')->willReturn(0);
        $this->followService->method('getFollowCount')->willReturn(0);

        // Verify message query uses correct message types
        $messageQb = $this->createMock(QueryBuilder::class);
        $messageQb->method('select')->willReturn($messageQb);
        $messageQb->method('where')->willReturn($messageQb);
        $messageQb->method('andWhere')->willReturn($messageQb);
        $messageQb->method('setParameter')->willReturnCallback(function ($key, $value) use ($messageQb) {
            if ('type' === $key) {
                $this->assertEquals([MessageType::SYSTEM_NOTIFICATION, MessageType::REPLY, MessageType::FOLLOW], $value);
            }

            return $messageQb;
        });
        $messageQb->method('getQuery')->willReturn($zeroQuery);

        $this->messageNotificationRepository->method('createQueryBuilder')
            ->willReturn($messageQb)
        ;

        $userInfo = $this->service->getUserInfo($user);

        // Verify the service was called and returned a UserInfo object
        $this->assertInstanceOf(UserInfo::class, $userInfo);
    }
}
