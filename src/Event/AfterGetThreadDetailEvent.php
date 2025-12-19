<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\Thread;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterGetThreadDetailEvent extends Event
{
    /**
     * @var array<string, mixed>
     */
    private array $extraInfo = [];

    /**
     * 帖子
     */
    private Thread $thread;

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread): void
    {
        $this->thread = $thread;
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtraInfo(): array
    {
        return $this->extraInfo;
    }

    /**
     * @param array<string, mixed> $extraInfo
     */
    public function setExtraInfo(array $extraInfo): void
    {
        $this->extraInfo = $extraInfo;
    }
}
