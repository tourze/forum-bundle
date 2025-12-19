<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\Thread;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterThreadDeleteEvent extends Event
{
    private Thread $thread;

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread): void
    {
        $this->thread = $thread;
    }
}
