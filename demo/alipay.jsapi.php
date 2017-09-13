<?php
/**
 * @desc: bc alipay jsapi（支付宝服务窗）
 * 1.oauth认证获取user_id，参考资料: https://doc.open.alipay.com/docs/doc.htm?treeId=220&articleId=105337&docType=1#s5
 * 2.支付宝jsapi调起支付，参考资料：https://doc.open.alipay.com/docs/doc.htm?&docType=1&articleId=105591
 *
 * @author: jason
 * @since:  2017-06-13 10:04
 */
define("AOP_SDK_WORK_DIR", "/tmp/");

require_once("../loader.php");
require_once("config.php");
require_once 'lib/alipay/aop/AopClient.php';
require_once 'lib/alipay/aop/request/AlipaySystemOauthTokenRequest.php';

header("Content-type: text/html; charset=utf-8");
$alipay_app_id = 'xxxxxx'; //支付宝开放平台创建应用的APP ID

if(!isset($_GET['auth_code'])){
    $redirect_uri = urlencode('http://www.xxx.com/demo/alipay.jsapi.php');
    $url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=$alipay_app_id&scope=auth_userinfo&redirect_uri=$redirect_uri";
    header('Location:'.$url);
}else{
    try {
        $aop = new AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $alipay_app_id;
        $aop->rsaPrivateKey = 'xxxxxxx'; //创建应用配置的私钥（请填写开发者私钥去头去尾去回车，一行字符串；和验签名方式保持一致，推荐RSA2方式）
        $aop->alipayrsaPublicKey='xxxxxxx'; //创建应用配置的支付宝公钥（请填写支付宝公钥，一行字符串；和验签名方式保持一致，推荐RSA2方式）
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';

        //获取access_token
        $request = new AlipaySystemOauthTokenRequest ();
        $request->setGrantType("authorization_code");
        $request->setCode($_GET['auth_code']);
        $result = $aop->execute ($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if(isset($result->$responseNode->user_id)){
            $user_id = $result->$responseNode->user_id;
        } else {
            echo '<pre>';
            print_r($result);die;
        }

        $data = array();
        $data["timestamp"] = time() * 1000;
        //total_fee(int 类型) 单位分
        $data["total_fee"] = 1;
        $data["bill_no"] = "phpdemo" . $data["timestamp"];
        //title UTF8编码格式，32个字节内，最长支持16个汉字
        $data["title"] = 'PHP BC_ALI_JSAPI支付测试';
        $data["channel"] = "BC_ALI_JSAPI";
        $data["openid"] = $user_id;
        $aop = new AopClient ();
        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
        $result = $api->bill($data);
        if ($result->result_code != 0) {
            echo '<pre>';
            print_r($result);die;
        }
        $trade_no = $result->trade_no;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝JSAPI支付</title>
</head>
<script type="text/javascript">
    //调用支付宝JS api 支付
    function jsApiCall() {
        AlipayJSBridge.call("tradePay",{
            tradeNO: '<?php echo $trade_no;?>'
        }, function(data){
            /**
             *  名称              类型                      描述
             *  resultCode      string               支付结果：
                                                     ‘9000’: 订单支付成功;
                                                     ‘8000’: 正在处理中;
                                                     ‘4000’: 订单支付失败;
                                                     ‘6001’: 用户中途取消;
                                                     ‘6002’: 网络连接出错
                                                     ‘99’: 用户点击忘记密码快捷界面退出(only iOS since 9.5)
             *  callbackUrl     bool                 交易成功后应跳转到的url；一般为空, 除非交易有特殊配置
             *  memo            bool                 收银台服务端返回的memo字符串
             *  result          bool                 收银台服务端返回的result字符串
             */
            //alert(JSON.stringify(data));
            if ('9000' == data.resultCode) {
                alert("支付成功");
            }else if ('6001' == data.resultCode) {
                alert("取消支付");
            }else if ('4000' == data.resultCode) {
                alert("支付失败");
            }else if ('8000' == data.resultCode) {
                alert("正在处理中...");
            }
            // 通过jsapi关闭当前窗口
            //AlipayJSBridge.call('closeWebview');
        });
    }
    function callpay() {
        if (typeof AlipayJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('AlipayJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('AlipayJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
</script>
<body onload="callpay();"> </body>
</html>