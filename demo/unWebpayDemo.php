<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 4/11/15
 * Time: 16:30
 */
header( 'Content-type:text/html;charset=utf-8');

include_once("../BCUnPay.php");

$pay = new BCUnPay();
$pay->configProduct( array(
    'orderId' => date('YmdHis'),	//*商户订单号
    'traceId' => date('YmdHis'),    //
    'frontUrl' =>  'http://localhost:8085/upacp_sdk_php/demo/gbk/FrontReceive.php',  		//*前台通知地址
    'txnAmt' => '10',		//*交易金额，单位分
    'orderDesc' => "描述" // 商品描述
));

$html = $pay->getWebpay();

if ($html != false) {
    echo $html;
} else {
    echo "null";
}
