<?php

namespace ForumBundle\EventSubscriber;

use ForumBundle\Enum\BadgeType;
use ForumBundle\Service\BadgeService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\UserFollowBundle\Event\AfterFollowUser;

/**
 * 关注用户
 */
#[WithMonologChannel(channel: 'forum')]
final readonly class FollowForumUserSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private BadgeService $badgeService,
    ) {
    }

    #[AsEventListener]
    public function onAfterFollowUser(AfterFollowUser $event): void
    {
        $relation = $event->getFollowRelation();
        // 处理徽章成长逻辑
        $followUser = $relation->getFollowUser();
        if (null !== $followUser) {
            try {
                // 被关注者升级
                $this->badgeService->upgrade($followUser, BadgeType::FANS);
            } catch (\Throwable $exception2) {
                $this->logger->error('处理徽章升级逻辑失败', [
                    'error' => $exception2,
                ]);
            }
        }
    }
}
