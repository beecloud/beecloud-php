<?php
require_once("../loader.php");
require_once("config.php");

$data["timestamp"] = time() * 1000;
$data["limit"] = 10;

$type = $_GET['type'];
switch($type){
    case 'ALI' :
        $title = "支付宝";
        $data["channel"] = "ALI";
        break;
    case 'BD' :
        $title = "百度";
        $data["channel"] = "BD";
        break;
    case 'JD' :
        $title = "京东";
        $data["channel"] = "JD";
        break;
    case 'WX' :
        $title = "微信";
        $data["channel"] = "WX";
        break;
    case 'UN' :
        $title = "银联";
        $data["channel"] = "UN";
        break;
    case 'YEE' :
        $data["channel"] = "YEE";
        $title = "易宝";
        break;
    case 'KUAIQIAN' :
        $data["channel"] = "KUAIQIAN";
        $title = "快钱";
        break;
    case 'BC' :
        $data["channel"] = "BC";
        $title = "BC支付";
        break;
    case "PAYPAL" :
        $data["channel"] = "PAYPAL";
        $title = "PAYPAL";
        break;
    default :
        exit("No this type.");
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud<?php echo $title;?>退款查询示例</title>
    <link type="text/css" rel="stylesheet" href="statics/demo.css" />
</head>
<body>
<h3>注意:退款状态更新接口提供查询退款状态以更新退款状态的功能，用于对退款后不发送回调的渠道（WX、YEE、KUAIQIAN、BD）退款后的状态更新。</h3>
<table border="1" align="center" cellspacing=0>
<?php
    try {
        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

        $result = $api->refunds($data);
        if ($result->result_code != 0 || $result->result_msg != "OK") {
            print_r($result);
            exit();
        }
        $refunds = $result->refunds;
        $str = "<tr><th>ID</th><th>退款是否成功</th><th>退款创建时间</th><th>退款号</th><th>订单金额(分)</th><th>退款金额(分)</th><th>渠道类型</th><th>订单号</th><th>退款是否完成</th><th>订单标题</th><th>查看状态</th></tr>";
        foreach($refunds as $list) {
            $result = $list->result ? "成功" : "失败";
            $create_time = $list->create_time ? date('Y-m-d H:i:s',$list->create_time/1000) : '';
            $finish = $list->finish ? "完成" : "未完成";

            $search_status = '';
            if(in_array($data["channel"], array('WX','YEE','KUAIQIAN','BD'))){
                $search_status = "<a href='./refund.changestatus.php?channel={$list->channel}&refund_no={$list->refund_no}' target='_blank'>查询</a>";
            }
            $str .= "<tr><td>{$list->id}</td><td>$result</td><td>$create_time</td><td>{$list->refund_no}</td><td>{$list->total_fee}</td>
                    <td>{$list->refund_fee}</td><td>{$list->sub_channel}</td><td>{$list->bill_no}</td><td>$finish</td><td>{$list->title}</td><td>$search_status</td></tr>";
        }
        echo $str;

        unset($data["limit"]);
        $result = $api->refunds_count($data);
        if ($result->result_code != 0 || $result->result_msg != "OK") {
            print_r($result);
            exit();
        }
        $count = $result->count;
        echo '<tr><td colspan="1">退款订单总数:</td><td colspan="10">'.$count.'</td></tr>';
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</table>
</body>
</html>