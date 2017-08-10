<?php
/**
 * @desc: 微信小程序支付demo（不依赖类库，可以单独复制文件夹使用，即demo/wx/wxmini）
 *
 * 支付参考文档：https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=7_3&index=1
 * 小程序开发参考文档：https://mp.weixin.qq.com/debug/wxadoc/dev/index.html?t=2017621
 *      1、wx.request(OBJECT)参考文档: https://mp.weixin.qq.com/debug/wxadoc/dev/api/network-request.html
 *      2、wx.requestPayment(OBJECT)参考文档: https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-pay.html#wxrequestpaymentobject
 *      3、获取openid参考文档：https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxloginobject
 * @author: jason
 * @since:  2017-08-08 18:59
 */
header("Content-type: text/html; charset=utf-8");
$ret = array('resultCode' => 1);
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
switch($type){
    case 'openid': //小程序支付获取openid
        //小程序的appid和appsecret
        $appid = 'wx22f0f5085f846137';
        $appsecret = '3e5f684e2948de6f74b487386692940a';
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
        exit(json_encode($ret));
        break;
    case 'pay': //BeePay小程序支付－获取调起支付的参数
        $openid = isset($_POST['openid']) ? trim($_POST['openid']) : '';
        if(empty($openid)){
            $ret['errMsg'] = '缺少参数openid';
            exit(json_encode($ret));
        }
        //beecloud平台创建的应用appid和appsecret
        $appid = 'c5d1cba1-5e3f-4ba0-941d-9b0a371fe719';
        $appsecret = '39a7a518-9ac8-4a9e-87bc-7885f33cf18c';
        $timestamp = time() * 1000;
        $data = array(
            'app_id' => $appid,
            'timestamp' => $timestamp,
            'app_sign' => md5($appid . $timestamp . $appsecret),
            'total_fee' => 1,
            'title' => 'PHP BC_WX_MINI支付测试',
            'channel' => 'BC_WX_MINI',
            'bill_no' => 'phpdemo' . $timestamp,
            'openid' => $openid
        );
        $rs = request('https://api.beecloud.cn/2/rest/bill', $data);
        $json = json_decode($rs);
        if(!$json){
            $ret['errMsg'] = $rs;
            exit(json_encode($ret));
        }else if(isset($json->resultCode) && $json->resultCode){
            $ret['errMsg'] = $json->errMsg ? $json->errMsg : $json->err_detail;
            exit(json_encode($ret));
        }
        $ret['resultCode'] = 0;
        $ret['params'] = $json;
        exit(json_encode($ret));
        break;
    default :
        $ret['errMsg'] = 'No this type : ' . $type;
        exit(json_encode($ret));
        break;
}

function request($url, array $data, $timeout = 30) {
    try {
        $ch = curl_init();
        /*支持SSL 不验证CA根验证*/
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        /*重定向跟随*/
        if (ini_get('open_basedir') == '' && !ini_get('safe_mode')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        //设置 CURLINFO_HEADER_OUT 选项之后 curl_getinfo 函数返回的数组将包含 cURL
        //请求的 header 信息。而要看到回应的 header 信息可以在 curl_setopt 中设置
        //CURLOPT_HEADER 选项为 true
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);

        //fail the request if the HTTP code returned is equal to or larger than 400
        //curl_setopt($ch, CURLOPT_FAILONERROR, true);
        $header = array("Content-Type:application/json;charset=utf-8;", "Connection: keep-alive;");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        if (curl_errno($ch) > 0) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        return $result;
    } catch (Exception $e) {
        return json_encode(array('resultCode' => 1, 'errMsg' => "CURL EXCEPTION: ".$e->getMessage()));
    }
}