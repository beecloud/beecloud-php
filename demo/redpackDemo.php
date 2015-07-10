
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
/**
 * 本demo适用于通过“微信授权后获取到用户openId”的情况下,直接发送红包给用户
 * 例如：授权的网页内
 */
set_time_limit(120);
include_once("../BCWxmpRedpack.php");
$usrOpenId = "o3kKrjlUsMnv__cK5DYZMl0JoAkY";//用户openId
$appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719"; //BeeCloud appId !!!此处请用你的BeeCloud appId
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c"; //BeeCloud appSecret  !!!此处请用你的BeeCloud appSecret
$appSign = md5($appId.$appSecret);
$mchId = "1234275402";  //微信商户号,请填写你的商户号

$api = new BCWxmpApi($appId, $appSecret, $mchId);

$redpack = array(
    "nick_name" => "BeeCloud",
    "send_name" => "BeeCloud",
    "total_amount" => 100, //（分）红包固定金额
    "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
    "act_name" => "BeeCloud红包雨",
    "remark" => "BeeCloud",
    /**
     * 此处特别注意count_per_user, period根据你的需求的设置
     */
    "count_per_user" => 100, //在当前时间t到 t - period时间内每个用户能得到红包个数上限(选填，默认为1)
//    "period" => 300000, //（ms）用户领取红包的判重时间长度,默认为所有时间内
    "probability" => 0.3 //（float）单次获得红包概率 范围0-1, 默认为1
);

$raw = $api->sendRedpackTo($usrOpenId, $redpack, 30);
echo $raw;
/**
 * 处理过程请参考以下
 */
//
//$result = json_decode($raw);
//if (null == $result) {
//    //发送失败
//    echo $api->responseText("出错信息");
//    exit();
//} else {
//    if ($result->resultCode == 0) {
//        if ($result->sendStatus) {
//            //发送成功
//
//        } else {
//            if (preg_match("/^该用户已达到发送红包上限/", $result->sendMsg)) {
//
//            } else if (preg_match("/^该用户随机未成功/", $result->sendMsg)) {
//
//            }
//        }
//    } else if ($result->errMsg == "WX_SERVER_ERROR" && $result->err_code=="NOTENOUGH") {
//        //商户余额不足
//    }
//}

?>
</body>