<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud京东退款示例</title>
</head>
<body>
<?php
require_once("../../sdk/beecloud.php");
$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["bill_no"] = $_GET["bill_no"];
$data["refund_no"] = $_GET["refund_no"];
$data["refund_fee"] = (int)$_GET["refund_fee"];
//选择渠道类型(WX、WX_APP、WX_NATIVE、WX_JSAPI、ALI、ALI_APP、ALI_WEB、ALI_QRCODE、UN、UN_APP、UN_WEB)
$data["channel"] = "KUAIQIAN";
//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));


try {
    $result = BCRESTApi::refund($data);
    if ($result->result_code != 0 || $result->result_msg != "OK") {
        echo json_encode($result->err_detail);
        exit();
    }
    echo $result->html;
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>
</html>