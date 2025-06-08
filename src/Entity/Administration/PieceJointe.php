<?php

namespace App\Entity\Administration;

use App\Repository\Administration\PieceJointeRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.piece_jointe')]
#[ORM\Entity(repositoryClass: PieceJointeRepository::class)]
class PieceJointe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'pieceJointes')]
    private ?Messagerie $code_email = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCodeEmail(): ?Messagerie
    {
        return $this->code_email;
    }

    public function setCodeEmail(?Messagerie $code_email): static
    {
        $this->code_email = $code_email;

        return $this;
    }
}
