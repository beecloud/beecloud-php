<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud微信退款示例</title>
</head>
<body>
<table border="1" align="center" cellspacing=0>
<?php
require_once("../../loader.php");
$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["bill_no"] = $_GET["bill_no"];
$data["refund_no"] = $_GET["refund_no"];
$data["refund_fee"] = (int)$_GET["refund_fee"];
$data["channel"] = "WX";
//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));


try {
    $result = $api->refund($data);
    if ($result->result_code != 0 || $result->result_msg != "OK") {
        echo $result->err_detail;
        exit();
    }
    echo "退款成功";

} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</table>
</body>
</html>