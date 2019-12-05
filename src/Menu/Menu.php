<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Menu {

    private $factory;

    public function __construct(FactoryInterface $factory) {
        $this->factory = $factory;
    }

    public function menu(RequestStack $requestStack) {
        $menu = $this->factory->createItem('root')->setChildrenAttributes(array('class' => 'nav'));

        $urls = [
            ['', 'Dashboard', 'admin_homepage', 'pe-7s-graph'],
            ['domain_index', 'Domains', 'domain_index', 'pe-7s-plug'], // DOMAINS USING IT; FLIP SWITCH
            ['purchase_index', 'Purchases', 'purchase_index', 'pe-7s-plug'],
            ['product', 'Products', 'product_index', 'pe-7s-plug'],
            ['campaign', 'Campaigns', 'campaign_index', 'pe-7s-plug'],
            ['schema', 'Schema Catalog', 'schemabuilder_index', 'pe-7s-plug'],
            ['schema-author', 'Schema Authors', 'schemaauthor_index', 'pe-7s-plug'],
            ['plugin-version', 'Plugin Versions', 'plugin-version_index', 'pe-7s-plug'],
            ['setting', 'Settings', 'setting_index', 'pe-7s-plug'],
        ];

        return $this->generateMenu($urls, $menu, $requestStack);
    }

    public function generateMenu($urls, $menu, $requestStack) {
        foreach ($urls as $url) {

            $menu->addChild($url[1], array(
                        'route' => $url[2])
                    )->setLinkAttribute('class', 'nav-link')
                    ->setExtra('icon', $url[3]
            );

            if ($url[0] && strpos($requestStack->getMasterRequest()->getPathInfo(), '/' . $url[0]) !== false) {

                $menu[$url[1]]->setCurrent(true);
            }
        }

        return $menu;
    }

}
