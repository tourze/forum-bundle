<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Repository\ForumShareRecordRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;

#[ORM\Entity(repositoryClass: ForumShareRecordRepository::class, readOnly: true)]
#[ORM\Table(name: 'forum_share_record', options: ['comment' => '帖子分享记录'])]
#[ORM\Index(columns: ['type', 'source_id'], name: 'forum_share_record_idx_forum_share_record_source_id_type')]
class ForumShareRecord implements \Stringable
{
    use SnowflakeKeyAware;
    use CreateTimeAware;
    use CreatedByAware;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '分享类型'])] // 可能需要添加枚举
    #[Assert\NotBlank(message: '分享类型不能为空')]
    #[Assert\Length(max: 20, maxMessage: '分享类型长度不能超过{{ limit }}个字符')]
    private ?string $type = null;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '来源主体主键id'])]
    #[Assert\NotBlank(message: '来源主键ID不能为空')]
    #[Assert\Length(max: 50, maxMessage: '来源主键ID长度不能超过{{ limit }}个字符')]
    private ?string $sourceId = null;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function __toString(): string
    {
        return sprintf('Share %s: %s', $this->type ?? 'unknown', $this->sourceId ?? '');
    }
}
