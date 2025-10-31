<?php

namespace ForumBundle\Service;

use ForumBundle\Entity\Thread;

/**
 * 帖子统计操作门面 - 专注于各种统计数据的更新
 */
readonly class ThreadStatisticsFacade
{
    public function __construct(
        private StatService $statService,
    ) {
    }

    public function updateAllStat(Thread $thread): void
    {
        $this->statService->updateAllStatistics($thread);
    }

    public function updateLikeStat(Thread $thread): void
    {
        $this->statService->updateLikeStatistics($thread);
    }

    public function updateCollectStat(Thread $thread): void
    {
        $this->statService->updateCollectStatistics($thread);
    }

    public function updateShareStat(Thread $thread): void
    {
        $this->statService->updateShareStatistics($thread);
    }

    public function updateCommentStat(Thread $thread): void
    {
        $this->statService->updateCommentStatistics($thread);
    }

    public function updateVisitStat(Thread $thread): void
    {
        $this->statService->updateVisitStatistics($thread);
    }

    public function executeStatUpdateByType(Thread $thread, string $type): void
    {
        match ($type) {
            'like' => $this->updateLikeStat($thread),
            'share' => $this->updateShareStat($thread),
            'comment' => $this->updateCommentStat($thread),
            'visit' => $this->updateVisitStat($thread),
            'collect' => $this->updateCollectStat($thread),
            default => null,
        };
    }
}
