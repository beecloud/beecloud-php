<?php
require_once("../loader.php");

$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["total_fee"] = 1;
$data["bill_no"] = "bcdemo" . $data["timestamp"];
$data["title"] = "白开水";
$data["return_url"] = "http://payservice.beecloud.cn";
//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));

$type = $_GET['type'];
switch($type){
    case 'ALI_WEB' :
        $title = "支付宝及时到账";
        $data["channel"] = "ALI_WEB";
        break;
    case 'ALI_WAP' :
        $title = "支付宝移动网页";
        $data["channel"] = "ALI_WAP";
        break;
    case 'ALI_QRCODE' :
        $title = "支付宝扫码支付";
        $data["channel"] = "ALI_QRCODE";
        //qr_pay_mode必填 二维码类型含义
        //0： 订单码-简约前置模式, 对应 iframe 宽度不能小于 600px, 高度不能小于 300px
        //1： 订单码-前置模式, 对应 iframe 宽度不能小于 300px, 高度不能小于 600px
        //3： 订单码-迷你前置模式, 对应 iframe 宽度不能小于 75px, 高度不能小于 75px
        $data["qr_pay_mode"] = "0";
        break;
    case 'BD_WEB' :
        $data["channel"] = "BD_WEB";
        $title = "百度网页支付";
        break;
    case 'BD_WAP' :
        $data["channel"] = "BD_WAP";
        $title = "百度移动网页";
        break;
    case 'JD_WEB' :
        $data["channel"] = "JD_WEB";
        $title = "京东网页";
        break;
    case 'JD_WAP' :
        $data["channel"] = "JD_WAP";
        $title = "京东移动网页";
        break;
    case 'UN_WEB' :
        $data["channel"] = "UN_WEB";
        $title = "银联网页";
        break;
    case 'WX_NATIVE':
        $data["channel"] = "WX_NATIVE";
        $title = "微信扫码";
        require_once 'wx/wx.native.php';
        exit();
        break;
    case 'WX_JSAPI':
        $data["channel"] = "WX_JSAPI";
        $title = "微信H5网页";
        require_once 'wx/wx.jsapi.php';
        exit();
        break;
    case 'YEE_WEB' :
        $data["channel"] = "YEE_WEB";
        $title = "易宝网页";
        break;
    case 'YEE_WAP' :
        $data["channel"] = "YEE_WAP";
        $data["identity_id"] = "lengthlessthan50useruniqueid";
        $title = "易宝移动网页";
        break;
    case 'KUAIQIAN_WEB' :
        $data["channel"] = "KUAIQIAN_WEB";
        $title = "快钱移动网页";
        break;
    case 'KUAIQIAN_WAP' :
        $data["channel"] = "KUAIQIAN_WEB";
        $title = "快钱移动网页";
        break;
    case 'PAYPAL_PAYPAL' :
        $data["channel"] = "PAYPAL_PAYPAL";
        $data["currency"] = "USD";
        $title = "Paypal网页";
        break;
    case 'PAYPAL_CREDITCARD' :
        $data["channel"] = "PAYPAL_CREDITCARD";
        $data["currency"] = "USD";

        $card_info = array(
            'card_number' => '',
            'expire_month' => 1,  //int month
            'expire_year' => 2016, //int year
            'cvv' => 0,           //string
            'first_name' => '', //string
            'last_name' => '',  //string
            'card_type' => 'visa' //string
        );
        $data["credit_card_info"] = (object)$card_info;
        $title = "Paypal信用卡";
        break;
    case 'PAYPAL_SAVED_CREDITCARD' :
        $data["channel"] = "PAYPAL_SAVED_CREDITCARD";
        $data["currency"] = "USD";
        $data["credit_card_id"] = '';
        $title = "Paypal快捷";
        break;
    case 'ALI_OFFLINE_QRCODE' :
        $data["channel"] = "ALI_OFFLINE_QRCODE";
        require_once 'ali.offline.qrcode/index.php';
        exit();
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud<?php echo $title;?>支付示例</title>
</head>
<body>
<?php
try {
    if(in_array($type, array('PAYPAL_PAYPAL', 'PAYPAL_CREDITCARD', 'PAYPAL_SAVED_CREDITCARD'))){
        $result =  $international->bill($data);
    }else{
        $result =  $api->bill($data);
    }
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }
    if(isset($result->html)) {
        echo $result->html;
    }else{
        header("Location:$result->url");
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

</body>
</html>