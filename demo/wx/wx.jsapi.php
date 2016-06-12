<?php
header("Content-type: text/html; charset=utf-8");
/**
 * 微信用户的openid获取请参考官方demo sdk和文档
 * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=11_1
 * 微信获取openid php代码, 运行环境是微信内置浏览器访问时
 */
include_once('lib/WxPayPubHelper/WxPayPubHelper.php');
$jsApi = new JsApi_pub();
//网页授权获取用户openid============
//通过code获得openid
if (!isset($_GET['code'])){
    //触发微信返回code码
    $url = $jsApi->createOauthUrlForCode(WxPayConf_pub::JS_API_CALL_URL);
    header("Location: $url");
} else {
    //获取code码，以获取openid
    $code = $_GET['code'];
    $jsApi->setCode($code);
    $openid = $jsApi->getOpenId();
}

$data["openid"] = $openid;
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud微信安全支付</title>
</head>
<?php
try {
    $result = $api->bill($data);
    if ($result->result_code != 0) {
        print_r($result);
        exit();
    }
    $jsApiParam = array(
        "appId" => $result->app_id,
        "timeStamp" => $result->timestamp,
        "nonceStr" => $result->nonce_str,
        "package" => $result->package,
        "signType" => $result->sign_type,
        "paySign" => $result->pay_sign
    );
} catch (Exception $e) {
    echo $e->getMessage();exit();
}
?>
<script type="text/javascript">
    //调用微信JS api 支付
    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo json_encode($jsApiParam);?>,
            function(res){
                /* 参考:https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6
                 * res.err_msg的返回值
                 * 1.支付成功, get_brand_wcpay_request：ok
                 * 2.支付过程中用户取消, get_brand_wcpay_request：cancel
                 * 3.支付失败, get_brand_wcpay_request：fail
                 */
                alert(res.err_msg); //供调试使用
                if(res.err_msg == "get_brand_wcpay_request：ok" ) {
                    //用户自己的操作, eg: window.location.href = '用户自己的URL';
                }else{
                    //用户自己的操作, eg: window.location.href = '用户自己的URL';
                }
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
<body onload="callpay();"> </body>
</html>