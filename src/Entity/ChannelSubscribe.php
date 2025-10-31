<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Repository\ChannelSubscribeRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: ChannelSubscribeRepository::class)]
#[ORM\Table(name: 'forum_channel_subscribe', options: ['comment' => '频道订阅信息'])]
#[ORM\UniqueConstraint(name: 'forum_channel_subscribe_idx_uniq', columns: ['user_id', 'channel_id'])]
class ChannelSubscribe implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Groups(groups: ['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE', options: ['comment' => '用户'])]
    private ?UserInterface $user = null;

    #[ORM\ManyToOne(inversedBy: 'subscribes')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE', options: ['comment' => '频道'])]
    private ?Channel $channel = null;

    public function __toString(): string
    {
        $user = $this->getUser();
        $userName = '';
        if (null !== $user) {
            if (method_exists($user, 'getNickName')) {
                $nickname = $user->getNickName();
                $userName = is_string($nickname) ? $nickname : $user->getUserIdentifier();
            } else {
                $userName = $user->getUserIdentifier();
            }
        }

        return "{$userName} 订阅 {$this->getChannel()?->getTitle()} 于 {$this->getCreateTime()?->format('Y-m-d H:i:s')}";
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): void
    {
        $this->channel = $channel;
    }
}
