<?php

namespace App\Entity;

use App\Repository\TermRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;


#[ORM\Entity(repositoryClass: TermRepository::class)]
class Term
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $value;

    #[ORM\ManyToMany(targetEntity: Definition::class)]
    #[ORM\JoinTable(name: 'terms_definitions')]
    #[ORM\JoinColumn(name: 'term_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'definition_id', referencedColumnName: 'id')]
    private Collection $definitions;

    #[Pure] public function __construct()
    {
        $this->definitions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param ArrayCollection|Collection $definitions
     * @return Term
     */
    public function setDefinitions(ArrayCollection|Collection $definitions): Term
    {
        $this->definitions = $definitions;
        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getDefinitions(): ArrayCollection|Collection
    {
        return $this->definitions;
    }
}
