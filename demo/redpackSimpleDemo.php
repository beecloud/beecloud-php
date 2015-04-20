
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 4/17/15
 * Time: 15:17
 */
set_time_limit(120);
include_once("../BCWxmpRedpack.php");
$usrOpenId = "o3kKrjlUsMnv__cK5DYZMl0JoAkY";//用户openId
$appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719"; //BeeCloud appId
$appSecret = ""; //BeeCloud appSecret 为了保密
$appSign = md5($appId.$appSecret);
$mchId = "1234275402";  //微信商户号
$postStr = "<xml><ToUserName><![CDATA[gh_71e32cfe546c]]></ToUserName><FromUserName><![CDATA[o3kKrjlUsMnv__cK5DYZMl0JoAkY]]></FromUserName><CreateTime>1429494041</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[抢红包]]></Content><MsgId>6139023951558013395</MsgId></xml>";
//在处理微信请求的服务器上请用如下方式获取真实xml
//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
$api = new BCWxmpApi($appId, $appSecret, $mchId);
$msg = $api->getCallMsg($postStr);//解析xml,获取msg内的参数

$redpack = array(
    "nick_name" => "BeeCloud",
    "send_name" => "BeeCloud",
    "total_amount" => 100,
    "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
    "act_name" => "BeeCloud红包雨",
    "remark" => "BeeCloud",
    "count_per_user" => 100, //每个用户能得到红包个数上限
//    "period" => 300000,
    "probability" => 0.3 //获得红包概率 范围0-1
);

echo $api->sendRedpack($redpack);
/**
 * 处理过程请参考以下
 */
//
//
//$raw =  $api->sendRedpack($redpack);
//$result = json_decode($raw);
//if (null == $result) {
//    //发送失败
//    echo $api->responseText($redpackErrMsg);
//    exit();
//} else {
//    if ($result->resultCode == 0) {
//        if ($result->sendStatus) {
//            //发送成功
//            echo $api->responseText($redpackSuccessMsg);
//        } else {
//            if (preg_match("/^该用户已达到发送红包上限/", $result->sendMsg)) {
//                echo $api->responseText($redpackRepeatMsg);
//            } else if (preg_match("/^该用户随机未成功/", $result->sendMsg)) {
//                echo $api->responseText($redpackMissMsg);
//            }
//        }
//    } else if ($result->errMsg == "WX_SERVER_ERROR" && $result->err_code=="NOTENOUGH") {
//        echo $api->responseText($redpackNotenoughMsg);
//    }
//}

?>
</body>