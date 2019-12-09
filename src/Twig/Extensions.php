<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class Extensions extends \Twig_Extension {

    private $em;
    private $container;
    public $settings = [];
    public $templates = [];

    public function __construct(ContainerInterface $container, EntityManagerInterface $em) {
        $this->em = $em;
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
            new \Twig_SimpleFunction('jsonEncode', array($this, 'jsonEncode')),
            new \Twig_SimpleFunction('setting', array($this, 'setting')),
        );
    }

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('licenses', [$this, 'licenses'], ['needs_environment' => true, 'is_safe' => ['html']]),
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

    public function setting($key) {
        return $this->container->get('helpers')->getSetting($key);
    }

    public function licenses(Environment $twig, $licenses) {

        $allLicenses= false;
        
        if ($licenses) {
            $licenses = explode(', ', $licenses);

            if (count($licenses) >= 3) {
                $allLicenses = $licenses;
                $licenses = array_slice($licenses, 0, 3);
                
                
            }

            return $twig->render('blocks/licenses.html.twig', compact('licenses', 'allLicenses'));
        }
    }

}
