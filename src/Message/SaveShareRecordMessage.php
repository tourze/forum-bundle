<?php

namespace ForumBundle\Message;

use Tourze\AsyncContracts\AsyncMessageInterface;

final class SaveShareRecordMessage implements AsyncMessageInterface
{
    private string $threadId;

    private string $type;

    private int $userId;

    public function getThreadId(): string
    {
        return $this->threadId;
    }

    public function setThreadId(string $threadId): void
    {
        $this->threadId = $threadId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
