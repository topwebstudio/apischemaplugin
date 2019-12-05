<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class DomainController extends Controller {
 
    public function indexAction(Request $request, $page) {
 
        $result = $this->getEntityManager()->getRepository('App:Domain')->findAll($request->get('key'));
        $domains = $this->get('knp_paginator')->paginate($result, $page, 100);

        return $this->render('domain/index.html.twig', array(
                    'domains' => $domains,
        ));
    }

    public function enableDisableAction($id, $action) {
        $em = $this->getEntityManager();

        if (!is_object($id)) {
            $domain = $em->getRepository('App:Domain')->find($id);
        } else {
            $domain = $id;
        }

        if ($domain) {
            $domain->setStatus($action);
            $em->persist($domain);
            $em->flush();

            $this->performDomainEnableDisable($domain);

            return $this->returnJsonResponse(['success' => 'true']);
        }
    }

    public function searchAction(Request $request) {
        $query = $request->query->get('query');

        $results = $this->getEntityManager()->getRepository('App:Domain')->search(trim($query));

        $array = array();
        foreach ($results as $result) {
            $name = $result->getDomain();

            $url = $this->generateUrl('domain_index', array('key' => $result->getUid()));
            $array['suggestions'][] = array('value' => $name, 'data' => $url);
        }

        return $this->returnJsonResponse($array);
    }

    public function deleteAction($id) {
        $em = $this->getEntityManager();
        $domain = $em->getRepository('App:Domain')->find($id);

        if ($domain) {
            $em->remove($domain);
            $em->flush();

            return $this->returnJsonResponse(['success' => 'true']);
        }
    }

}
