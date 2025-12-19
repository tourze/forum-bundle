<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\ThreadLike;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 点赞帖子后
 */
final class AfterLikeThread extends Event
{
    private ThreadLike $threadLike;

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

    public function getThreadLike(): ThreadLike
    {
        return $this->threadLike;
    }

    public function setThreadLike(ThreadLike $threadLike): void
    {
        $this->threadLike = $threadLike;
    }
}
