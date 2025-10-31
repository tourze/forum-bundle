<?php

namespace ForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Enum\ThreadCommentState;
use ForumBundle\Repository\ThreadCommentRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ThreadCommentRepository::class)]
#[ORM\Table(name: 'forum_thread_comment', options: ['comment' => '帖子评论'])]
#[ORM\Index(columns: ['thread_id', 'status'], name: 'forum_thread_comment_idx_1')]
class ThreadComment implements AdminArrayInterface, \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    // Properties handled by TimestampableAware trait:
    // - createTime
    // - updateTime
    // Properties handled by BlameableAware trait:
    // - createdBy
    // - updatedBy
    #[ORM\ManyToOne(targetEntity: Thread::class, inversedBy: 'threadComments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Thread $thread = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $replyUser = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1)]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '评论内容'])]
    private ?string $content = null;

    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 20)]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '父级id'])]
    private ?string $parentId = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [ThreadCommentState::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 20, nullable: false, enumType: ThreadCommentState::class, options: ['default' => ThreadCommentState::AUDIT_PASS, 'comment' => '审核状态 pass：有效， system_delete：系统删除，user_delete：用户删除'])]
    private ?ThreadCommentState $status = ThreadCommentState::AUDIT_PASS;

    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 20)]
    #[IndexColumn]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '根父级id', 'default' => 0])]
    private ?string $rootParentId = '0';

    /**
     * @var Collection<int, ThreadCommentLike>
     */
    #[ORM\OneToMany(mappedBy: 'threadComment', targetEntity: ThreadCommentLike::class, orphanRemoval: true)]
    private Collection $threadCommentLikes;

    #[Assert\NotNull]
    #[IndexColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '最佳'])]
    private ?bool $best = false;

    public function __construct()
    {
        $this->threadCommentLikes = new ArrayCollection();
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): void
    {
        $this->thread = $thread;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return Collection<int, ThreadCommentLike>
     */
    public function getThreadCommentLikes(): Collection
    {
        return $this->threadCommentLikes;
    }

    public function addThreadCommentLike(ThreadCommentLike $threadCommentLike): self
    {
        if (!$this->threadCommentLikes->contains($threadCommentLike)) {
            $this->threadCommentLikes->add($threadCommentLike);
            $threadCommentLike->setThreadComment($this);
        }

        return $this;
    }

    public function removeThreadCommentLike(ThreadCommentLike $threadCommentLike): self
    {
        if ($this->threadCommentLikes->removeElement($threadCommentLike)) {
            // set the owning side to null (unless already changed)
            if ($threadCommentLike->getThreadComment() === $this) {
                $threadCommentLike->setThreadComment(null);
            }
        }

        return $this;
    }

    public function getRootParentId(): ?string
    {
        return $this->rootParentId;
    }

    public function setRootParentId(?string $rootParentId): void
    {
        $this->rootParentId = $rootParentId;
    }

    public function getReplyUser(): ?UserInterface
    {
        return $this->replyUser;
    }

    public function setReplyUser(?UserInterface $replyUser): void
    {
        $this->replyUser = $replyUser;
    }

    public function setBest(?bool $best): void
    {
        $this->best = $best;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'status' => $this->getStatus(),
            'content' => $this->getContent(),
            'best' => $this->isBest(),
            'createdFromIp' => $this->getCreatedFromIp(),
            'updatedFromIp' => $this->getUpdatedFromIp(),
        ];
    }

    public function getStatus(): ?ThreadCommentState
    {
        return $this->status;
    }

    public function setStatus(?ThreadCommentState $status): void
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

    public function isBest(): ?bool
    {
        return $this->best;
    }

    public function __toString(): string
    {
        $preview = null !== $this->content ? mb_substr(strip_tags($this->content), 0, 50) : '';

        return sprintf('Comment: %s...', '' !== $preview ? $preview : 'Empty');
    }
}
