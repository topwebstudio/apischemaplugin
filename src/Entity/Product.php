<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product {

    use EntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productId;

    /**
     * @ORM\Column(type="integer")
     */
    private $licensedWebsites;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campaign", inversedBy="products")
     */
    private $campaign;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase", mappedBy="product")
     */
    private $purchases;

    public function __construct()
    {
        $this->purchases = new ArrayCollection();
    }

    public function getLicensedWebsites(): ?int {
        return $this->licensedWebsites;
    }

    public function setLicensedWebsites(int $licensedWebsites): self {
        $this->licensedWebsites = $licensedWebsites;

        return $this;
    }

    public function getCampaignName(): ?string {
        return $this->campaignName;
    }

    public function setCampaignName(string $campaignName): self {
        $this->campaignName = $campaignName;

        return $this;
    }

    public function getProductId(): ?string {
        return $this->productId;
    }

    public function setProductId(string $productId): self {
        $this->productId = $productId;

        return $this;
    }

    public function getProductName(): ?string {
        return $this->productName;
    }

    public function setProductName(string $productName): self {
        $this->productName = $productName;

        return $this;
    }

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * @return Collection|Purchase[]
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->setProduct($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->contains($purchase)) {
            $this->purchases->removeElement($purchase);
            // set the owning side to null (unless already changed)
            if ($purchase->getProduct() === $this) {
                $purchase->setProduct(null);
            }
        }

        return $this;
    }

}
