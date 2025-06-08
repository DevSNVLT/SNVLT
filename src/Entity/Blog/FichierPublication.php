<?php

namespace App\Entity\Blog;

use App\Repository\Blog\FichierPublicationRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'blog.fichier_publication')]
#[ORM\Entity(repositoryClass: FichierPublicationRepository::class)]
class FichierPublication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'fichierPublications')]
    private ?Publication $code_publication = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getCodePublication(): ?Publication
    {
        return $this->code_publication;
    }

    public function setCodePublication(?Publication $code_publication): static
    {
        $this->code_publication = $code_publication;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }
}
