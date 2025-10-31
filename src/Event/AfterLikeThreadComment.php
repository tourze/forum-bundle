<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\ThreadCommentLike;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 点赞帖子评论后
 */
class AfterLikeThreadComment extends Event
{
    private ThreadCommentLike $threadCommentLike;

    /**
     * @var array<string, mixed>
     */
    private array $result = [];

    /**
     * @return array<string, mixed>
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param array<string, mixed> $result
     */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    public function getThreadCommentLike(): ThreadCommentLike
    {
        return $this->threadCommentLike;
    }

    public function setThreadCommentLike(ThreadCommentLike $threadCommentLike): void
    {
        $this->threadCommentLike = $threadCommentLike;
    }
}
