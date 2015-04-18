<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 4/17/15
 * Time: 15:17
 */
set_time_limit(120);
include_once("../BCWxmpRedpack.php");
$usrOpenId = "o3kKrjpXbcZ3fLR6zu-3B7B0hW-4";//用户openId
$appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719"; //BeeCloud appId
$appSecret = ""; //BeeCloud appSecret 为了保密
$appSign = md5($appId.$appSecret);
$mchId = "1234275402";  //微信商户号


$redpack = array(
    "nick_name" => "BeeCloud",
    "send_name" => "BeeCloud",
    "total_amount" => 100,
    "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
    "act_name" => "BeeCloud红包雨",
    "remark" => "BeeCloud"); //mch_id + yyyymmdd + timestamp

$beecloud = new stdClass();
$beecloud->appId = $appId;
$beecloud->appSign = $appSign;
$beecloud->mchId = $mchId;

echo BCWxmpApi::sendWxmpRedpack(BCSetting::$serverURL, $usrOpenId, $redpack, $beecloud, 30);

