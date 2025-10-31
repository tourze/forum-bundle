<?php

namespace ForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use ForumBundle\Repository\ThreadRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\LockServiceBundle\Model\LockEntity;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ThreadRepository::class)]
#[ORM\Table(name: 'forum_thread', options: ['comment' => '帖子'])]
#[ORM\Index(columns: ['top', 'sort_number', 'create_time'], name: 'forum_thread_idx_forum_thread_sort')]
#[ORM\Index(columns: ['user_id', 'status', 'cover_picture', 'type'], name: 'forum_thread_idx_forum_thread_where')]
class Thread implements AdminArrayInterface, LockEntity, \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    #[ORM\Column(type: Types::STRING, length: 200, nullable: false, options: ['comment' => '标题', 'default' => ''])]
    private string $title = '';

    #[ORM\ManyToOne(targetEntity: UserInterface::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [ThreadState::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 20, nullable: false, enumType: ThreadState::class, options: ['default' => ThreadState::AUDIT_PASS, 'comment' => '审核状态 audit_pass：审核通过， audit_reject：审核拒绝，user_delete：用户删除'])]
    private ?ThreadState $status = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1)]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '帖子内容'])]
    private ?string $content = null;

    #[Assert\Length(max: 300)]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 300, nullable: false, options: ['comment' => '封面图', 'default' => ''])]
    private string $coverPicture = '';

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0, 'comment' => '迁移数据主键'])]
    private int $postId = 0;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [ThreadType::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 20, nullable: false, enumType: ThreadType::class, options: ['default' => ThreadType::USER_THREAD, 'comment' => '帖子类型 user_thread：用户帖子， topic_thread：话题主贴'])]
    private ThreadType $type = ThreadType::USER_THREAD;

    #[Assert\Length(max: 20)]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['default' => '', 'comment' => '标识'])]
    private string $identify = '';

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ['default' => '', 'comment' => '驳回理由'])]
    private string $rejectReason = '';

    /**
     * @var Collection<int, ThreadLike>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: ThreadLike::class, fetch: 'EXTRA_LAZY')]
    private Collection $threadLikes;

    /**
     * @var Collection<int, ThreadComment>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: ThreadComment::class, fetch: 'EXTRA_LAZY')]
    private Collection $threadComments;

    /**
     * @var Collection<int, ThreadMedia>
     */
    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: ThreadMedia::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $threadMedia;

    #[ORM\ManyToOne(targetEntity: Catalog::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Catalog $catalog = null;

    /**
     * @var Collection<int, ThreadCollect>
     */
    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: ThreadCollect::class, fetch: 'EXTRA_LAZY')]
    private Collection $threadCollect;

    /**
     * @var Collection<int, Channel>
     */
    #[ORM\ManyToMany(targetEntity: Channel::class, inversedBy: 'threads', fetch: 'EXTRA_LAZY')]
    private Collection $channels;

    #[Assert\NotNull]
    #[IndexColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否置顶'])]
    private ?bool $top = false;

    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0, 'comment' => '排序'])]
    private int $sortNumber = 0;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '热门'])]
    private bool $hot = false;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '关闭评论'])]
    private bool $closeComment = false;

    #[Assert\NotNull]
    #[IndexColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否为官方发帖'])]
    private ?bool $official = false;

    /**
     * @var Collection<int, ThreadDimension>
     */
    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: ThreadDimension::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $dimensions;

    #[ORM\OneToOne(mappedBy: 'thread', targetEntity: VisitStat::class, cascade: ['persist', 'remove'], fetch: 'EXTRA_LAZY')]
    private ?VisitStat $visitStat = null;

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[CreateTimeColumn]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0, 'comment' => '最后评论时间'])]
    private int $lastCommentTime = 0;

    #[Assert\Type(type: \DateTimeImmutable::class)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '自动发布时间'])]
    private ?\DateTimeImmutable $autoReleaseTime = null;

    #[Assert\Type(type: \DateTimeImmutable::class)]
    #[IndexColumn]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '自动下架时间'])]
    private ?\DateTimeImmutable $autoTakeDownTime = null;

    public function __construct()
    {
        $this->threadLikes = new ArrayCollection();
        $this->threadComments = new ArrayCollection();
        $this->threadMedia = new ArrayCollection();
        $this->threadCollect = new ArrayCollection();
        $this->channels = new ArrayCollection();
        $this->dimensions = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setTop(?bool $top): void
    {
        $this->top = $top;
    }

    public function getLikeCount(): int
    {
        $expressionBuilder = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where($expressionBuilder->eq('status', 1));

        /** @var ArrayCollection<int, ThreadLike> $threadLikes */
        $threadLikes = $this->threadLikes;

        return $threadLikes->matching($criteria)->count();
    }

    /**
     * @return Collection<int, ThreadLike>
     */
    public function getThreadLikes(): Collection
    {
        return $this->threadLikes;
    }

    public function addThreadLike(ThreadLike $threadLike): self
    {
        if (!$this->threadLikes->contains($threadLike)) {
            $this->threadLikes->add($threadLike);
            $threadLike->setThread($this);
        }

        return $this;
    }

    public function removeThreadLike(ThreadLike $threadLike): self
    {
        if ($this->threadLikes->removeElement($threadLike)) {
            // set the owning side to null (unless already changed)
            if ($threadLike->getThread() === $this) {
                // Remove the reference instead of setting to null
                // since ThreadLike::setThread() doesn't accept null
                // This relationship will be handled by Doctrine's orphan removal
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ThreadComment>
     */
    public function getThreadComments(): Collection
    {
        return $this->threadComments;
    }

    public function addThreadComment(ThreadComment $threadComment): self
    {
        if (!$this->threadComments->contains($threadComment)) {
            $this->threadComments->add($threadComment);
            $threadComment->setThread($this);
        }

        return $this;
    }

    public function removeThreadComment(ThreadComment $threadComment): self
    {
        if ($this->threadComments->removeElement($threadComment)) {
            // set the owning side to null (unless already changed)
            if ($threadComment->getThread() === $this) {
                $threadComment->setThread(null);
            }
        }

        return $this;
    }

    public function addThreadMedium(ThreadMedia $threadMedium): self
    {
        if (!$this->threadMedia->contains($threadMedium)) {
            $this->threadMedia->add($threadMedium);
            $threadMedium->setThread($this);
        }

        return $this;
    }

    public function removeThreadMedium(ThreadMedia $threadMedium): self
    {
        if ($this->threadMedia->removeElement($threadMedium)) {
            // set the owning side to null (unless already changed)
            if ($threadMedium->getThread() === $this) {
                $threadMedium->setThread(null);
            }
        }

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getCatalog(): ?Catalog
    {
        return $this->catalog;
    }

    public function setCatalog(?Catalog $catalog): void
    {
        $this->catalog = $catalog;
    }

    public function getCoverPicture(): string
    {
        return $this->coverPicture;
    }

    public function setCoverPicture(string $coverPicture): void
    {
        $this->coverPicture = $coverPicture;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function getRejectReason(): string
    {
        return $this->rejectReason;
    }

    public function setRejectReason(string $rejectReason): void
    {
        $this->rejectReason = $rejectReason;
    }

    public function getCollectCount(): int
    {
        $expressionBuilder = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where($expressionBuilder->eq('valid', true));

        /** @var ArrayCollection<int, ThreadCollect> $threadCollect */
        $threadCollect = $this->threadCollect;

        return $threadCollect->matching($criteria)->count();
    }

    /**
     * @return Collection<int, ThreadCollect>
     */
    public function getThreadCollect(): Collection
    {
        return $this->threadCollect;
    }

    public function addThreadCollect(ThreadCollect $threadCollect): self
    {
        if (!$this->threadCollect->contains($threadCollect)) {
            $this->threadCollect->add($threadCollect);
            $threadCollect->setThread($this);
        }

        return $this;
    }

    public function removeThreadCollect(ThreadCollect $threadCollect): self
    {
        if ($this->threadCollect->removeElement($threadCollect)) {
            // set the owning side to null (unless already changed)
            if ($threadCollect->getThread() === $this) {
                $threadCollect->setThread(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Channel>
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(Channel $channel): self
    {
        if (!$this->channels->contains($channel)) {
            $this->channels->add($channel);
        }

        return $this;
    }

    public function removeChannel(Channel $channel): self
    {
        $this->channels->removeElement($channel);

        return $this;
    }

    public function setHot(bool $hot): void
    {
        $this->hot = $hot;
    }

    public function setCloseComment(bool $closeComment): void
    {
        $this->closeComment = $closeComment;
    }

    public function getIdentify(): string
    {
        return $this->identify;
    }

    public function setIdentify(string $identify): void
    {
        $this->identify = $identify;
    }

    /**
     * @return Collection<int, ThreadDimension>
     */
    public function getDimensions(): Collection
    {
        return $this->dimensions;
    }

    public function addDimension(ThreadDimension $dimension): self
    {
        if (!$this->dimensions->contains($dimension)) {
            $this->dimensions->add($dimension);
            $dimension->setThread($this);
        }

        return $this;
    }

    public function removeDimension(ThreadDimension $dimension): self
    {
        if ($this->dimensions->removeElement($dimension)) {
            // set the owning side to null (unless already changed)
            if ($dimension->getThread() === $this) {
                $dimension->setThread(null);
            }
        }

        return $this;
    }

    public function getVisitStat(): ?VisitStat
    {
        return $this->visitStat;
    }

    public function setVisitStat(?VisitStat $visitStat): void
    {
        $this->visitStat = $visitStat;
    }

    /**
     * @return Collection<int, ThreadMedia>
     */
    public function getThreadMedia(): Collection
    {
        return $this->threadMedia;
    }

    public function getType(): ThreadType
    {
        return $this->type;
    }

    public function setType(ThreadType $type): void
    {
        $this->type = $type;
    }

    public function getLastCommentTime(): int
    {
        return $this->lastCommentTime;
    }

    public function setLastCommentTime(int $lastCommentTime): void
    {
        $this->lastCommentTime = $lastCommentTime;
    }

    public function getAutoReleaseTime(): ?\DateTimeImmutable
    {
        return $this->autoReleaseTime;
    }

    public function setAutoReleaseTime(?\DateTimeImmutable $autoReleaseTime): void
    {
        $this->autoReleaseTime = $autoReleaseTime;
    }

    public function getAutoTakeDownTime(): ?\DateTimeImmutable
    {
        return $this->autoTakeDownTime;
    }

    public function setAutoTakeDownTime(?\DateTimeImmutable $autoTakeDownTime): void
    {
        $this->autoTakeDownTime = $autoTakeDownTime;
    }

    public function getOfficial(): ?bool
    {
        return $this->official;
    }

    public function setOfficial(?bool $official): void
    {
        $this->official = $official;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'content' => $this->getContent(),
            'top' => $this->isTop(),
            'sortNumber' => $this->getSortNumber(),
            'hot' => $this->isHot(),
            'closeComment' => $this->isCloseComment(),
        ];
    }

    public function getStatus(): ?ThreadState
    {
        return $this->status;
    }

    public function setStatus(ThreadState $status): void
    {
        $this->status = $status;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isTop(): ?bool
    {
        return $this->top;
    }

    public function getSortNumber(): int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    public function isHot(): ?bool
    {
        return $this->hot;
    }

    public function isCloseComment(): ?bool
    {
        return $this->closeComment;
    }

    public function retrieveLockResource(): string
    {
        return 'lock_forum_thread_' . $this->getId();
    }

    public function __toString(): string
    {
        return $this->title ?? sprintf('Thread #%s', $this->id ?? 'new');
    }

    public function getCommentCount(): int
    {
        return $this->threadComments->count();
    }
}
