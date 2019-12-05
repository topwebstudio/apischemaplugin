<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class IPN {

    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * isValidLicense
     * @param type $authToken The Paykickstart vendor’s API Key
     * @param type $licenseKey The customer’s license key
     * @return boolean
     */
    public function isValidLicense($authToken, $licenseKey, $debug = false) {
        $data = $this->call($authToken, $licenseKey, "licenses/status");
        
        if ($debug) {
            return $data;
        }

        if ($data && isset($data['success'])) {
            if ($data['data']['valid'] && $data['data']['active']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate IPN data
     * @param type $data
     * @param type $secret_key
     * @return type
     */
    public function isValidIpn($data, $secret_key) {
        $ipnHash = $data['hash'];
        unset($data['hash'], $data['verification_code']);
        $data = array_filter(array_map('trim', $data));
        ksort($data, SORT_STRING);
        $hash = hash_hmac('sha1', implode("|", $data), $secret_key);
        return $hash == $ipnHash;
    }

    /**
     * 
     * @param type $authToken The Paykickstart vendor’s API Key
     * @param type $licenseKey The customer’s license key
     * @return type
     */
    protected function call($authToken, $licenseKey, $route) {
        $base_url = "https://app.paykickstart.com/api/";

        $url = $base_url . $route;
        $post = false;

        $data = http_build_query([
            'auth_token' => $authToken,
            'license_key' => $licenseKey
        ]);


        $ch = curl_init();
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $url = $url . "?" . $data;
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

}
