<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\ThreadComment;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 评论帖子后
 */
class AfterCommentThread extends Event
{
    private ThreadComment $threadComment;

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

    public function getThreadComment(): ThreadComment
    {
        return $this->threadComment;
    }

    public function setThreadComment(ThreadComment $threadComment): void
    {
        $this->threadComment = $threadComment;
    }
}
