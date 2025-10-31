<?php

namespace ForumBundle\Vo;

class UserInfo
{
    /**
     * @var int 用户id
     */
    private int $userId;

    /**
     * @var string 用户昵称
     */
    private string $nickname = '';

    /**
     * @var string 个性签名
     */
    private string $avatar = '';

    /**
     * @var string 个性签名
     */
    private string $sign = '';

    /**
     * @var int 获赞总数
     */
    private int $likeCount;

    /**
     * @var int 粉丝数量
     */
    private int $fansTotal;

    /**
     * @var int 关注数
     */
    private int $followTotal;

    /**
     * @var int 消息总数
     */
    private int $messageTotal;

    /**
     * @var int 徽章总数
     */
    private int $medalTotal;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getLikeCount(): int
    {
        return $this->likeCount;
    }

    public function setLikeCount(int $likeCount): void
    {
        $this->likeCount = $likeCount;
    }

    public function getFansTotal(): int
    {
        return $this->fansTotal;
    }

    public function setFansTotal(int $fansTotal): void
    {
        $this->fansTotal = $fansTotal;
    }

    public function getFollowTotal(): int
    {
        return $this->followTotal;
    }

    public function setFollowTotal(int $followTotal): void
    {
        $this->followTotal = $followTotal;
    }

    public function getMessageTotal(): int
    {
        return $this->messageTotal;
    }

    public function setMessageTotal(int $messageTotal): void
    {
        $this->messageTotal = $messageTotal;
    }

    public function getMedalTotal(): int
    {
        return $this->medalTotal;
    }

    public function setMedalTotal(int $medalTotal): void
    {
        $this->medalTotal = $medalTotal;
    }
}
