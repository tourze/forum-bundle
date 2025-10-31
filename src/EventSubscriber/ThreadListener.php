<?php

namespace ForumBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use ForumBundle\Entity\Thread;
use ForumBundle\Event\AfterThreadDeleteEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Thread::class)]
class ThreadListener
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function postRemove(Thread $thread): void
    {
        $event = new AfterThreadDeleteEvent();
        $event->setThread($thread);
        $this->eventDispatcher->dispatch($event);
    }
}
