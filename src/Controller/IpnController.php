<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Purchase;
use App\Entity\License;
use DateTime;
use App\Services\Helpers;
use App\Services\IPN;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;

class IpnController extends Controller {

    public function ipnAction(LoggerInterface $logger, IPN $ipnService, Helpers $helpers, Request $request, $apiSecret) {
        $em = $this->getEntityManager();

        if ($request->getMethod() !== 'POST') {
            throw new NotFoundHttpException("We expect POST method");
        }

        $data = $request->request->all();
        if ($this->getParameter('kernel.environment') == 'dev') {
            $logger->info(serialize($data));
        }

        if (!$data || !isset($data['invoice_id']) || !$data['invoice_id']) {
            throw new NotFoundHttpException("Request is wrong");
        }

        if ($helpers->getSetting('api_secret') !== $apiSecret) {
            throw new NotFoundHttpException("API SECRET MISSMATCH. WE CANT GO ON WITH THIS.");
        }
        
        $purchase = $em->getRepository('App:Purchase')->findOneBySubscriptionId($data['invoice_id']);

        if (!$purchase) {

            $purchase = new Purchase();

            $purchase->setBuyerEmail($data['buyer_email']);
            $purchase->setBuyerFirstName($data['buyer_first_name']);
            $purchase->setBuyerLastName($data['buyer_last_name']);
            $purchase->setVendorEmail($data['vendor_email']);
            $purchase->setVendorFirstName($data['vendor_first_name']);
            $purchase->setVendorLastName($data['vendor_last_name']);
            $purchase->setHash($data['hash']);
            $purchase->setVerificationCode($data['verification_code']);

            $purchase->setSubscriptionId($data['invoice_id']);

            $licenses = explode(', ', $data['licenses']);
            foreach ($licenses as $licenseKey) {
                $license = new License();
                $license->setEnabled(true);
                $license->setLicenseKey($licenseKey);
                $em->persist($license);
                $purchase->addLicense($license);
            }

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
        $purchase->setMode($data['mode']);

        if (isset($data['is_rebill'])) {
            $purchase->setIsRebill($data['is_rebill']);
        }

        if (isset($data['next_billing_date']) && $data['next_billing_date']) {
            $date = DateTime::createFromFormat('U', $data['next_billing_date']);
            $purchase->setNextBillingDate($date);
        }

        $purchase->setPaymentProcessor($data['payment_processor']);
        $purchase->setTransactionId($data['transaction_id']);


        if (isset($data['transaction_time']) && $data['transaction_time']) {
            $date = DateTime::createFromFormat('U', $data['transaction_time']);
            $purchase->setTransactionTime($date);
        }

        if (in_array($data['event'], ['refund', 'subscription-cancelled', 'subscription-trial-end', 'subscription-payment-failed'])) {
            $purchase->setIsActivePurchase(false);
        } else {
            if ($ipnService->isValidIpn($data, $product->getCampaign()->getSecretKey())) {
                $purchase->setIsActivePurchase(true);
                $purchase->setEnabled(true);
            }
        }


        $em->persist($purchase);

        $em->flush();

        exit('OK');
    }

}

//        $data2 = 'a:55:{s:27:"affiliate_commission_amount";s:0:"";s:28:"affiliate_commission_percent";s:0:"";s:15:"affiliate_email";s:0:"";s:20:"affiliate_first_name";s:0:"";s:19:"affiliate_last_name";s:0:"";s:6:"amount";s:6:"297.00";s:17:"billing_address_1";s:0:"";s:17:"billing_address_2";s:0:"";s:12:"billing_city";s:0:"";s:15:"billing_country";s:0:"";s:13:"billing_state";s:0:"";s:11:"billing_zip";s:0:"";s:11:"buyer_email";s:17:"jorjivt@gmail.com";s:16:"buyer_first_name";s:6:"Georgi";s:8:"buyer_ip";s:14:"109.121.249.30";s:15:"buyer_last_name";s:8:"Katsarov";s:14:"buyer_tax_name";s:0:"";s:16:"buyer_tax_number";s:0:"";s:11:"campaign_id";s:5:"10417";s:13:"campaign_name";s:16:"WP Schema Plugin";s:11:"coupon_code";s:0:"";s:11:"coupon_rate";s:0:"";s:11:"coupon_type";s:0:"";s:8:"currency";s:3:"USD";s:5:"event";s:5:"sales";s:10:"invoice_id";s:13:"PK-P1WKXZVO2E";s:4:"mode";s:4:"test";s:17:"next_billing_date";s:0:"";s:17:"payment_processor";s:8:"testmode";s:10:"product_id";s:5:"31043";s:12:"product_name";s:37:"WP Schema Plugin - Agency License 100";s:31:"ref_affiliate_commission_amount";s:0:"";s:32:"ref_affiliate_commission_percent";s:0:"";s:19:"ref_affiliate_email";s:0:"";s:24:"ref_affiliate_first_name";s:0:"";s:23:"ref_affiliate_last_name";s:0:"";s:18:"shipping_address_1";s:0:"";s:18:"shipping_address_2";s:0:"";s:13:"shipping_city";s:0:"";s:16:"shipping_country";s:0:"";s:14:"shipping_state";s:0:"";s:12:"shipping_zip";s:0:"";s:10:"tax_amount";s:0:"";s:11:"tax_percent";s:0:"";s:18:"tax_transaction_id";s:0:"";s:11:"tracking_id";s:0:"";s:14:"transaction_id";s:13:"PK-T1WK48551E";s:16:"transaction_time";s:10:"1576058251";s:16:"transaction_type";s:7:"primary";s:12:"vendor_email";s:22:"mikejmartin3@gmail.com";s:17:"vendor_first_name";s:7:"Michael";s:16:"vendor_last_name";s:6:"Martin";s:8:"licenses";s:2098:"MADX-QWFL-UOBU-MKVG, LBDU-N6QC-QXKH-ZQNE, EUNF-S8GT-KBSG-BQSP, FRCS-7QVY-EMMT-7QAF, 1R9B-Z4LK-J6PZ-TQHT, MR8U-MAAM-BNCP-OKXJ, HTLW-SD0S-U3N1-LHBO, VWQM-QREZ-RDIP-S09S, IBIW-PNL9-VOAH-OOP3, AZJC-HQQD-SUMJ-R0R8, 6QWD-VHPV-MN3G-SL0G, MEDC-QYJ1-NCFB-5HTF, PINZ-6BRR-7GPC-YXHS, RDQD-RKRQ-LILX-JGOB, KDYS-F6V8-T0TW-G9QY, ESX3-1AZB-2ZXC-SS65, R1NI-H9XE-XAHI-JP9I, CN5Q-JAW6-JYCN-DQLF, YASA-52MV-FM86-OKXU, PHBV-JD8J-0XA8-QQBQ, GTWB-B0GU-CKZC-4XIQ, BVQG-IAB8-976T-AOPW, XLOX-MKC6-YPWX-YLG0, O4KR-Q96U-WCMX-DPEI, 0P4F-R2J2-KG27-06JV, SB0A-OIXZ-OL3E-5I5U, LT9C-GC8I-8HF2-HA4E, VGWL-XDJK-DFA6-UO8T, W825-ICPG-B2GF-K683, 9OBV-06E6-XL32-QTXW, IAD1-RUWY-MQE3-NCJO, BL7Q-5FRQ-UCDV-NQXR, YATS-GLCN-L0XE-NFBG, ADFU-CGQ0-WMQX-NGDA, IYPN-J3T3-SPEC-OQAW, PR0I-V9LW-M65D-CYZ4, 713Q-CBYW-TFTY-WYHX, OI71-4OAX-6EJB-9KLI, 1SBC-NBLH-RD9Z-S3OE, SIZZ-TPBL-C02S-ZBCS, AXIF-CYBR-AX3E-R5IV, FSXF-W5YH-7NC4-LZP8, B2E3-EE4K-UYXR-CXJC, 54EX-BPRB-SKOU-LB9E, OR1A-0LSG-SAR5-Q7M7, O9NP-8MOL-QYSD-2VPK, LI7O-QHMB-LYD4-9BFY, GVK2-CCVM-NNUQ-ACQB, DPQF-PUEM-NXYE-7RFG, KCQK-XEUX-BYNV-BNQ7, PFCH-BFSF-XCIV-SDTL, SK4Q-PSVA-KDK6-WOJB, YX5S-ZBIY-MMAO-3SJT, ZPHQ-PUJO-OPNT-QTVC, W5SC-VCTU-VMIO-HJFQ, MEQQ-RRAK-NWIK-2JXE, ZWPC-3JKW-TPH5-F36R, AXRZ-V5QS-QQO0-CWVE, GBPQ-UU5S-6AI0-KPR8, 2P5O-K33Q-LXCF-JIIZ, U0R5-TSRB-SSGU-SBW3, QR95-WTDM-547D-EKZC, 8HDA-CP7R-ZXPK-BXGP, EJ94-NGAB-6QMK-OLF7, PYFP-PZAV-EIKX-UVYW, VWJL-XZU8-ZJQJ-PI6T, BV1Y-NY2G-LDOS-TZVV, HPJA-COCU-ICVZ-HR2B, RIED-VRGT-LLCW-REYH, MATD-IKQ1-HTMW-R931, L7CL-Z4G5-M2PT-NL4M, R2UF-ITEY-JQ4L-5YCL, YGAR-36A2-IJMG-TFFJ, KLVW-OZ17-7JUW-OGBO, 8SGJ-UVMS-PDUU-D2WS, IBZJ-DDI8-JR2N-HWVS, HOYY-8B9H-EE7Z-S5KE, AJDR-TFKK-0QVL-MPAT, A8HG-LK7X-C88B-IFZI, 8NE5-KTGW-QYAS-JN1D, W1NV-DSFO-UD5J-FQYO, ISRJ-WNLV-WPTB-MOH9, UM66-Q3NA-GC4I-DBRZ, HVND-2FR3-QRYE-0E76, XUKT-T8QY-CUU9-DNHI, AL95-JOIL-GQEZ-QLJ6, BSWC-A2VL-ANEP-PRJJ, M8BG-KJWC-T53T-JGWY, 9QWN-ZXUF-WCCB-R8TS, N2OW-Y3NT-DQOZ-HUQJ, 1C3Z-5WXU-H5KJ-YDZC, L1W7-IT74-TF38-5SBV, MIUD-O9R9-SXY4-BERL, IO54-9AO9-QPYJ-IHSF, AXEP-WZXG-QLFI-F18X, D7NR-IWOI-SXL8-T4Y3, B50S-XROZ-Q0X9-RHBY, UNWZ-JCNN-LSLW-9EJP, CGID-9GGZ-HJDN-4IDJ, 57CK-CHUB-H5NX-YBGO";s:4:"hash";s:40:"1805b1248fba6faf3e819e6ea6aadf7d99fafd95";s:17:"verification_code";s:40:"354c82eabaaf95362becf5914228434cb33d8165";}';
//        $data = unserialize($data2);
//        $purchase = $em->getRepository('App:Purchase')->findOneBySubscriptionId($data['invoice_id']);

/**
 *             $product = $purchase->getProduct();
//            $licenses = $purchase->getLicenses();
 */
/**use Doctrine\ORM\PersistentCollection;
 * //                if ($licenses instanceof PersistentCollection) {
//                    $license = $licenses->first();
//                    $key = $license->getLicenseKey();
//                } else {
//                    $key = is_array($licenses) ? $licenses[0] : $licenses;
//                }
//                if ($ipnService->isValidLicense($helpers->getSetting('api_key'), $key)) {
    //}
 */