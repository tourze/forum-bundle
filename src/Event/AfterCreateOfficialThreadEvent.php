<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\Thread;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterCreateOfficialThreadEvent extends Event
{
    /**
     * @var array<string, mixed>
     */
    private array $result = [];

    private Thread $thread;

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
