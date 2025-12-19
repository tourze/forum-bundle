<?php

namespace ForumBundle\Message;

use Tourze\AsyncContracts\AsyncMessageInterface;

final class CalcDimensionValueMessage implements AsyncMessageInterface
{
    private string $threadId;

    public function getThreadId(): string
    {
        return $this->threadId;
    }

    public function setThreadId(string $threadId): void
    {
        $this->threadId = $threadId;
    }
}
