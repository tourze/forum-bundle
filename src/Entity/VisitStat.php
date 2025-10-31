<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Repository\VisitStatRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: VisitStatRepository::class)]
#[ORM\Table(name: 'forum_thread_visit_stat', options: ['comment' => '访问统计'])]
class VisitStat implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[ORM\OneToOne(inversedBy: 'visitStat', targetEntity: Thread::class, cascade: ['persist'])]
    #[ORM\JoinColumn(unique: true, nullable: true, onDelete: 'CASCADE')]
    private ?Thread $thread = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '总点赞数'])]
    private int $likeTotal = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '总分享数'])]
    private int $shareTotal = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '总评论数'])]
    private int $commentTotal = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '访问数， 拉一次详情算一次访问'])]
    private int $visitTotal = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '收藏数'])]
    private int $collectCount = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '点赞排行'])]
    private int $likeRank = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '分享排行'])]
    private int $shareRank = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '总评论数排行'])]
    private int $commentRank = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '访问数排行'])]
    private int $visitRank = 0;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '收藏数排行'])]
    private int $collectRank = 0;

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): void
    {
        $this->thread = $thread;
    }

    public function getLikeTotal(): int
    {
        return $this->likeTotal;
    }

    public function setLikeTotal(int $likeTotal): void
    {
        $this->likeTotal = $likeTotal;
    }

    public function getShareTotal(): int
    {
        return $this->shareTotal;
    }

    public function setShareTotal(int $shareTotal): void
    {
        $this->shareTotal = $shareTotal;
    }

    public function getCommentTotal(): int
    {
        return $this->commentTotal;
    }

    public function setCommentTotal(int $commentTotal): void
    {
        $this->commentTotal = $commentTotal;
    }

    public function getVisitTotal(): int
    {
        return $this->visitTotal;
    }

    public function setVisitTotal(int $visitTotal): void
    {
        $this->visitTotal = $visitTotal;
    }

    public function getCollectCount(): int
    {
        return $this->collectCount;
    }

    public function setCollectCount(int $collectCount): void
    {
        $this->collectCount = $collectCount;
    }

    public function getLikeRank(): int
    {
        return $this->likeRank;
    }

    public function setLikeRank(int $likeRank): void
    {
        $this->likeRank = $likeRank;
    }

    public function getShareRank(): int
    {
        return $this->shareRank;
    }

    public function setShareRank(int $shareRank): void
    {
        $this->shareRank = $shareRank;
    }

    public function getCommentRank(): int
    {
        return $this->commentRank;
    }

    public function setCommentRank(int $commentRank): void
    {
        $this->commentRank = $commentRank;
    }

    public function getVisitRank(): int
    {
        return $this->visitRank;
    }

    public function setVisitRank(int $visitRank): void
    {
        $this->visitRank = $visitRank;
    }

    public function getCollectRank(): int
    {
        return $this->collectRank;
    }

    public function setCollectRank(int $collectRank): void
    {
        $this->collectRank = $collectRank;
    }

    public function __toString(): string
    {
        return sprintf('%s #%s', 'VisitStat', $this->id ?? 'new');
    }
}
