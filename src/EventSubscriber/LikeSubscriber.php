<?php

namespace ForumBundle\EventSubscriber;

use ForumBundle\Enum\BadgeType;
use ForumBundle\Event\AfterLikeThread;
use ForumBundle\Event\AfterLikeThreadComment;
use ForumBundle\Service\BadgeService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * 处理点赞事件
 */
#[WithMonologChannel(channel: 'forum')]
final readonly class LikeSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private BadgeService $badgeService,
    ) {
    }

    #[AsEventListener]
    public function onAfterLikeThread(AfterLikeThread $event): void
    {
        $user = $event->getThreadLike()->getUser();
        $result = $event->getResult();
        // 处理徽章成长逻辑
        if (null !== $user) {
            try {
                $this->badgeService->upgrade($user, BadgeType::THREAD_LIKE);
            } catch (\Throwable $exception2) {
                $this->logger->error('处理徽章升级逻辑失败', [
                    'error' => $exception2,
                ]);
            }
        }

        $event->setResult($result);
    }

    #[AsEventListener]
    public function onAfterLikeThreadComment(AfterLikeThreadComment $event): void
    {
        $user = $event->getThreadCommentLike()->getUser();
        $result = $event->getResult();
        // 处理徽章成长逻辑
        if (null !== $user) {
            try {
                $this->badgeService->upgrade($user, BadgeType::THREAD_LIKE);
            } catch (\Throwable $exception2) {
                $this->logger->error('处理徽章升级逻辑失败', [
                    'error' => $exception2,
                ]);
            }
        }

        $event->setResult($result);
    }
}
