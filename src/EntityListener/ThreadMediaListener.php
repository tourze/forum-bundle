<?php

namespace ForumBundle\EntityListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use ForumBundle\Entity\ThreadMedia;

#[AsEntityListener(event: Events::prePersist, entity: ThreadMedia::class)]
final class ThreadMediaListener
{
    public function prePersist(ThreadMedia $threadMedia): void
    {
        if (null === $threadMedia->getType()) {
            $threadMedia->setType('image');
        }

        if (null !== $threadMedia->getPath() && null === $threadMedia->getThumbnail()) {
            $threadMedia->setThumbnail($threadMedia->getPath());
        }

        if (null === $threadMedia->getPath() && null !== $threadMedia->getThumbnail()) {
            $threadMedia->setPath($threadMedia->getThumbnail());
        }
    }
}
