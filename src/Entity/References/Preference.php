<?php

namespace App\Entity\References;

use App\Repository\References\PreferenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.preference')]
#[ORM\Entity(repositoryClass: PreferenceRepository::class)]
class Preference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $default_locale = null;

    #[ORM\Column(length: 10)]
    private ?string $monnaie = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefaultLocale(): ?string
    {
        return $this->default_locale;
    }

    public function setDefaultLocale(?string $default_locale): static
    {
        $this->default_locale = $default_locale;

        return $this;
    }

    public function getMonnaie(): ?string
    {
        return $this->monnaie;
    }

    public function setMonnaie(string $monnaie): static
    {
        $this->monnaie = $monnaie;

        return $this;
    }

}
