<?php

namespace App\Entity\Observateur;

use App\Repository\Observateur\TicketFilesRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'observateur.ticket_files')]
#[ORM\Entity(repositoryClass: TicketFilesRepository::class)]
class TicketFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'ticketFiles')]
    private ?Ticket $code_ticket = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $extension = null;

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

    public function getCodeTicket(): ?Ticket
    {
        return $this->code_ticket;
    }

    public function setCodeTicket(?Ticket $code_ticket): static
    {
        $this->code_ticket = $code_ticket;

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

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }
}
