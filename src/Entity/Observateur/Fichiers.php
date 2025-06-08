<?php

namespace App\Entity\Observateur;

use App\Repository\Observateur\FichiersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'observateur.fichiers')]
#[ORM\Entity(repositoryClass: FichiersRepository::class)]
class Fichiers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alText = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    private ?Ticket $code_ticket = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAlText(): ?string
    {
        return $this->alText;
    }

    public function setAlText(?string $alText): static
    {
        $this->alText = $alText;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getCodeTicket(): ?Ticket
    {
        return $this->code_ticket;
    }

    public function setCodeTicket(?Ticket $code_ticket): static
    {
        $this->code_ticket = $code_ticket;

        return $this;
    }
}
