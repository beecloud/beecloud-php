<?php
/*
 * 批量审核接口仅支持预退款，批量审核分为批量驳回和批量同意。
 */
require_once("../loader.php");
require_once("config.php");

$data["timestamp"] = time() * 1000;
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
    //设置app id, app secret, master secret, test secret
    $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

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