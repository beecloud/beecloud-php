<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud微信更新退款状态示例</title>
</head>
<body>
<?php
require_once("../../loader.php");
$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "WX";
$data["refund_no"] = $_GET["refund_no"];
try {
    $result = $api->refundStatus($data);
    if ($result->result_code != 0 || $result->result_msg != "OK") {
        echo $result->err_detail;
        echo "<br/><a href='wx.refunds.php'>返回</a>";
        exit();
    }
    echo "更新成功，<a href='wx.refunds.php'>返回</a>";

} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>
</html>