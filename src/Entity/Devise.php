<?php

namespace App\Entity;

use App\Repository\DeviseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviseRepository::class)]
class Devise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $xof = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $euro = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $usd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getXof(): ?string
    {
        return $this->xof;
    }

    public function setXof(?string $xof): static
    {
        $this->xof = $xof;

        return $this;
    }

    public function getEuro(): ?string
    {
        return $this->euro;
    }

    public function setEuro(?string $euro): static
    {
        $this->euro = $euro;

        return $this;
    }

    public function getUsd(): ?string
    {
        return $this->usd;
    }

    public function setUsd(?string $usd): static
    {
        $this->usd = $usd;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
