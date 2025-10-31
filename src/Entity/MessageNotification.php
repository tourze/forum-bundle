<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Enum\MessageActionType;
use ForumBundle\Enum\MessageType;
use ForumBundle\Repository\MessageNotificationRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: MessageNotificationRepository::class)]
#[ORM\Table(name: 'forum_message_notification', options: ['comment' => '论坛消息通知'])]
#[ORM\Index(columns: ['user_id', 'type', 'read_status', 'deleted'], name: 'forum_message_notification_idx_1')]
class MessageNotification implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $sender = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1)]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '评论内容'])]
    private ?string $content = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [MessageType::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 20, enumType: MessageType::class, options: ['comment' => '通知类型  system_notification:系统通知 ; reply:回复;  follow:关注; private_letter:私信; like:点赞'])]
    private ?MessageType $type = null;

    #[Assert\Choice(callback: [MessageActionType::class, 'cases'])]
    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, enumType: MessageActionType::class, options: ['comment' => '操作类型 '])]
    private ?MessageActionType $action = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '跳转路径'])]
    private ?string $path = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '跳转路径类型, complete_url:完整路径， router：路由'])]
    private ?string $pathType = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[ORM\Column(type: Types::STRING, options: ['comment' => '目标id（如thread的id）'])]
    private ?string $targetId = null;

    #[Assert\NotNull]
    #[Assert\Choice(choices: [0, 1])]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '已读状态 0 未读， 1 已读'])]
    private ?int $readStatus = null;

    #[Assert\NotNull]
    #[Assert\Choice(choices: [0, 1])]
    #[IndexColumn]
    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0, 'comment' => '删除状态 0 未删除， 1 已删除'])]
    private ?int $deleted = 0;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getSender(): ?UserInterface
    {
        return $this->sender;
    }

    public function setSender(?UserInterface $sender): void
    {
        $this->sender = $sender;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getType(): ?MessageType
    {
        return $this->type;
    }

    public function setType(MessageType $type): void
    {
        $this->type = $type;
    }

    public function getTargetId(): ?string
    {
        return $this->targetId;
    }

    public function setTargetId(string $targetId): void
    {
        $this->targetId = $targetId;
    }

    public function getReadStatus(): ?int
    {
        return $this->readStatus;
    }

    public function setReadStatus(int $readStatus): void
    {
        $this->readStatus = $readStatus;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getPathType(): ?string
    {
        return $this->pathType;
    }

    public function setPathType(?string $pathType): void
    {
        $this->pathType = $pathType;
    }

    public function getDeleted(): ?int
    {
        return $this->deleted;
    }

    public function setDeleted(?int $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getAction(): ?MessageActionType
    {
        return $this->action;
    }

    public function setAction(?MessageActionType $action): void
    {
        $this->action = $action;
    }

    public function __toString(): string
    {
        return sprintf('%s #%s', 'MessageNotification', $this->id ?? 'new');
    }
}
