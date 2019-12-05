<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

class Helpers {

    private $container;
    private $em;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em) {
        $this->container = $container;
        $this->em = $em;
    }

    public function getSetting($key) {
        $setting = $this->em->getRepository('App:Setting')->findOneByKey($key);

        if ($setting) {
            return $setting->getValue();
        }
    }

    public function getDomain() {
        $domain = $this->container->get('request_stack')->getMasterRequest()->get('domain');

        if ($domain) {
            $cleaned = $this->clean_url($domain);

            if ($cleaned) {
                $subdomain = $this->extract_subdomains($cleaned);
                if ($subdomain) {
                    $cleaned = $this->str_replace_first($subdomain . '.', '', $cleaned);
                }

                return $cleaned;
            }
        }
    }

    public function extract_domain($domain) {
        if (preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches)) {
            return $matches['domain'];
        } else {
            return $domain;
        }
    }

    public function extract_subdomains($domain) {
        $subdomains = $domain;
        $domain = $this->extract_domain($subdomains);

        $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

        return $subdomains;
    }

    public function clean_url($url) {
        $url = str_replace("http://", "", $url);
        $url = str_replace("https://", "", $url);

        if (strtolower(substr($url, 0, 4)) == 'www.') {
            $url = substr($url, 4);
        }

        $url = explode('/', $url);
        $url = reset($url);
        $url = explode(':', $url);
        $url = reset($url);

        return $url;
    }

    function inverseFirstLastLetter($string) {
        $lastChar = substr($string, -1);
        $firstString = substr($string, 0, 1);
        $withoutFirstAndLastString = substr($string, 1, -1);

        return $lastChar . $withoutFirstAndLastString . $firstString;
    }

    public function generateDomainKey() {
        $domain = $this->getDomain();

        if (!$domain) {
            return false;
        }

        $stringToProcess = $domain;

        if (strlen($stringToProcess) > 5) {
            $stringToProcess = substr($domain, 0, 5);
        }

        if (strlen($domain) === 4) {
            $stringToProcess = $domain . "Z";
        }

        $hash = $this->inverseFirstLastLetter($stringToProcess);
        $combinedString = $hash . $domain . $hash . $domain . $hash; // change here
        $hashed = md5($combinedString);
        $hashed = strtoupper($hashed);

        $domain_key = "DL-" . substr($hashed, 0, 17); // change here

        return $domain_key;
    }

    // make sure the subodmain is replaced (first occurence)
    public function str_replace_first($search, $replace, $subject) {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

}
