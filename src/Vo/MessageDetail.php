<?php

namespace ForumBundle\Vo;

class MessageDetail
{
    /**
     * @var int 消息id
     */
    private int $id;

    /**
     * @var string 消息内容
     */
    private string $content;

    /**
     * @var string 消息类型
     */
    private string $type;

    /**
     * @var string 目标id
     */
    private string $targetId;

    /**
     * @var string 用户昵称
     */
    private string $userNickname;

    /**
     * @var string 用户头像
     */
    private string $userAvatar;

    /**
     * @var int 用户id
     */
    private int $userId;

    /**
     * @var string 帖子路径
     */
    private string $path;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getTargetId(): string
    {
        return $this->targetId;
    }

    public function setTargetId(string $targetId): void
    {
        $this->targetId = $targetId;
    }

    public function getUserNickname(): string
    {
        return $this->userNickname;
    }

    public function setUserNickname(string $userNickname): void
    {
        $this->userNickname = $userNickname;
    }

    public function getUserAvatar(): string
    {
        return $this->userAvatar;
    }

    public function setUserAvatar(string $userAvatar): void
    {
        $this->userAvatar = $userAvatar;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
