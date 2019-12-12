<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Services\Helpers;

class ApiController extends Controller {

    public function validateApiKeyAction(Request $request, Helpers $helpers) {
        $key = $this->getRequestParameter('api_key', $request);

        // if request has reached this point, api key must be valid
        $data = array(
            'status' => 'success',
            'data' => 'Provided API Key ' . $key . ' is valid.',
            'key' => $helpers->generateDomainKey()
        );

        return $this->returnJsonResponse($data);
    }

    public function getLatestPluginVersionAction(Request $request) {
        $latest = $this->getEntityManager()->getRepository('App:PluginVersion')->getLatestVersion();

        $api = $this->getParameter('api');

        if ($latest) {
            $url = $this->getDownloadURL($request);

            if ($request->attributes->get('apiKeyError')) {
                $url = '';
            }

            // i'm using it behind proxy..
            if ($this->getParameter('router.request_context.scheme') === 'https') {
                $url = str_replace('http:', 'https:', $url);
            }

            $response = array(
                'name' => $api['plugin_name'],
                'version' => $latest->getVersion(),
                'download_url' => $url,
                "homepage" => $api['business_homepage'],
                "requires" => $api['plugin_min_wp_version'],
                "tested" => $latest->getTestedVersion(),
                'upgrade_notice' => $latest->getDescription(),
                'last_updated' => $latest->getDate()->format('Y-m-d H:i:s'),
                "author" => $api['business_name'],
                "author_homepage" => $api['business_homepage'],
                "sections" => array(
                    "description" => $latest->getDescription(),
                    "installation" => $latest->getInstallation(),
                    "changelog" => $latest->getChangelog(),
                ),
                "banners" => array(
                    "low" => $api['plugin_banner_low'],
                    "high" => $api['plugin_banner_high'],
                ),
            );

            if ($request->attributes->get('apiKeyError')) {
                $key = $this->getRequestParameter('api_key', $request);
                
                $response['key'] = serialize(array('key' => $key, 'flag' => false));
            }


            return $this->returnJsonResponse($response);
        }

        exit();
    }

    public function downloadAction(Request $request, $version) {
        $latest = $this->getEntityManager()->getRepository('App:PluginVersion')->findOneByVersion($version);

        if ($latest) {
            $filename = $this->getParameter('upload_directory') . '/' . $latest->getArchive();

            return $this->file($filename);
        }
    }

    public function updateLicenseDataAction(Request $request) {
        $purchase = $this->get('customer_api')->validateApiKey($this->getRequestParameter('api_key', $request));

        // check if the purchase is active
        $active = $this->get('customer_api')->isPurchaseActive($this->getRequestParameter('api_key', $request));

        // domain must be last so it's created if not existing already 
        $domain = $this->getEntityManager()->getRepository('App:Domain')->findOneByDomain($this->get('helpers')->getDomain());

        if ($domain && $domain->getStatus() && $purchase === true && $active) {
            $response = array(
                'status' => 'success',
                'data' => 'We have successfully verified your domain.',
                'key' => $domain->getKey(),
                'flag' => false
            );

            return $this->returnJsonResponse($response);
        } else {

            $response = array(
                'status' => 'error',
                'data' => 'There\'s an issue with your license and some plugin features has been disabled.',
                'flag' => true,
                'key' => ''
            );

            if ($domain) {
                $response['key'] = substr($domain->getKey(), 0, 9);
            }


            return $this->returnJsonResponse($response);
        }
    }

    private function getRequestParameter($parameter, $request) {
        if ($request->isMethod('POST')) {
            return $request->request->get($parameter);
        } else {
            return $request->query->get($parameter);
        }
    }

    private function getDownloadURL($request) {
        $latest = $this->getEntityManager()->getRepository('App:PluginVersion')->getLatestVersion();
        $settings = array(
            'version' => $latest->getVersion(), // the new version!
            'name' => $this->getRequestParameter('name', $request),
            'path' => $this->getRequestParameter('path', $request),
            'domain' => $this->getRequestParameter('domain', $request),
            'current_version' => $this->getRequestParameter('version', $request),
            'api_key' => $this->getRequestParameter('api_key', $request),
        );

        return $this->generateUrl('api_download', $settings, UrlGeneratorInterface::ABSOLUTE_URL);
    }

}
