<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $produits = null;

    #[ORM\Column(nullable: true)]
    private ?float $sousTotal = null;

    #[ORM\Column(nullable: true)]
    private ?float $montantLivraison = null;

    #[ORM\Column(nullable: true)]
    private ?float $montantTotal = null;

    #[ORM\Column(nullable: true)]
    private ?float $deduction = null;

    #[ORM\Column(nullable: true)]
    private ?float $nap = null;

    #[ORM\Column(nullable: true)]
    private ?float $verse = null;

    #[ORM\Column(nullable: true)]
    private ?float $reste = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coupon = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'paniers')]
    private ?Adresse $adresse = null;

    #[ORM\ManyToOne(inversedBy: 'paniers')]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduits(): ?array
    {
        return $this->produits;
    }

    public function setProduits(?array $produits): static
    {
        $this->produits = $produits;

        return $this;
    }

    public function getSousTotal(): ?float
    {
        return $this->sousTotal;
    }

    public function setSousTotal(?float $sousTotal): static
    {
        $this->sousTotal = $sousTotal;

        return $this;
    }

    public function getMontantLivraison(): ?float
    {
        return $this->montantLivraison;
    }

    public function setMontantLivraison(?float $montantLivraison): static
    {
        $this->montantLivraison = $montantLivraison;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(?float $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }

    public function getDeduction(): ?float
    {
        return $this->deduction;
    }

    public function setDeduction(?float $deduction): static
    {
        $this->deduction = $deduction;

        return $this;
    }

    public function getNap(): ?float
    {
        return $this->nap;
    }

    public function setNap(?float $nap): static
    {
        $this->nap = $nap;

        return $this;
    }

    public function getVerse(): ?float
    {
        return $this->verse;
    }

    public function setVerse(?float $verse): static
    {
        $this->verse = $verse;

        return $this;
    }

    public function getReste(): ?float
    {
        return $this->reste;
    }

    public function setReste(?float $reste): static
    {
        $this->reste = $reste;

        return $this;
    }

    public function getCoupon(): ?string
    {
        return $this->coupon;
    }

    public function setCoupon(?string $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): \DateTime
    {
        return $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedValue(): \DateTime
    {
        return $this->updatedAt = new \DateTime();
    }
}
