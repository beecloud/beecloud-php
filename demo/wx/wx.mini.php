<?php
/**
 * @desc: 微信小程序支付demo
 *
 * 支付参考文档：https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=7_3&index=1
 * 小程序开发参考文档：https://mp.weixin.qq.com/debug/wxadoc/dev/index.html?t=2017621
 *      1、wx.request(OBJECT)参考文档: https://mp.weixin.qq.com/debug/wxadoc/dev/api/network-request.html
 *      2、wx.requestPayment(OBJECT)参考文档: https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-pay.html#wxrequestpaymentobject
 *      3、获取openid参考文档：https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxloginobject
 *
 * @author: jason
 * @since:  2017-08-08 18:59
 */
header("Content-type: text/html; charset=utf-8");
$ret = array('resultCode' => 1);

try{
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    switch($type){
        case 'openid': //小程序支付获取openid
            //小程序的appid和appsecret
            $appid = 'xxx';
            $appsecret = 'xxx';
            $code = isset($_POST['code']) ? trim($_POST['code']) : '';
            if(empty($code)){
                $ret['errMsg'] = '登录凭证code获取失败';
                exit(json_encode($ret));
            }
            $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$appsecret&js_code=$code&grant_type=authorization_code";
            $json = json_decode(file_get_contents($url));
            if(isset($json->errcode) && $json->errcode){
                $ret['errMsg'] = $json->errcode.', '.$json->errmsg;
                exit(json_encode($ret));
            }
            //$session_key = $json->session_key;
            $ret['resultCode'] = 0;
            $ret['openid'] = $json->openid;
            break;
        case 'pay': //小程序支付－获取调起支付的参数
            require_once("../../loader.php");
            require_once("../config.php");

            $openid = isset($_POST['openid']) ? trim($_POST['openid']) : '';
            if(empty($openid)){
                $ret['errMsg'] = '缺少参数openid';
                exit(json_encode($ret));
            }
            $data = array(
                'total_fee' => 1,
                'title' => 'PHP BC_WX_MINI支付测试',
                'channel' => 'BC_WX_MINI',
                //如果是微信官方小程序支付，channel为WX_MINI
                //'title' => 'PHP WX_MINI支付测试',
                //'channel' => 'WX_MINI',
                'bill_no' => 'phpdemo' . time() * 1000,
                'openid' => $openid
            );

            //设置应用参数
            $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
            //\beecloud\rest\api::registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
            $json = $api->bill($data);
            if(isset($json->result_code) && $json->result_code){
                $ret['errMsg'] = $json->err_detail;
                exit(json_encode($ret));
            }
            $ret['resultCode'] = 0;
            $ret['params'] = $json;
            break;
        default :
            $ret['errMsg'] = 'No this type : ' . $type;
            break;
    }
}catch(Exception $e){
    $ret['errMsg'] = $e->getMessage();
}
exit(json_encode($ret));