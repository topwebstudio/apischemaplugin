<?php

namespace App\Controller;

class PurchaseController extends Controller {

    public function indexAction($page) {
        $result = $this->getEntityManager()->getRepository('App:Purchase')->findAll();
        $purchases = $this->get('knp_paginator')->paginate($result, $page, 100);

        return $this->render('purchase/index.html.twig', array(
                    'purchases' => $purchases
        ));
    }

    public function enableDisableAction($id, $action, $domains) {

        $em = $this->getEntityManager();
        $entity = $em->getRepository('App:JVZooPurchase')->find($id);


        if ($entity) {
            $entity->setEnabled($action);
            $em->persist($entity);
            $em->flush();

            if ($domains) {
                $_domain = $this->getEntityManager()->getRepository('App:Domain')->findOneByUid($entity->getApiKey());

                if ($domains == "true") {
                    $this->performDomainEnableDisable($_domain);
                }
            }

            return $this->returnJsonResponse(['status' => "success"]);
        }
    }

}
