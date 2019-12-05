<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="domains")
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 */
class Domain {

    use EntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=100)
     */
    private $domain;
    
    /**
     * @var string
     *
     * @ORM\Column(name="domain_key", type="string", length=100)
     */
    private $domainKey;    

    /**
     * @var string
     *
     * @ORM\Column(name="enabled", type="boolean", unique=false, nullable=true)
     */
    private $enabled;
    
    /**
     * @var string
     *
     * @ORM\Column(name="domains", type="array", unique=false, nullable=true)
     */
    private $domains;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Purchase", inversedBy="domains")
     * @ORM\JoinColumn(nullable=false)
     */
    private $purchase;
    

 
    public function setDomain($domain) {
        $this->domain = $domain;
    }

    public function getDomain() {
        return $this->domain;
    }
    
    public function setDomainKey($domainKey){
        $this->domainKey = $domainKey;
    }
    
    public function getDomainKey(){
        return $this->domainKey;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    
    public function getKey(){
        return $this->domainKey;
    }
    
    public function setKey($key) {
        $this->domainKey = $key;
    }
    
    public function getDomains(){
        return $this->domains;
    }
    
    
    public function setDomains($domains){
        $this->domains = $domains;
    }

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): self
    {
        $this->purchase = $purchase;

        return $this;
    }
    
    
}
