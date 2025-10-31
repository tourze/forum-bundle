<?php

namespace ForumBundle\Vo;

class ThreadDetail
{
    /**
     * @var string 帖子id
     */
    private string $threadId;

    /**
     * @var int|string 用户id
     */
    private int|string $userId;

    /**
     * @var string 用户头像
     */
    private string $userAvatar;

    /**
     * @var string 用户名
     */
    private string $userName;

    /**
     * @var string 帖子状态
     */
    private string $status;

    /**
     * @var bool 是否关注帖子用户
     */
    private bool $follow = false;

    /**
     * @var string 发布时间
     */
    private string $releaseTime;

    /**
     * @var string 话题名称
     */
    private string $topicName = '';

    /**
     * @var int|string 话题id
     */
    private int|string $topicId = 0;

    /**
     * @var array<mixed> 多图
     */
    private array $mediaFiles = [];

    /**
     * @var string 封面图
     */
    private string $coverPicture = '';

    /**
     * @var string 帖子内容
     */
    private string $content;

    /**
     * @var string 帖子标题
     */
    private string $title;

    /**
     * @var string|null 驳回理由
     */
    private ?string $rejectReason;

    /**
     * @var bool 当前用户是否对当前帖子点赞
     */
    private bool $like = false;

    /**
     * @var bool 当前用户是否对当前帖子收藏
     */
    private bool $collect = false;

    /**
     * @var bool 是否是我的帖子
     */
    private bool $mine = false;

    /**
     * @var int 点赞数
     */
    private int $likeCount = 0;

    /**
     * @var int 收藏数
     */
    private int $collectCount = 0;

    /**
     * @var int 分享总数
     */
    private int $shareCount = 0;

    /**
     * @var int 评论数
     */
    private int $commentCount = 0;

    private bool $top = false;

    private bool $closeComment = false;

    private bool $hot = false;

    /**
     * 额外入参
     *
     * @var array<mixed>
     */
    private array $extraInfo = [];

    /**
     * @var bool 是否为官方发帖
     */
    private bool $official = false;

    /**
     * @var string 帖子分类
     */
    private string $channelsTitle;

    public function getUserId(): int|string
    {
        return $this->userId;
    }

    public function setUserId(int|string $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserAvatar(): string
    {
        return $this->userAvatar;
    }

    public function setUserAvatar(string $userAvatar): void
    {
        $this->userAvatar = $userAvatar;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getReleaseTime(): string
    {
        return $this->releaseTime;
    }

    public function setReleaseTime(string $releaseTime): void
    {
        $this->releaseTime = $releaseTime;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCoverPicture(): string
    {
        return $this->coverPicture;
    }

    public function setCoverPicture(string $coverPicture): void
    {
        $this->coverPicture = $coverPicture;
    }

    public function getTopicName(): string
    {
        return $this->topicName;
    }

    public function setTopicName(string $topicName): void
    {
        $this->topicName = $topicName;
    }

    public function getTopicId(): int|string
    {
        return $this->topicId;
    }

    public function setTopicId(int|string $topicId): void
    {
        $this->topicId = $topicId;
    }

    /**
     * @return array<mixed>
     */
    public function getMediaFiles(): array
    {
        return $this->mediaFiles;
    }

    /**
     * @param array<mixed> $mediaFiles
     */
    public function setMediaFiles(array $mediaFiles): void
    {
        $this->mediaFiles = $mediaFiles;
    }

    public function getLikeCount(): int
    {
        return $this->likeCount;
    }

    public function setLikeCount(int $likeCount): void
    {
        $this->likeCount = $likeCount;
    }

    public function getCollectCount(): int
    {
        return $this->collectCount;
    }

    public function setCollectCount(int $collectCount): void
    {
        $this->collectCount = $collectCount;
    }

    public function getCommentCount(): int
    {
        return $this->commentCount;
    }

    public function setCommentCount(int $commentCount): void
    {
        $this->commentCount = $commentCount;
    }

    public function getShareCount(): int
    {
        return $this->shareCount;
    }

    public function setShareCount(int $shareCount): void
    {
        $this->shareCount = $shareCount;
    }

    public function getLike(): bool
    {
        return $this->like;
    }

    public function setLike(bool $like): void
    {
        $this->like = $like;
    }

    public function getCollect(): bool
    {
        return $this->collect;
    }

    public function setCollect(bool $collect): void
    {
        $this->collect = $collect;
    }

    public function isFollow(): bool
    {
        return $this->follow;
    }

    public function setFollow(bool $follow): void
    {
        $this->follow = $follow;
    }

    public function getMine(): bool
    {
        return $this->mine;
    }

    public function setMine(bool $mine): void
    {
        $this->mine = $mine;
    }

    /**
     * @return int
     */
    public function getThreadId(): int|string
    {
        return $this->threadId;
    }

    /**
     * @param int $threadId
     */
    public function setThreadId(int|string $threadId): void
    {
        $this->threadId = strval($threadId);
    }

    public function isOfficial(): bool
    {
        return $this->official;
    }

    public function setOfficial(bool $official): void
    {
        $this->official = $official;
    }

    public function isTop(): bool
    {
        return $this->top;
    }

    public function setTop(bool $top): void
    {
        $this->top = $top;
    }

    public function isCloseComment(): bool
    {
        return $this->closeComment;
    }

    public function setCloseComment(bool $closeComment): void
    {
        $this->closeComment = $closeComment;
    }

    public function isHot(): bool
    {
        return $this->hot;
    }

    public function setHot(bool $hot): void
    {
        $this->hot = $hot;
    }

    public function getChannelsTitle(): string
    {
        return $this->channelsTitle;
    }

    public function setChannelsTitle(string $channelsTitle): void
    {
        $this->channelsTitle = $channelsTitle;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRejectReason(): ?string
    {
        return $this->rejectReason;
    }

    public function setRejectReason(?string $rejectReason): void
    {
        $this->rejectReason = $rejectReason;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraInfo(): array
    {
        return $this->extraInfo;
    }

    /**
     * @param array<mixed> $extraInfo
     */
    public function setExtraInfo(array $extraInfo): void
    {
        $this->extraInfo = $extraInfo;
    }
}
