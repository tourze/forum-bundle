<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Repository\ThreadCommentLikeRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: ThreadCommentLikeRepository::class)]
#[ORM\Table(name: 'forum_thread_comment_like', options: ['comment' => '论坛帖子评论'])]
class ThreadCommentLike implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(targetEntity: ThreadComment::class, inversedBy: 'threadCommentLikes', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ThreadComment $threadComment = null;

    #[Assert\NotNull]
    #[Assert\Choice(choices: [0, 1])]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '点赞状态 0 取消点赞， 1 已点赞'])]
    private ?int $status = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    public function getThreadComment(): ?ThreadComment
    {
        return $this->threadComment;
    }

    public function setThreadComment(?ThreadComment $threadComment): void
    {
        $this->threadComment = $threadComment;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function __toString(): string
    {
        return sprintf('%s #%s', 'ThreadCommentLike', $this->id ?? 'new');
    }
}
