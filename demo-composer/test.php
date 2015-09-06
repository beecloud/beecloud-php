<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 9/6/15
 * Time: 21:03
 */
include_once "../composer/network.php";
include_once "../composer/api.php";
use beecloud;

$now = date('Y-m-d',time());
$refund_no = str_replace("-","",$now).time() * 1000;
$data = array();
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
//选择渠道类型(WX、WX_APP、WX_NATIVE、WX_JSAPI、ALI、ALI_APP、ALI_WEB、ALI_QRCODE、UN、UN_APP、UN_WEB)
$data["channel"] = "ALI";
$data["limit"] = 10;


try {
    $result = beecloud\api::bills($data);
    echo json_encode($result);
} catch (Exception $e) {
    echo $e->getMessage();
}
