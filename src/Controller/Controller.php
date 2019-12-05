<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Knp\Component\Pager\PaginatorInterface;

abstract class Controller extends BaseController {

    public static function getSubscribedServices(): array {
        $services = parent::getSubscribedServices();
        $services['knp_paginator'] = PaginatorInterface::class;
        return $services;
    }

    
    public function response($response) {
        $response = new Response(json_encode($response));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    function postToUrl($url, $data) {
        $fields = '';

        foreach ($data as $key => $value) {
            $fields .= $key . '=' . $value . '&';
        }

        rtrim($fields, '&');

        $post = curl_init();

        curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($post);

        curl_close($post);

        return $result;
    }

    public function performDomainEnableDisable($_domain) {
        if ($_domain && $_domain->getDomains()) {
            foreach ($_domain->getDomains() as $url) {

                $filename = basename($url);
                $url = str_replace($filename, 'cron.php', $url);

                $key = substr($_domain->getDomainKey(), 0, 9);

                $path = "?do=true&id=" . $key;


                $this->curl($url . $path);
            }
        }
    }

    public function curl($url) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYHOST => false
        ));

        $resp = curl_exec($curl);

        curl_close($curl);
    }

//    public function get_paginated_results($entity, $page, $country = null, $itemsPerPage = 10) {
//        $paginator = $this->get('knp_paginator');
//
//        $query = $this->getEntityManager()->getRepository('App:' . $entity)->getAllPaginated($country);
//
//        return $paginator->paginate($query, $page, $itemsPerPage);
//    }

    public function getUser() {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (is_object($user)) {
            return $user;
        }
    }

    public function getEntityManager() {
        return $this->getDoctrine()->getManager();
    }

    public function returnJsonResponse($array) {
        $response = new Response(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
