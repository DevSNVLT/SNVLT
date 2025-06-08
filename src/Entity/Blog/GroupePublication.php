<?php

namespace App\Entity\Blog;

use App\Repository\Blog\GroupePublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'blog.groupe_publication')]
#[ORM\Entity(repositoryClass: GroupePublicationRepository::class)]
class GroupePublication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'code_groupe', targetEntity: CategoryPublication::class)]
    private Collection $categoryPublications;

    public function __construct()
    {
        $this->categoriePublications = new ArrayCollection();
        $this->categoryPublications = new ArrayCollection();
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
     * @return Collection<int, CategoryPublication>
     */
    public function getCategoryPublications(): Collection
    {
        return $this->categoryPublications;
    }

    public function addCategoryPublication(CategoryPublication $categoryPublication): static
    {
        if (!$this->categoryPublications->contains($categoryPublication)) {
            $this->categoryPublications->add($categoryPublication);
            $categoryPublication->setCodeGroupe($this);
        }

        return $this;
    }

    public function removeCategoryPublication(CategoryPublication $categoryPublication): static
    {
        if ($this->categoryPublications->removeElement($categoryPublication)) {
            // set the owning side to null (unless already changed)
            if ($categoryPublication->getCodeGroupe() === $this) {
                $categoryPublication->setCodeGroupe(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->libelle;
    }
}
