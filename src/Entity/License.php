<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="licenses")
 * @ORM\Entity(repositoryClass="App\Repository\LicenseRepository")
 */
class License {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $licenseKey;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Purchase", inversedBy="licenses")
     */
    private $purchase;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Domain", mappedBy="license")
     */
    private $domains;

    public function __construct() {
        $this->domains = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getLicenseKey(): ?string {
        return $this->licenseKey;
    }

    public function setLicenseKey(string $licenseKey): self {
        $this->licenseKey = $licenseKey;

        return $this;
    }

    public function getPurchase(): ?Purchase {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): self {
        $this->purchase = $purchase;

        return $this;
    }

    /**
     * @return Collection|Domain[]
     */
    public function getDomains(): Collection {
        return $this->domains;
    }

    public function addDomain(Domain $domain): self {
        if (!$this->domains->contains($domain)) {
            $this->domains[] = $domain;
            $domain->setLicense($this);
        }

        return $this;
    }

    public function removeDomain(Domain $domain): self {
        if ($this->domains->contains($domain)) {
            $this->domains->removeElement($domain);
            // set the owning side to null (unless already changed)
            if ($domain->getLicense() === $this) {
                $domain->setLicense(null);
            }
        }

        return $this;
    }

}
