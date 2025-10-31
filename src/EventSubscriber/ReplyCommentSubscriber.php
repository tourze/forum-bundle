<?php

namespace ForumBundle\EventSubscriber;

use ForumBundle\Enum\BadgeType;
use ForumBundle\Event\AfterCommentThread;
use ForumBundle\Service\BadgeService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * 处理回复帖子事件
 */
#[WithMonologChannel(channel: 'forum')]
readonly class ReplyCommentSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private BadgeService $badgeService,
    ) {
    }

    #[AsEventListener]
    public function onAfterCommentThread(AfterCommentThread $event): void
    {
        $user = $event->getThreadComment()->getUser();
        $result = $event->getResult();

        // 处理徽章成长逻辑
        if (null !== $user) {
            try {
                $this->badgeService->upgrade($user, BadgeType::THREAD_COMMENT);
            } catch (\Throwable $exception2) {
                $this->logger->error('处理徽章升级逻辑失败', [
                    'error' => $exception2,
                ]);
            }
        }

        $event->setResult($result);
    }
}
