<?php

namespace App\Entity\References;

use App\Repository\References\TypeForetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.type_foret')]
#[ORM\Entity(repositoryClass: TypeForetRepository::class)]
#[UniqueEntity(fields: ['libelle'], message: 'This name already exists')]
class TypeForet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'code_type_foret', targetEntity: Foret::class)]
    private Collection $forets;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    public function __construct()
    {
        $this->forets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Foret>
     */
    public function getForets(): Collection
    {
        return $this->forets;
    }

    public function addForet(Foret $foret): static
    {
        if (!$this->forets->contains($foret)) {
            $this->forets->add($foret);
            $foret->setCodeTypeForet($this);
        }

        return $this;
    }

    public function removeForet(Foret $foret): static
    {
        if ($this->forets->removeElement($foret)) {
            // set the owning side to null (unless already changed)
            if ($foret->getCodeTypeForet() === $this) {
                $foret->setCodeTypeForet(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    /**
     * @param string|null $updated_by
     */
    public function setUpdatedBy(?string $updated_by): void
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
    }



    public function __toString(): string
    {
        return $this->libelle;
    }
}
