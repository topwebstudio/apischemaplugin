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
            $productLicensesIssued = $purchase->getLicensesCount(); // 3
            // more than one license
            if ($productLicensesIssued > 1) {
                $licensedDomains = $this->em->getRepository('App:Domain')->findLicensedDomainsCount($key);
                if ($licensedDomains >= $maxLicensedWebsites) {
                    return true;
                }
            } else {
                // for just one license
                if (count($purchase->getDomains()) >= $maxLicensedWebsites) {
                    return true;
                }
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

            $key = $this->container->get('helpers')->generateDomainKey($domain);
            $domain->setDomainKey($key);
            $domain->setLicenseKey($apiKey);

            $domain->setEnabled(true);

            $purchase = $this->getPurchase($apiKey);
            $purchase->addDomain($domain);

            $this->em->persist($purchase);

            $this->em->persist($domain);
            $this->em->flush();
        }
    }

    private function getPurchase($key) {
        return $this->em->getRepository('App:Purchase')->getActivePurchaseByApiKey($key);
    }

}
