<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="campaigns")
 * @ORM\Entity(repositoryClass="App\Repository\CampaignRepository")
 */
class Campaign {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $campaignName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $campaignId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secretKey;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="campaign")
     */
    private $products;

    public function __construct() {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getCampaignName(): ?string {
        return $this->campaignName;
    }

    public function setCampaignName(string $campaignName): self {
        $this->campaignName = $campaignName;

        return $this;
    }

    public function getCampaignId(): ?string {
        return $this->campaignId;
    }

    public function setCampaignId(string $campaignId): self {
        $this->campaignId = $campaignId;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection {
        return $this->products;
    }

    public function addProduct(Product $product): self {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCampaign($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCampaign() === $this) {
                $product->setCampaign(null);
            }
        }

        return $this;
    }

    public function getNicename() {
        return $this->getCampaignName() . ' (' . $this->getCampaignId() . ')';
    }

    public function getSecretKey(): ?string {
        return $this->secretKey;
    }

    public function setSecretKey(string $secretKey): self {
        $this->secretKey = $secretKey;

        return $this;
    }

}
