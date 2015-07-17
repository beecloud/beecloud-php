<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝订单查询示例</title>
</head>
<body>
<table border="1" align="center" cellspacing=0>
<?php
require_once("../beecloud.php");
date_default_timezone_set("Asia/Shanghai");

$data = array();
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
//选择渠道类型(WX、WX_APP、WX_NATIVE、WX_JSAPI、ALI、ALI_APP、ALI_WEB、ALI_QRCODE、UN、UN_APP、UN_WEB)
$data["channel"] = "ALI";
$data["limit"] = 10;


try {
    $result = BCRESTApi::bills($data);
    if ($result->result_code != 0 || $result->result_msg != "OK") {
        echo json_encode($result->err_detail);
        exit();
    }
    $bills = $result->bills;
    foreach($bills as $list) {
        echo "<tr>";
        foreach($list as $k=>$v) {
            switch ($k) {
                case "title":
                    echo "<td>订单标题</td>";
                    break;
                case "spay_result":
                    echo "<td>是否支付</td>";
                    break;
                case "total_fee":
                    echo "<td>总价(分)</td>";
                    break;
                case "channel":
                    echo "<td>渠道类型</td>";
                    break;
                case "bill_no":
                    echo "<td>订单号</td>";
                    break;
            }
            echo "<td>".($k=="spay_result"?($v?"支付":"未支付"):($k=="created_time"?date('Y-m-d H:i:s',$v/1000):$v))."</td>";
        }
        echo "</tr>";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</table>
</body>
</html>