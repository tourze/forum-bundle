<?php

namespace ForumBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Repository\SortingRuleRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Table(name: 'forum_sorting_rule', options: ['comment' => '排序规则'])]
#[ORM\Entity(repositoryClass: SortingRuleRepository::class)]
class SortingRule implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '规则名'])]
    private ?string $title = null;

    #[Ignore]
    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'sortingRules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dimension $dimension = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1)]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '规则公式'])]
    private ?string $formula = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return "{$this->getTitle()} {$this->getFormula()}";
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(string $formula): void
    {
        $this->formula = $formula;
    }

    public function getDimension(): ?Dimension
    {
        return $this->dimension;
    }

    public function setDimension(?Dimension $dimension): void
    {
        $this->dimension = $dimension;
    }
}
