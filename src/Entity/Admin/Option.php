<?php

namespace App\Entity\Admin;

use App\Repository\Admin\OptionRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'admin.option')]
#[ORM\Entity(repositoryClass: OptionRepository::class)]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private $value;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;


    public function __construct(?string $label, ?string $name, ?string $value, ?string $type)
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    /**
     * @param string|null $categorie
     */
    public function setCategorie(?string $categorie): void
    {
        $this->categorie = $categorie;
    }


    public function __toString(): string
    {
        return $this->value;
    }
}
