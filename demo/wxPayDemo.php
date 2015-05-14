<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 4/1/15
 * Time: 16:45
 */
include_once("../BCWXPay.php");

$pay = new BCWXPay($_GET);


$billID = $pay->configProduct(array(
    "body" => "web wxpay",
    "total_fee" => "1", //总金额单位为分以下非必填参数，商户可根据实际情况选填
//    "out_trade_no" =>"" //订单号，默认自动生成billID,如果手动设置则使用手动设置的值
//  "sub_mch_id" => "123",//子商户号
//    "device_info" => "android",//设备号
//    "attach" =>"wechao",//附加数据
//    "time_start" => "0",//交易起始时间
//    "time_expire" => "0",//交易结束时间
//    "goods_tag" => "hehe",//商品标记
//    "product_id" => "111"//商品ID
));

$result = $pay->getJsParams(false);
if ($result->result) {
    $params = $result->params;
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

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title>微信安全支付</title>

    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall() {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $params;?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);
                }
            );
        }

        function callpay() {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>
</head>
<body>
</br></br></br></br>
<div align="center">
    <button style="width:210px; height:30px; background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >贡献一下</button>
</div>
</body>
</html>