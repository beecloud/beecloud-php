<?php
require_once("../loader.php");

$data = array();
$appSecret = APP_SECRET;
$data["app_id"] = APP_ID;
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
//agree, boolean: 批量驳回传false，批量同意传true
$data["agree"] = true;
//deny_reason选填, 驳回理由
//$data["deny_reason"] = '';

//退款记录id列表
if(!isset($_GET['ids']) || !$_GET['ids']) exit('请选择要预退款的记录!');
$data["ids"] = explode('@', $_GET['ids']);

$type = $_GET['channel'];
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
        $result = $api->refund($data, 'put');
        if ($result->result_code != 0 || $result->result_msg != "OK") {
            print_r($result);
            exit();
        }
        //agree为true时,支付宝退款地址，需用户在支付宝平台上手动输入支付密码处理
        if($data["channel"] == 'ALI'){
            header("Location:$result->url");
            exit();
        }
        //agree为true时,批量同意单笔结果集合，key:单笔记录id; value:此笔记录结果。
        //当退款处理成功时，value值为"OK"；当退款处理失败时， value值为具体的错误信息。
        print_r($result->result_map);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</body>
</html>
