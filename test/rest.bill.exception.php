<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 10/22/15
 * Time: 14:55
 */
include_once("../loader.php");

//"bill should throw Exception when channel error.\r\n";

$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
//选择渠道类型(WX、WX_APP、WX_NATIVE、WX_JSAPI、ALI、ALI_APP、ALI_WEB、ALI_QRCODE、UN、UN_APP、UN_WEB)
$data["channel"] = "UN_ERROR";
$data["total_fee"] = 1;
$data["bill_no"] = "bcdemo" . $data["timestamp"];
$data["title"] = "白开水";
$data["return_url"] = "http://payservice.beecloud.cn";

//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));

try {
    $result = $api->bill($data);

    echo "error:bill should throw Exception when channel error\r\n";
} catch (Exception $e) {
    echo "success:bill should throw Exception when channel error\r\n";
}



