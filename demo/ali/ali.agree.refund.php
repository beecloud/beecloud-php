<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝退款示例</title>
</head>
<body>
<table border="1" align="center" cellspacing=0>
<?php
require_once("../../sdk/beecloud.php");
$data = array();
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["bill_no"] = $_GET["bill_no"];
$data["refund_no"] = $_GET["refund_no"];
$data["refund_fee"] = (int)$_GET["refund_fee"];
//选择渠道类型(WX、WX_APP、WX_NATIVE、WX_JSAPI、ALI、ALI_APP、ALI_WEB、ALI_QRCODE、UN、UN_APP、UN_WEB)
$data["channel"] = "ALI";
//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));


try {
    $result = BCRESTApi::refund($data);
    if ($result->result_code != 0 || $result->result_msg != "OK") {
        echo json_encode($result->err_detail);
        exit();
    }
    $url = $result->url;
    echo "<script language='javascript' type='text/javascript'>";
    echo 'window.location.href="'.$url.'"';
    echo "</script>";

} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</table>
</body>
</html>