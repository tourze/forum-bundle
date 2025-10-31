<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Repository\ThreadMediaRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ThreadMediaRepository::class)]
#[ORM\Table(name: 'forum_thread_media', options: ['comment' => '帖子素材'])]
class ThreadMedia implements AdminArrayInterface, \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    // Properties handled by TimestampableAware trait:
    // - createTime
    // - updateTime
    // Properties handled by BlameableAware trait:
    // - createdBy
    // - updatedBy
    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Thread::class, inversedBy: 'threadMedia')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Thread $thread = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '媒体类型'])]
    private ?string $type = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '缩略图'])]
    private ?string $thumbnail = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '来源路径'])]
    private ?string $path = null;

    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0, 'comment' => '大小'])]
    private int $size = 0;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '扩展选项'])]
    private ?string $options = null;

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): void
    {
        $this->thread = $thread;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
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
            'thumbnail' => $this->getThumbnail(),
            'path' => $this->getPath(),
            'size' => $this->getSize(),
            'type' => $this->getType(),
            'options' => $this->getOptions(),
        ];
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): void
    {
        $this->options = $options;
    }

    public function __toString(): string
    {
        return sprintf('Media %s: %s', $this->type ?? 'unknown', $this->path ?? 'no-path');
    }
}
