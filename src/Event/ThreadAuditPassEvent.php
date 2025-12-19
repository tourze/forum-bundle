<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\Thread;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

final class ThreadAuditPassEvent extends UserInteractionEvent
{
    private Thread $thread;

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

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread): void
    {
        $this->thread = $thread;
    }
}
