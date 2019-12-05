<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="purchases")
 * @ORM\Entity(repositoryClass="App\Repository\PurchaseRepository")
 */
class Purchase {

    use EntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var datetime $dateRegistered
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var datetime $dateRegistered
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_updated", type="datetime")
     */
    private $dateUpdated;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActivePurchase;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $buyerEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $buyerFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $buyerLastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $event;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isRebill;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $nextBillingDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentProcessor;

    /**
     * @ORM\Column(type="string")
     */
    private $transactionId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $transactionTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vendorEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vendorFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vendorLastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $verificationCode;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $licenses = [];

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $subscriptionId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="purchases")
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Domain", mappedBy="purchase", orphanRemoval=true)
     */
    private $domains;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
    }

    public function getBuyerEmail(): ?string {
        return $this->buyerEmail;
    }

    public function setBuyerEmail(string $buyerEmail): self {
        $this->buyerEmail = $buyerEmail;

        return $this;
    }

    public function getBuyerFirstName(): ?string {
        return $this->buyerFirstName;
    }

    public function setBuyerFirstName(string $buyerFirstName): self {
        $this->buyerFirstName = $buyerFirstName;

        return $this;
    }

    public function getBuyerLastName(): ?string {
        return $this->buyerLastName;
    }

    public function setBuyerLastName(?string $buyerLastName): self {
        $this->buyerLastName = $buyerLastName;

        return $this;
    }

    public function getEvent(): ?string {
        return $this->event;
    }

    public function setEvent(string $event): self {
        $this->event = $event;

        return $this;
    }

    public function getIsRebill(): ?bool {
        return $this->isRebill;
    }

    public function setIsRebill(?bool $isRebill): self {
        $this->isRebill = $isRebill;

        return $this;
    }

    public function getMode(): ?string {
        return $this->mode;
    }

    public function setMode(?string $mode): self {
        $this->mode = $mode;

        return $this;
    }

    public function getNextBillingDate() {
        return $this->nextBillingDate;
    }

    public function setNextBillingDate($nextBillingDate): self {
        $this->nextBillingDate = $nextBillingDate;

        return $this;
    }

    public function getPaymentProcessor(): ?string {
        return $this->paymentProcessor;
    }

    public function setPaymentProcessor(?string $paymentProcessor): self {
        $this->paymentProcessor = $paymentProcessor;

        return $this;
    }

    public function getTransactionId(): ?string {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): self {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getTransactionTime() {
        return $this->transactionTime;
    }

    public function setTransactionTime($transactionTime): self {
        $this->transactionTime = $transactionTime;

        return $this;
    }

    public function getVendorEmail(): ?string {
        return $this->vendorEmail;
    }

    public function setVendorEmail(?string $vendorEmail): self {
        $this->vendorEmail = $vendorEmail;

        return $this;
    }

    public function getVendorFirstName(): ?string {
        return $this->vendorFirstName;
    }

    public function setVendorFirstName(?string $vendorFirstName): self {
        $this->vendorFirstName = $vendorFirstName;

        return $this;
    }

    public function getVendorLastName(): ?string {
        return $this->vendorLastName;
    }

    public function setVendorLastName(?string $vendorLastName): self {
        $this->vendorLastName = $vendorLastName;

        return $this;
    }

    public function getHash(): ?string {
        return $this->hash;
    }

    public function setHash(string $hash): self {
        $this->hash = $hash;

        return $this;
    }

    public function getVerificationCode(): ?string {
        return $this->verificationCode;
    }

    public function setVerificationCode(string $verificationCode): self {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    public function getLicenses() {
        return $this->licenses;
    }

    public function setLicenses($licenses): self {
        $this->licenses = $licenses;

        return $this;
    }

    public function getSubscriptionId(): ?string {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): self {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    public function getIsActivePurchase(): ?bool {
        return $this->isActivePurchase;
    }

    public function setIsActivePurchase(?bool $isActivePurchase): self {
        $this->isActivePurchase = $isActivePurchase;

        return $this;
    }

    public function getProduct(): ?Product {
        return $this->product;
    }

    public function setProduct(?Product $product): self {
        $this->product = $product;

        return $this;
    }

    public function isActive() {
        if ($this->getEnabled() && $this->getIsActivePurchase()) {
            return true;
        }

        return false;
    }

    /**
     * @return Collection|Domain[]
     */
    public function getDomains(): Collection
    {
        return $this->domains;
    }

    public function addDomain(Domain $domain): self
    {
        if (!$this->domains->contains($domain)) {
            $this->domains[] = $domain;
            $domain->setPurchase($this);
        }

        return $this;
    }

    public function removeDomain(Domain $domain): self
    {
        if ($this->domains->contains($domain)) {
            $this->domains->removeElement($domain);
            // set the owning side to null (unless already changed)
            if ($domain->getPurchase() === $this) {
                $domain->setPurchase(null);
            }
        }

        return $this;
    }

}
