<?php

namespace App\Entity\Blog;

use App\Entity\Blog\GroupePublication;
use App\Repository\Blog\CategoryPublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'blog.category_publication')]
#[ORM\Entity(repositoryClass: CategoryPublicationRepository::class)]
class CategoryPublication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'categoryPublications')]
    private ?GroupePublication $code_groupe = null;

    #[ORM\OneToMany(mappedBy: 'code_category', targetEntity: Publication::class)]
    private Collection $publications;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->publications = new ArrayCollection();
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

    public function getCodeGroupe(): ?GroupePublication
    {
        return $this->code_groupe;
    }

    public function setCodeGroupe(?GroupePublication $code_groupe): static
    {
        $this->code_groupe = $code_groupe;

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setCodeCategory($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getCodeCategory() === $this) {
                $publication->setCodeCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
