<?php
    /*
     *  针对channel=BC_WX_JSAPI,需要注意以下几点:
     *  1.将文件bc.wxjsapi.php复制到服务器的huami文件夹下(由于微信授权目录限制)
     *  2.注意修改sdk中文件loader.php和demo/config.php的引用路径(根据自己php sdk引用路径进行修改)
     *
     *  备注: 当前的loader.php和demo/config.php引用路径是beecloud-php目录和huami目录同级
     */
    header("Content-type: text/html; charset=utf-8");
    require_once("../beecloud-php/loader.php");
    require_once("../beecloud-php/demo/config.php");

    //get openid for channel bc_wx_jsapi
    if(!isset($_GET["openid"])){
        header("Location:http://wxactivity.beecloud.cn/activity/getopenid.php?callbackurl=".urlencode("http://huami.beecloud.cn/huami/bc.wxjsapi.php"));
    }
    $data['openid'] = $_GET['openid'];


    //common params
    $data["channel"] = "BC_WX_JSAPI";
    $data["timestamp"] = time() * 1000;
    //total_fee(int 类型) 单位分
    $data["total_fee"] = 1;
    $data["bill_no"] = "bcdemo" . $data["timestamp"];
    //title UTF8编码格式，32个字节内，最长支持16个汉字
    $data["title"] = 'PHP BC_WX_JSAPI 支付测试';
    //渠道类型:ALI_WEB 或 ALI_QRCODE 或 UN_WEB或JD_WAP或JD_WEB时为必填
    $data["return_url"] = "https://beecloud.cn";
    //选填 optional
    $data["optional"] = (object)array("tag"=>"msgtoreturn");
    //选填 订单失效时间bill_timeout
    //必须为非零正整数，单位为秒，建议最短失效时间间隔必须大于360秒
    //京东(JD*)不支持该参数。
    //$data["bill_timeout"] = 360;

    /**
     * notify_url 选填，该参数是为接收支付之后返回信息的,仅适用于线上支付方式, 等同于在beecloud平台配置webhook，
     * 如果两者都设置了，则优先使用notify_url。配置时请结合自己的项目谨慎配置，具体请
     * 参考demo/webhook.php
     */
    //$data['notify_url'] = 'http://xxx/webhook.php';

    try {
        /* registerApp fun need four params:
         *  @param(first) $app_id beecloud平台的APP ID
         *  @param(second) $app_secret  beecloud平台的APP SECRET
         *  @param(third) $master_secret  beecloud平台的MASTER SECRET
         *  @param(fouth) $test_secret  beecloud平台的TEST SECRET, for sandbox
         **/
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
        //Test Model,只提供下单和支付订单查询的Sandbox模式,不写setSandbox函数或者false即live模式,true即test模式
        $api->setSandbox(false);

        //\beecloud\rest\api::registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
        //\beecloud\rest\api::setSandbox(false);
    }catch(Exception $e){
        die($e->getMessage());
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeePay微信扫码</title>
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
        die($e->getMessage());
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
                alert(JSON.stringify(res)) //供调试使用
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
