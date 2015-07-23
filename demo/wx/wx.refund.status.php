<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud微信更新退款状态示例</title>
</head>
<body>
<?php
require_once("../../sdk/beecloud.php");
$data = array();
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "WX";
$data["refund_no"] = $_GET["refund_no"];
try {
    $result = BCRESTApi::refundStatus($data);
    if ($result->result_code != 0 || $result->result_msg != "OK") {
        echo json_encode($result->err_detail);
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