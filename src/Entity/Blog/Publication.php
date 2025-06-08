<?php

namespace App\Entity\Blog;

use App\Entity\Blog\CategoryPublication;
use App\Repository\Blog\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'blog.publication')]
#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle_publication = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\Column(nullable: true)]
    private ?bool $actif = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategoryPublication $code_category = null;

    #[ORM\OneToMany(mappedBy: 'code_publication', targetEntity: FichierPublication::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $fichierPublications;

    public function __construct()
    {
        $this->fichierPublications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibellePublication(): ?string
    {
        return $this->libelle_publication;
    }

    public function setLibellePublication(string $libelle_publication): static
    {
        $this->libelle_publication = $libelle_publication;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCodeCategory(): ?CategoryPublication
    {
        return $this->code_category;
    }

    public function setCodeCategory(?CategoryPublication $code_category): static
    {
        $this->code_category = $code_category;

        return $this;
    }

    /**
     * @return Collection<int, FichierPublication>
     */
    public function getFichierPublications(): Collection
    {
        return $this->fichierPublications;
    }

    public function addFichierPublication(FichierPublication $fichierPublication): static
    {
        if (!$this->fichierPublications->contains($fichierPublication)) {
            $this->fichierPublications->add($fichierPublication);
            $fichierPublication->setCodePublication($this);
        }

        return $this;
    }

    public function removeFichierPublication(FichierPublication $fichierPublication): static
    {
        if ($this->fichierPublications->removeElement($fichierPublication)) {
            // set the owning side to null (unless already changed)
            if ($fichierPublication->getCodePublication() === $this) {
                $fichierPublication->setCodePublication(null);
            }
        }

        return $this;
    }
}
