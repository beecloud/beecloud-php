<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝二维码示例</title>
</head>
<body>
<?php
require_once("../../loader.php");
$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "ALI_QRCODE";
$data["total_fee"] = 1;
$data["bill_no"] = "bcdemo" . $data["timestamp"];
$data["title"] = "白开水";
$data["return_url"] = "http://payservice.beecloud.cn";
//qr_pay_mode必填 二维码类型含义
//0： 订单码-简约前置模式, 对应 iframe 宽度不能小于 600px, 高度不能小于 300px
//1： 订单码-前置模式, 对应 iframe 宽度不能小于 300px, 高度不能小于 600px
//3： 订单码-迷你前置模式, 对应 iframe 宽度不能小于 75px, 高度不能小于 75px 
$data["qr_pay_mode"] = "0";
//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));

try {
    $result = $api->bill($data);
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }

    $htmlContent = $result->html;
    $url = $result->url;
    echo $htmlContent;
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>
</html>
