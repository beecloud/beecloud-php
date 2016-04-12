<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud百度订单查询示例</title>
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
$data["channel"] = "BD";
$data["limit"] = 10;
//退款单号
$refund_no = date('Ymd',time()).time() * 1000;

try {
    $result = $api->bills($data);
    if ($result->result_code != 0 || $result->result_msg != "OK") {
        echo json_encode($result->err_detail);
        exit();
    }
    $bills = $result->bills;
    $str = "<tr><td>同意退款</td><td>是否支付</td><td>创建时间</td><td>总价(分)</td><td>渠道类型</td><td>订单号</td><td>订单标题</td></tr>";
    foreach($bills as $list) {
        $agree_refund = $list->spay_result ? "<a href='bd.agree.refund.php?refund_no=".$refund_no."&bill_no=".$list->bill_no."&refund_fee=".$list->total_fee."'>同意退款</a>" : "";
        $spay_result = $list->spay_result ? '支付' : '未支付';
        $create_time = $list->create_time ? date ( 'Y-m-d H:i:s', $list->create_time / 1000 ) : '';
        $str .= "<tr><td>$agree_refund</td><td>$spay_result</td><td>$create_time</td><td>{$list->total_fee}</td><td>{$list->sub_channel}</td>
            	<td>{$list->bill_no}</td><td>{$list->title}</td></tr>";
    }
    echo $str;
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</table>
</body>
</html>