<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Repository\ThreadDimensionRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: ThreadDimensionRepository::class)]
#[ORM\Table(name: 'forum_thread_dimension', options: ['comment' => '帖子维度数据'])]
#[ORM\UniqueConstraint(name: 'forum_thread_dimension_idx_uniq', columns: ['thread_id', 'dimension_id'])]
class ThreadDimension implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    /**
     * @var array<string, mixed>
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '关联数据'])]
    private ?array $context = null;

    #[ORM\ManyToOne(inversedBy: 'dimensions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Thread $thread = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Dimension $dimension = null;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '维度数值'])]
    private ?int $value = null;

    /**
     * @return array<string, mixed>|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * @param array<string, mixed>|null $context
     */
    public function setContext(?array $context): void
    {
        $this->context = $context;
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): void
    {
        $this->thread = $thread;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getDimension(): ?Dimension
    {
        return $this->dimension;
    }

    public function setDimension(?Dimension $dimension): void
    {
        $this->dimension = $dimension;
    }

    public function __toString(): string
    {
        return sprintf('%s #%s', 'ThreadDimension', $this->id ?? 'new');
    }
}
