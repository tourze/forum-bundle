<?php

namespace ForumBundle\EventSubscriber;

use ForumBundle\Repository\ThreadRepository;
use ForumBundle\Service\SaveShareRecordService;
use ForumBundle\Service\ThreadManagementService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\UserTrackBundle\Event\TrackLogReportEvent;

/**
 * 处理分享上报事件
 */
class TrackLogReportSubscriber
{
    public function __construct(
        private readonly ThreadRepository $threadRepository,
        private readonly SaveShareRecordService $saveShareRecordService,
        private readonly ThreadManagementService $threadManagementService,
    ) {
    }

    #[AsEventListener]
    public function onTrackLogReport(TrackLogReportEvent $event): void
    {
        if ('shareThread' !== $event->getEvent()) {
            return;
        }

        // TrackLogReportEvent doesn't have getUser() and getExtraDataArray() methods

        $trackLog = $event->getTrackLog();

        $user = $trackLog->getReporter();
        if (null === $user) {
            return;
        }

        $params = $trackLog->getParams();
        if ([] === $params) {
            return;
        }

        // TrackLog doesn't have getContextData method
        // Skip share record logic since we can't get contextData
        if (method_exists($user, 'getId')) {
            $userId = $user->getId();
            $threadId = $params['threadId'] ?? null;
            if (is_string($userId) && is_string($threadId) && $userId !== '' && $threadId !== '') {
                $this->saveShareRecordService->save($userId, 'thread', $threadId);
            }
        }

        $thread = $this->threadRepository->find($params['threadId']);
        if (null !== $thread) {
            $this->threadManagementService->threadVisitStat($thread->getId() ?? '', 'visit');
        }
    }
}
