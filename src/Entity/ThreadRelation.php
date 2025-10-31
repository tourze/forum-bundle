<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Enum\ThreadRelationType;
use ForumBundle\Repository\ThreadRelationRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: ThreadRelationRepository::class)]
#[ORM\Table(name: 'forum_thread_relation', options: ['comment' => '论坛线程关系'])]
class ThreadRelation implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '来源id'])]
    private string $sourceId;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [ThreadRelationType::class, 'cases'])]
    #[ORM\Column(type: Types::STRING, length: 50, enumType: ThreadRelationType::class, options: ['comment' => '类型'])]
    private ThreadRelationType $sourceType;

    #[ORM\ManyToOne(targetEntity: Thread::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Thread $thread;

    public function getSourceType(): ThreadRelationType
    {
        return $this->sourceType;
    }

    public function setSourceType(ThreadRelationType $sourceType): void
    {
        $this->sourceType = $sourceType;
    }

    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread): void
    {
        $this->thread = $thread;
    }

    public function __toString(): string
    {
        return sprintf('%s #%s', 'ThreadRelation', $this->id ?? 'new');
    }
}
