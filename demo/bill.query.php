<?php
/**
 *  根据具体的条件进行查询，可查询的字段以文档为准
 *  参考文档：https://beecloud.cn/doc/#4-4
 *
 */
require_once("../loader.php");
require_once("config.php");

$data["timestamp"] = time() * 1000;
//只列出了支付成功的订单
$data["spay_result"] = true;
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
    case "PAYPAL" :
        $data["channel"] = "PAYPAL";
        $title = "PAYPAL";
        break;
    case "BC" :
        $data["channel"] = "BC";
        $title = "BC网关/快捷支付";
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
    <title>BeeCloud<?php echo $title;?>订单查询示例</title>
    <link type="text/css" rel="stylesheet" href="statics/demo.css" />
</head>
<body>
<table border="1" align="center" cellspacing=0>
<?php
    try {
        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
        //Test Model,只提供下单和支付订单查询的Sandbox模式,不写setSandbox函数或者false即live模式,true即test模式
        $api->setSandbox(false);

        $result = $api->bills($data);
        if ($result->result_code != 0 || $result->result_msg != "OK") {
            print_r($result);
            exit();
        }
        $bills = $result->bills;
        $str = "<tr><th>ID</th><th>同意退款</th><th>预退款</th><th>是否支付</th><th>创建时间</th><th>总价(分)</th><th>渠道类型</th><th>订单号</th><th>订单标题</th></tr>";
        foreach($bills as $list) {
            $refund = true;
            $pre_refund = true;
            $type = trim($list->channel);
            if(in_array($list->sub_channel, array('ALI_OFFLINE_QRCODE', 'ALI_SCAN', 'WX_SCAN', 'WX_NATIVE'))){
                $type = trim($list->sub_channel);
                $pre_refund = false;
            }
            if($type == 'BC' || $type == 'PAYPAL' || $api->getSandbox()){
                $refund = false;
                $pre_refund = false;
                if(in_array($list->sub_channel, array('BC_ALI_QRCODE', 'BC_ALI_APP', 'BC_ALI_WAP', 'BC_ALI_SCAN', 'BC_NATIVE', 'BC_WX_JSAPI', 'BC_WX_SCAN'))) {
                    $type = trim($list->sub_channel);
                    $refund = true;
                }
            }
            $strParams = "bill.refund.php?type=$type&bill_no={$list->bill_no}&refund_fee={$list->total_fee}";
            $agree_refund = $list->spay_result&&!$list->refund_result&&$refund ? "<a href='".$strParams."' target='_blank'>退款</a>" : "";
            $prep_refund = $list->spay_result&&!$list->refund_result&&$pre_refund ? "<a href='".$strParams."&need_approval=true' target='_blank'>预退款</a>" : "";
            $spay_result = $list->spay_result ? ($list->refund_result ? '已退款' : '支付') : '未支付';
            $create_time = $list->create_time ? date ( 'Y-m-d H:i:s', $list->create_time / 1000 ) : '';
            $str .= "<tr><td>$list->id</td><td>$agree_refund</td><td>$prep_refund</td><td>$spay_result</td><td>$create_time</td><td>{$list->total_fee}</td><td>{$list->sub_channel}</td>
            	<td>{$list->bill_no}</td><td>{$list->title}</td></tr>";
        }
        echo $str;

        unset($data["limit"]);
        $result = $api->bills_count($data);
        if ($result->result_code != 0 || $result->result_msg != "OK") {
            print_r($result);
            exit();
        }
        $count = $result->count;
        echo '<tr><td colspan="1">支付订单总数:</td><td colspan="8">'.$count.'</td></tr>';
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>
</table>
</body>
</html>