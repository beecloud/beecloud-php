<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 9/29/15
 * Time: 16:05
 */

require_once("../../loader.php");
date_default_timezone_set("Asia/Shanghai");

$data = array();
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
//选择渠道类型(WX、WX_APP、WX_NATIVE、WX_JSAPI、ALI、ALI_APP、ALI_WEB、ALI_QRCODE、UN、UN_APP、UN_WEB)
$data["channel"] = "WX";
$data["limit"] = 10;
$data["bill_no"] = $_POST["billNo"];

$result = \beecloud\rest\api::bills($data);

print json_encode($result);






