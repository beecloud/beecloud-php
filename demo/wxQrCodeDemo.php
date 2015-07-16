<?php
/**
 * Native（原生）支付-模式二-demo
 * ====================================================
 * 商户生成订单，先调用统一支付接口获取到code_url，
 * 此URL直接生成二维码，用户扫码后调起支付。
 *
 */
class test {
    public $test = 1;
}
include_once("../BCWXPay.php");
$pay = new BCWXQrCode();
$out_trade_no = WxPayConf_pub::APPID.time();
$optional = new stdClass();
$optional->hello = 1;
$pay->configProduct(array(
    "body" => "web wxpay",
    "total_fee" => "1", //总金额单位为分以下非必填参数，商户可根据实际情况选填
    "out_trade_no" => "$out_trade_no",//商户订单号
    "optional" => $optional
//  "sub_mch_id" => "123",//子商户号
//    "device_info" => "android",//设备号
//    "attach" =>"wechao",//附加数据
//    "time_start" => "0",//交易起始时间
//    "time_expire" => "0",//交易结束时间
//    "goods_tag" => "hehe",//商品标记
//    "product_id" => "111"//商品ID
));


$result = $pay->getOrderResult(false);
if ($result->result) {
    //商户根据实际情况设置相应的处理流程
    if ($result->params["return_code"] == "FAIL") {
        //商户自行增加处理流程
        echo "通信出错：".$result->params['return_msg']."<br>";
        exit();
    } elseif($result->params["result_code"] == "FAIL") {
        //商户自行增加处理流程
        echo "错误代码：".$result->params['err_code']."<br>";
        echo "错误代码描述：".$result->params['err_code_des']."<br>";
        exit();
    } elseif($result->params["code_url"] != NULL) {
        //从统一支付接口获取到code_url
        $params = json_decode($result->params);
        $code_url = $params->code_url;
        //商户自行增加处理流程
        //......
    }

} else {
    if ($result->errMsg == "WXMP_NOT_SET")  {
        //支付设置中，微信公众号设置中得参数没有设置
        echo "BeeCloud 微信公众号参数未设置";
        exit();
    } else {
        //请提供$result->errMsg 给BeeCloud
        echo "Debug 请联系BeeCloud:".$result->errMsg;
        exit();
    }
}
?>


<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>微信安全支付</title>
</head>
<body>

</html>
