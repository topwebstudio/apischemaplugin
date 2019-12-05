<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Extensions extends \Twig_Extension {

    private $em;
    private $container;
    public $settings = [];
    public $templates = [];

    public function __construct(
           // \Doctrine\ORM\EntityManager $em, 
            
            ContainerInterface $container) {
//        $this->em = $em;
        $this->container = $container;
    }

    public function setController(array $controller) {
        $this->controller = $controller;
    }

    public function getName() {
        return 'extensions';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('yesno', array($this, 'yesno')),
            new \Twig_SimpleFunction('active_purchase', array($this, 'active_purchase')),
            new \Twig_SimpleFunction('jsonEncode', array($this, 'jsonEncode')),
        );
    }

    public function jsonEncode($array) {

        return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function yesno($data) {
        if ($data) {
            return "Yes";
        }

        return "No";
    }

    public function active_purchase($key) {
        return $this->container->get('customer_api')->isPurchaseActive($key);
    }

}
