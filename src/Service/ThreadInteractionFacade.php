<?php

namespace ForumBundle\Service;

use ForumBundle\Entity\ThreadCollect;
use ForumBundle\Entity\ThreadLike;

/**
 * 帖子交互操作门面 - 专注于点赞、收藏等交互功能
 */
readonly class ThreadInteractionFacade
{
    public function __construct(
        private ThreadLikeService $threadLikeService,
        private ThreadCollectService $threadCollectService,
    ) {
    }

    /**
     * 对帖子点赞
     *
     * @return array<string, mixed>
     */
    public function like(ThreadLike $like): array
    {
        return $this->threadLikeService->like($like);
    }

    /**
     * 对帖子收藏
     *
     * @return array<string, mixed>
     */
    public function collect(ThreadCollect $collect): array
    {
        return $this->threadCollectService->collect($collect);
    }
}
