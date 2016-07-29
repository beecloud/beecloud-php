<?php
require_once("../loader.php");
require_once("config.php");

$data["timestamp"] = time() * 1000;
$data["bill_no"] = $_GET["bill_no"];
$data["refund_no"] = $_GET["refund_no"];
$data["refund_fee"] = (int)$_GET["refund_fee"];
//选填,是否为预退款,预退款need_approval->true,直接退款->不加此参数或者false
if(isset($_GET["need_approval"])){
    $data["need_approval"] = true;
}
//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));

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
        exit('开发中...');
        break;
    case 'ALI_OFFLINE_QRCODE':
    case 'ALI_SCAN':
    case 'WX_SCAN':
    case 'WX_NATIVE' : //非服务商WX_NATIVE 可通过/rest/refund/ 或 /rest/offline/refund/进行退款
        $title = $type." 线下退款";
        $data["channel"] = substr($type, 0, strpos($type, '_'));
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

        if(in_array($type, array('ALI_OFFLINE_QRCODE', 'ALI_SCAN', 'WX_SCAN', 'WX_NATIVE'))){
            $result = $api->offline_refund($data);
        }else{
            $result = $api->refund($data);
        }
        if ($result->result_code != 0 || $result->result_msg != "OK") {
            print_r($result);
            exit();
        }
        //当channel为ALI_APP、ALI_WEB、ALI_QRCODE，并且不是预退款
        if(!isset($data["need_approval"]) && $type == 'ALI'){
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
