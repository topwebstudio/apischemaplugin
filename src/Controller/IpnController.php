<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Purchase;
use DateTime;
use App\Services\Helpers;
use App\Services\IPN;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IpnController extends Controller {

    public function ipnAction(IPN $ipnService, Helpers $helpers, Request $request, $apiSecret) {

        if ($request->getMethod() !== 'POST') {
            throw new NotFoundHttpException("We expect POST method");
        }

        $data = $request->request->all();
        $em = $this->getEntityManager();


        if (!$data || !isset($data['invoice_id']) || !$data['invoice_id']) {
            throw new NotFoundHttpException("Request is wrong");
        }

        if ($helpers->getSetting('api_secret') !== $apiSecret) {
            throw new NotFoundHttpException("API SECRET missmatch");
        }

        $purchase = $em->getRepository('App:Purchase')->findOneBySubscriptionId($data['invoice_id']);

        if (!$purchase) {
            $newPurchase = true;

            $purchase = new Purchase();

            $purchase->setBuyerEmail($data['buyer_email']);
            $purchase->setBuyerFirstName($data['buyer_first_name']);
            $purchase->setBuyerLastName($data['buyer_last_name']);
            $purchase->setVendorEmail($data['vendor_email']);
            $purchase->setVendorFirstName($data['vendor_first_name']);
            $purchase->setVendorLastName($data['vendor_last_name']);
            $purchase->setHash($data['hash']);
            $purchase->setVerificationCode($data['verification_code']);
            $purchase->setLicenses($data['licenses']);
            $purchase->setSubscriptionId($data['invoice_id']);


            $product = $em->getRepository('App:Product')->findOneByProductId($data['product_id']);

            if (!$product) {
                throw new NotFoundHttpException("Product " . $data['product_id'] . ' not found in our system');
            }

            $product->addPurchase($purchase);
            $em->persist($product);
        } else {
            $product = $purchase->getProduct();
        }

        $purchase->setEvent($data['event']);
        $purchase->setIsRebill($data['is_rebill']);
        $purchase->setMode($data['mode']);

        if (isset($data['next_billing_date'])) {
            $date = DateTime::createFromFormat('U', $data['next_billing_date']);
            $purchase->setNextBillingDate($date);
        }

        $purchase->setPaymentProcessor($data['payment_processor']);
        $purchase->setTransactionId($data['transaction_id']);

        $date = DateTime::createFromFormat('U', $data['transaction_time']);
        $purchase->setTransactionTime($date);

        if (in_array($data['event'], ['refund', 'subscription-cancelled', 'subscription-trial-end', 'subscription-payment-failed'])) {
            $purchase->setIsActivePurchase(false);
        } else {

            $verified = false;
            if ($ipnService->isValidIpn($data, $product->getCampaign()->getSecretKey())) {
                $key = is_array($data['licenses']) ? $data['licenses'][0] : $data['licenses'];

                if ($ipnService->isValidLicense($helpers->getSetting('api_key'), $key)) {
                    $verified = true;

                    if (isset($newPurchase)) {
                        $purchase->setEnabled(true);
                    }
                }
            }

            $purchase->setIsActivePurchase($verified);
        }


        $em->persist($purchase);

        $em->flush();

        exit('OK');
    }

}
