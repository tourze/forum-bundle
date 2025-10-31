<?php

namespace ForumBundle\EventSubscriber;

use ForumBundle\Enum\BadgeType;
use ForumBundle\Event\AfterPublishThread;
use ForumBundle\Service\BadgeService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * 处理发布帖子事件
 */
#[WithMonologChannel(channel: 'forum')]
readonly class PublishThreadSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private BadgeService $badgeService,
    ) {
    }

    #[AsEventListener]
    public function onAfterPublishThread(AfterPublishThread $event): void
    {
        $result = $event->getResult();
        $thread = $event->getRecord();
        $user = $thread->getUser();

        // 处理徽章成长逻辑
        if (null !== $user) {
            try {
                $this->badgeService->upgrade($user, BadgeType::THREAD);
            } catch (\Throwable $exception2) {
                $this->logger->error('处理徽章升级逻辑失败', [
                    'error' => $exception2,
                ]);
            }
        }

        $event->setResult($result);
    }
}
