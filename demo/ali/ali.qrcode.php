<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝二维码示例</title>
</head>
<body>
<?php
require_once("../../sdk/beecloud.php");

$data = array();
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "ALI_QRCODE";
$data["total_fee"] = 1;
$data["bill_no"] = "bcdemo" . $data["timestamp"];
$data["title"] = "白开水";
$data["return_url"] = "http://payservice.beecloud.cn";

//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));
//选填 qr_pay_mode 
//$data["qr_pay_mode"] = 0;

try {
    $result = BCRESTApi::bill($data);
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
