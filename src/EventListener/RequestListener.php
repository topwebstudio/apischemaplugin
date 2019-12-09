<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class RequestListener {

    protected $container;
    protected $request;
    protected $error;
    protected $cache;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->cache = new FilesystemAdapter($this->container->get('kernel')->getEnvironment(), 1); // @todo 60
    }  

    public function onKernelRequest(RequestEvent $event) {
        if ($event->isMasterRequest()) {
            $this->request = $this->container->get('request_stack')->getMasterRequest();

            // always allow new version checks to avoid errors
            if ($this->request->attributes->get('_route') === 'api_get_latest_plugin_version') {
                return true;
            }

            // require api_ url to continue with the checks
            if (strpos($this->request->attributes->get('_route'), 'api_') === false) {
                return true;
            }

            $key = md5(serialize([$this->getRequestParameter('path'), $this->getRequestParameter('domain'), $this->getRequestParameter('api_key')]));
            $api_key_databases_cache = $this->cache->getItem('api_valid_request_check_' . $key);

            if ($api_key_databases_cache->isHit() && $api_key_databases_cache->get() === true) {
                return true;
            }

            $this->checkRequiredParameters();
            $this->validateKey();
            $this->checkMaximumNumberOfInstalls();
            $this->isDomainActive();
            $this->domainInstalledWithOtherApiKey();

            if ($this->error) {
                $array = array(
                    'status' => 'error',
                    'data' => $this->error
                );

                $response = new Response(json_encode($array));
                $response->headers->set('Content-Type', 'application/json');
                return $event->setResponse($response);
            }

            $this->container->get('customer_api')->attatchKeyToDomain($this->getRequestParameter('api_key'));

            $api_key_databases_cache->set(true);
            $this->cache->save($api_key_databases_cache);
        }
    }

    /**
     * Require version, current_version, name, path, domain 
     */
    private function checkRequiredParameters() {
        $versionDefined = true;

        if (null === $this->getRequestParameter('version')) {
            if (null === $this->getRequestParameter('current_version')) {
                $versionDefined = false;
            }
        }

        // require name, slug, version and domain parameters 
        if (null === $this->getRequestParameter('name') or null === $this->getRequestParameter('path')
                or!$versionDefined or null === $this->getRequestParameter('domain')) {

            $this->error .= 'Request is wrong. We cannot procceed. ';
        }
    }

    /**
     * Validate Api Key
     */
    private function validateKey() {
        $valid = $this->container->get('customer_api')->validateApiKey($this->getRequestParameter('api_key'));
        if (!$valid) {
            $this->error .= 'Provided API Key ' . $this->getRequestParameter('api_key') . ' is not valid. ';
        }
    }

    /**
     * checkMaximumNumberOfInstalls
     */
    private function checkMaximumNumberOfInstalls() {
        $maximumNumber = $this->container->get('customer_api')->checkMaximimumNumberOfInstallsReached($this->getRequestParameter('api_key'));

        if ($maximumNumber) {
            $this->error .= 'Provided API Key ' . $this->getRequestParameter('api_key') . ' is already installed on maximum number of domains or domain is invalid. ';
        }
    }

    private function isDomainActive() {
        $active = $this->container->get('customer_api')->isDomainActive();
        if (!$active) {
            $this->error .= 'Your domain is deactivated in our records. Please contact Support if you believe this is an error. Be prepared to provide verification details from your purchase. ';
        }
    }

    private function domainInstalledWithOtherApiKey() {
        $existsWithOtherKey = $this->container->get('customer_api')->checkDomainExistWithOtherApiKey($this->getRequestParameter('api_key'));

        if ($existsWithOtherKey) {
            $this->error .= 'Provided API Key ' . $this->getRequestParameter('api_key') . ' cannot be used with this domain because the domain is already registered with other API KEY. '
                    . 'You can contact Support to resolve this issue. Be prepared to provide verification details from your purchase.';
        }
    }

    private function getRequestParameter($parameter) {
        if ($this->request->isMethod('POST')) {
            return $this->request->request->get($parameter);
        } else {
            return $this->request->query->get($parameter);
        }
    }

}
