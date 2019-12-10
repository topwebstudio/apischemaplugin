<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Domain;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Helpers;

class Api {

    private $em;
    protected $container;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em, Helpers $helpers) {
        $this->container = $container;
        $this->em = $em;
        $this->helpers = $helpers;
    }

    public function validateApiKey($key) {
        if ($this->getPurchase($key)) {
            return true;
        }

        return false;
    }

    public function checkMaximimumNumberOfInstallsReached($key) {
        $purchase = $this->getPurchase($key);

        if ($purchase) {
            $maxLicensedWebsites = $purchase->getProduct()->getLicensedWebsites(); // 100
            $domainToExclude = $this->helpers->getDomain();

            $licensedDomains = $this->em->getRepository('App:Domain')->findLicensedDomainsCount($key, $domainToExclude);
            
            if ($licensedDomains >= $maxLicensedWebsites) {
                return true;
            }
        }

        return false;
    }

    public function isDomainActive() {
        $domainUrl = $this->helpers->getDomain();
        $domain = $this->em->getRepository('App:Domain')->findOneByDomain($domainUrl);

        if ($domain && !$domain->getEnabled()) {
            return false;
        }

        return true;
    }

    public function checkDomainExistWithOtherApiKey($apiKey) {
        $domainUrl = $this->helpers->getDomain();
        $otherApiKeyDomain = $this->em->getRepository('App:Domain')->findOtherApiKeyDomain($apiKey, $domainUrl);

        if ($otherApiKeyDomain) {
            return true;
        }

        return false;
    }

    public function attatchKeyToDomain($apiKey) {
        $domainUrl = $this->container->get('helpers')->getDomain();
        $domain = $this->em->getRepository('App:Domain')->findOneByApiKeyAndDomainUrl($apiKey, $domainUrl);

        if (!$domain) {
            $domain = new Domain();
            $domain->setDomain($domainUrl);

            $license = $this->em->getRepository('App:License')->findOneByLicenseKey($apiKey);


            $key = $this->container->get('helpers')->generateDomainKey($domain);
            $domain->setDomainKey($key);

            $license->addDomain($domain);


            $domain->setEnabled(true);

            $purchase = $this->getPurchase($apiKey);
            $purchase->addDomain($domain);

            $this->em->persist($purchase);
            $this->em->persist($license);

            $this->em->persist($domain);
            $this->em->flush();
        }
    }

    private function getPurchase($key) {
        return $this->em->getRepository('App:Purchase')->getActivePurchaseByApiKey($key);
    }

}
