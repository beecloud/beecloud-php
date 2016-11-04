<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝线下扫码退款示例</title>
</head>
<body>
<table border="1" align="center" cellspacing=0>
<?php
    require_once("../../loader.php");
    require_once("../config.php");

    try {
        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

        $data["timestamp"] = time() * 1000;
        $data["bill_no"] = $_GET["bill_no"];
        $data["refund_no"] = $_GET["refund_no"];
        $data["refund_fee"] = (int)$_GET["refund_fee"];
        //选择渠道类型 (WX、ALI)
        $data["channel"] = "ALI";
        //选填 optional
        $data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));

        $result = $api->refund($data);
        if ($result->result_code != 0) {
            print_r($result);
            exit();
        }
        header("Location:{$result->url}");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</table>
</body>
</html>