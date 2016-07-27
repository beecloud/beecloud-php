<?php
require_once("../loader.php");
require_once("config.php");

$data["timestamp"] = time() * 1000;
$data["need_approval"] = true;
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
    <title>BeeCloud<?php echo $title;?>预退款查询-批量审核示例</title>
    <link type="text/css" rel="stylesheet" href="statics/demo.css" />
</head>
<body>

<h3>注意：<br/>
    1.批量审核必须针对同一个渠道发起<br/>
    2.当channel为ALI时，如果退款成功，返回参数中包含支付宝退款地址，需用户在支付宝平台上手动输入支付密码处理！
</h3>
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
        $str = "<tr><th>选择<input name='checkAll' type='checkbox'/></th><th>ID</th><th>退款是否成功</th><th>退款创建时间</th><th>退款号</th><th>订单金额(分)</th><th>退款金额(分)</th><th>渠道</th><th>子渠道</th><th>订单号</th><th>退款是否完成</th><th>订单标题</th></tr>";
        foreach($refunds as $list) {
            $result = $list->result ? "成功" : "失败";
            $create_time = $list->create_time ? date('Y-m-d H:i:s',$list->create_time/1000) : '';
            $finish = $list->finish ? "完成" : "未完成";
            $checkout = $list->result ? '' : "<input name='approve' type='checkbox' value='{$list->id}'/>";

            $str .= "<tr><td align='center'>$checkout</td><td>{$list->id}</td><td>$result</td><td>$create_time</td><td>{$list->refund_no}</td><td>{$list->total_fee}</td>
                    <td>{$list->refund_fee}</td><td>{$list->channel}</td><td>{$list->sub_channel}</td><td>{$list->bill_no}</td><td>$finish</td><td>{$list->title}</td></tr>";
        }
        echo $str;

        unset($data["limit"]);
        $result = $api->refunds_count($data);
        if ($result->result_code != 0 || $result->result_msg != "OK") {
            print_r($result);
            exit();
        }
        $count = $result->count;
        echo '<tr><td colspan="2">退款订单总数:</td><td colspan="10">'.$count.'</td></tr>';
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</table>

<input onclick="approve_pre_refunds()" type="button" class="button_blue" value="同意预退款" />
<script type="text/javascript" src="statics/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
    $("input[name=checkAll]").click(function(){
        if(this.checked){
            $('input[name=approve]').prop('checked',true);
        }else{
            $('input[name=approve]').prop('checked',false);
        }
    });

    function approve_pre_refunds() {
        var tmp="";
        $("input[name=approve]").each(function(){
            if(this.checked){
                tmp += $(this).val();
                tmp += "@";
            }
        });

        if (tmp.length > 1){
            tmp = tmp.substring(0, tmp.length - 1);
            window.open("./prep.refund.php?ids=" + tmp + '&channel=<?php echo $_GET['type'] ?>');
        }else{
            alert('请选择记录!');
        }
    }
</script>
</body>
</html>