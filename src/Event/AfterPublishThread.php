<?php

namespace ForumBundle\Event;

use ForumBundle\Entity\Thread;
use Symfony\Contracts\EventDispatcher\Event;

class AfterPublishThread extends Event
{
    /**
     * @var array<string, mixed>
     */
    private array $result = [];

    private Thread $record;

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

    public function getRecord(): Thread
    {
        return $this->record;
    }

    public function setRecord(Thread $record): void
    {
        $this->record = $record;
    }
}
