<?php
require_once("../loader.php");
require_once("config.php");

$data["timestamp"] = time() * 1000;
$data["bill_no"] = $_GET["bill_no"];

//refund_no退款单号,为(预)退款使用的, 格式为:退款日期(8位) + 流水号(3~24 位)。
//请自行确保在商户系统中唯一，且退款日期必须是发起退款的当天日期, 同一退款单号不可重复提交，否则会造成退款单重复。
//流水号可以接受数字或英文字符，建议使用数字，但不可接受“000”
$data["refund_no"] = date('Ymd',time()).time() * 1000;

$data["refund_fee"] = (int)$_GET["refund_fee"];
//选填,是否为预退款,预退款need_approval->true,直接退款->不加此参数或者false
if(isset($_GET["need_approval"])){
    $data["need_approval"] = true;
}
//选填 optional
$data["optional"] = (object)array("key"=>"refund");

//refund_account(类型Integer),适用于WX_NATIVE, WX_JSAPI, WX_SCAN, WX_APP
//退款资金来源 1:可用余额退款 0:未结算资金退款（默认使用未结算资金退款）
//$data["refund_account"] = 1;

/**
 * notify_url 选填，该参数是为退款成功之后接收返回信息配置的url,等同于在beecloud平台配置webhook，
 * 如果两者都设置了，则优先使用notify_url。配置时请结合自己的项目谨慎配置，具体请
 * 参考demo/webhook.php
 */
//$data['notify_url'] = 'http://beecloud.cn';

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
    case 'PAYPAL' :
        $data["channel"] = "PAYPAL";
        $title = "PAYPAL";
        break;
    case 'ALI_OFFLINE_QRCODE':
    case 'ALI_SCAN':
        $title = $type."线下退款";
        $data["channel"] = 'ALI';
        break;
    case 'WX_SCAN':
    case 'WX_NATIVE' : //非服务商WX_NATIVE 可通过/rest/refund/ 或 /rest/offline/refund/进行退款
        $title = $type."线下退款";
        $data["channel"] = 'WX';
        break;
    case 'BC_WX_SCAN':
    case 'BC_ALI_SCAN':
    case 'BC_ALI_QRCODE':
    case 'BC_NATIVE':
        $title = $type."线下退款";
        $data["channel"] = 'BC';
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
    <title>BeeCloud<?php echo $title;?>退款示例</title>
</head>
<body>
<?php
    try {
        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

        if(in_array($type, array('ALI_OFFLINE_QRCODE', 'ALI_SCAN', 'WX_SCAN', 'WX_NATIVE', 'BC_NATIVE', 'BC_WX_SCAN', 'BC_ALI_SCAN', 'BC_ALI_QRCODE'))){
            $result = $api->offline_refund($data);
        }else{
            $result = $api->refund($data);
        }
        if ($result->result_code != 0 || $result->result_msg != "OK") {
            print_r($result);
            exit();
        }
        //当channel为ALI_APP、ALI_WEB、ALI_QRCODE，并且不是预退款
        if((!isset($data["need_approval"]) || $data["need_approval"] === false) && $type == 'ALI'){
            header("Location:$result->url");
            exit();
        }
        echo (isset($_GET["need_approval"]) && $_GET["need_approval"] ? '预' : '')."退款成功, 退款表记录唯一标识ID: ".$result->id;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</body>
</html>
