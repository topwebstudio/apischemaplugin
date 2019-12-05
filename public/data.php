<?php

//$data = 'a:57:{s:27:"affiliate_commission_amount";s:0:"";s:28:"affiliate_commission_percent";s:0:"";s:15:"affiliate_email";s:0:"";s:20:"affiliate_first_name";s:0:"";s:19:"affiliate_last_name";s:0:"";s:6:"amount";s:4:"1.00";s:17:"billing_address_1";s:0:"";s:17:"billing_address_2";s:0:"";s:12:"billing_city";s:0:"";s:15:"billing_country";s:0:"";s:13:"billing_state";s:0:"";s:11:"billing_zip";s:0:"";s:11:"buyer_email";s:17:"jorjivt@gmail.com";s:16:"buyer_first_name";s:6:"Georgi";s:8:"buyer_ip";s:14:"109.121.249.30";s:15:"buyer_last_name";s:8:"Katsarov";s:14:"buyer_tax_name";s:0:"";s:16:"buyer_tax_number";s:0:"";s:11:"campaign_id";s:5:"10385";s:13:"campaign_name";s:13:"Schema Plugin";s:11:"coupon_code";s:0:"";s:11:"coupon_rate";s:0:"";s:11:"coupon_type";s:0:"";s:8:"currency";s:3:"USD";s:5:"event";s:5:"sales";s:10:"invoice_id";s:13:"PK-P3EV4GQ3MW";s:9:"is_rebill";s:1:"0";s:4:"mode";s:4:"test";s:17:"next_billing_date";s:10:"1575392852";s:17:"payment_processor";s:8:"testmode";s:10:"product_id";s:5:"30808";s:12:"product_name";s:13:"Schema Plugin";s:8:"quantity";s:1:"1";s:31:"ref_affiliate_commission_amount";s:0:"";s:32:"ref_affiliate_commission_percent";s:0:"";s:19:"ref_affiliate_email";s:0:"";s:24:"ref_affiliate_first_name";s:0:"";s:23:"ref_affiliate_last_name";s:0:"";s:18:"shipping_address_1";s:0:"";s:18:"shipping_address_2";s:0:"";s:13:"shipping_city";s:0:"";s:16:"shipping_country";s:0:"";s:14:"shipping_state";s:0:"";s:12:"shipping_zip";s:0:"";s:10:"tax_amount";s:0:"";s:11:"tax_percent";s:0:"";s:18:"tax_transaction_id";s:0:"";s:11:"tracking_id";s:0:"";s:14:"transaction_id";s:13:"PK-TQE7G7396L";s:16:"transaction_time";s:10:"1575306452";s:16:"transaction_type";s:7:"primary";s:12:"vendor_email";s:17:"jorjivt@gmail.com";s:17:"vendor_first_name";s:6:"Georgi";s:16:"vendor_last_name";s:8:"Katsarov";s:8:"licenses";s:0:"";s:4:"hash";s:40:"5497b1867d5f99bf648cf6bfe859cf46026a5c9f";s:17:"verification_code";s:40:"cfc6791a27c751eb6c4159656979939c35107021";}';
//print_r(unserialize($data));
 
//function is_valid_ipn($data, $secret_key) {
//    $ipnHash = $data['hash'];
//    unset($data['hash'], $data['verification_code']);
//    $data = array_filter(array_map('trim', $data));
//    ksort($data, SORT_STRING);
//    $hash = hash_hmac('sha1', implode("|", $data), $secret_key);
//    return $hash == $ipnHash;
//}
//
//var_dump(is_valid_ipn(unserialize($data), 'ZIFt8xqLsfUy'));



$cancel = 'a:55:{s:27:"affiliate_commission_amount";s:0:"";s:28:"affiliate_commission_percent";s:0:"";s:15:"affiliate_email";s:0:"";s:20:"affiliate_first_name";s:0:"";s:19:"affiliate_last_name";s:0:"";s:6:"amount";s:4:"1.00";s:17:"billing_address_1";s:0:"";s:17:"billing_address_2";s:0:"";s:12:"billing_city";s:0:"";s:15:"billing_country";s:0:"";s:13:"billing_state";s:0:"";s:11:"billing_zip";s:0:"";s:11:"buyer_email";s:17:"jorjivt@gmail.com";s:16:"buyer_first_name";s:6:"Georgi";s:8:"buyer_ip";s:14:"109.121.249.30";s:15:"buyer_last_name";s:8:"Katsarov";s:14:"buyer_tax_name";s:0:"";s:16:"buyer_tax_number";s:0:"";s:11:"campaign_id";s:5:"10385";s:13:"campaign_name";s:13:"Schema Plugin";s:11:"coupon_code";s:0:"";s:11:"coupon_rate";s:0:"";s:11:"coupon_type";s:0:"";s:8:"currency";s:3:"USD";s:5:"event";s:20:"subscription-updated";s:10:"invoice_id";s:13:"PK-PRW2KMKPKE";s:9:"is_rebill";s:1:"0";s:4:"mode";s:4:"test";s:17:"payment_processor";s:8:"testmode";s:10:"product_id";s:5:"30808";s:12:"product_name";s:13:"Schema Plugin";s:8:"quantity";s:1:"1";s:31:"ref_affiliate_commission_amount";s:0:"";s:32:"ref_affiliate_commission_percent";s:0:"";s:19:"ref_affiliate_email";s:0:"";s:24:"ref_affiliate_first_name";s:0:"";s:23:"ref_affiliate_last_name";s:0:"";s:18:"shipping_address_1";s:0:"";s:18:"shipping_address_2";s:0:"";s:13:"shipping_city";s:0:"";s:16:"shipping_country";s:0:"";s:14:"shipping_state";s:0:"";s:12:"shipping_zip";s:0:"";s:10:"tax_amount";s:0:"";s:11:"tax_percent";s:0:"";s:18:"tax_transaction_id";s:0:"";s:11:"tracking_id";s:0:"";s:14:"transaction_id";s:13:"PK-TJWX5M8QME";s:16:"transaction_time";s:10:"1575380863";s:12:"vendor_email";s:17:"jorjivt@gmail.com";s:17:"vendor_first_name";s:6:"Georgi";s:16:"vendor_last_name";s:8:"Katsarov";s:8:"licenses";s:0:"";s:4:"hash";s:40:"32d5014c4be76dacdafa0c3699abda1eab5b3970";s:17:"verification_code";s:40:"29d9690d1cad467625d890ff058da32a6b119920";}';

//$data = unserialize($cancel);
//
//
//print_r($data); 


exit();