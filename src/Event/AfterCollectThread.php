<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\ThreadCollect;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterCollectThread extends Event
{
    /**
     * @var array<string, mixed>
     */
    private array $result = [];

    private ThreadCollect $threadCollect;

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

    public function getThreadCollect(): ThreadCollect
    {
        return $this->threadCollect;
    }

    public function setThreadCollect(ThreadCollect $threadCollect): void
    {
        $this->threadCollect = $threadCollect;
    }
}
