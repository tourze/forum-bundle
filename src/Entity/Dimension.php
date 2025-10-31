<?php

namespace ForumBundle\Entity;

// AntdCpBundle 尚未添加到依赖中
// use AntdCpBundle\Builder\Field\DynamicFieldSet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForumBundle\Repository\DimensionRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Table(name: 'forum_dimension', options: ['comment' => '维度配置'])]
#[ORM\Entity(repositoryClass: DimensionRepository::class)]
class Dimension implements \Stringable
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

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '纬度名'])]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 60)]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 60, unique: true, options: ['comment' => '代号'])]
    private ?string $code = null;

    /**
     * @var Collection<int, SortingRule>
     */
    #[Groups(groups: ['admin_curd'])]
    #[ORM\OneToMany(mappedBy: 'dimension', targetEntity: SortingRule::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $sortingRules;

    public function __construct()
    {
        $this->sortingRules = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return "{$this->getTitle()}({$this->getId()})";
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return Collection<int, SortingRule>
     */
    public function getSortingRules(): Collection
    {
        return $this->sortingRules;
    }

    public function addSortingRule(SortingRule $sortingRule): self
    {
        if (!$this->sortingRules->contains($sortingRule)) {
            $this->sortingRules->add($sortingRule);
            $sortingRule->setDimension($this);
        }

        return $this;
    }

    public function removeSortingRule(SortingRule $sortingRule): self
    {
        if ($this->sortingRules->removeElement($sortingRule)) {
            // set the owning side to null (unless already changed)
            if ($sortingRule->getDimension() === $this) {
                $sortingRule->setDimension(null);
            }
        }

        return $this;
    }
}
