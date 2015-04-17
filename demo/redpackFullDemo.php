<?php
set_time_limit(120);
include_once("../BCWxmpRedpack.php");

//微信验证 的 处理, 可以去除
if (isset($_GET["signature"]) && isset($_GET["timestamp"])
    && isset($_GET["nonce"]) && isset($_GET["echostr"])) {
    $echoStr = $_GET["echostr"];
    if(BCWxmpApi::checkSignature($_GET, "beecloud")){
        echo $echoStr;
    }
    exit();
}
$appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719"; //BeeCloud appId
$appSecret = ""; //BeeCloud appSecret 为了保密
$mchId = "1234275402";  //微信商户号
$lockPath = "/tmp/"; // file lock path, 每用户互斥锁地址, linux 上默认为/tmp/
$salt = null;
try {
    $api = new BCWxmpApi($appId, $appSecret, $mchId, $salt, $lockPath);

    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"]; //post 原始数据
    $msg = $api->getCallMsg($postStr);//解析xml

    /**
     * 返回为 object : { type: "微信类型",
     *                  keyword:"用户输入的字符串“,
     *                  event: "微信事件类型"
     *                  fromUserName: "发送信息的用户openId"
     *                  toUserName: "目标公众号id"
     *                  xmlObj: 解析的完整结果
     *                  }
     * 如果 返回false 表示数据不能解析
     **/
    if ( $msg == false) {
        exit();
    }
    $type = $msg->type;
    if ($type == "event") {
        $event = $msg->event;
        switch ($event) {
            case "subscribe":
                //关注事件
                echo $api->responseText("老板快来,又一个要红包的!");
                break;
        }
        exit();
    } else if ($type == "text") {
        $keyword = $msg->keyword; //用户输入的字符串
        $rumors = array();
        $seed = rand(0,3);
        $rumors[0] = "老板已穷,躲在厕所哭呢";
        $rumors[1] = "老板卖身发红包,还没卖出去...";
        $rumors[2] = "红包不小心出bug了,老板倾家荡产";
        $rumors[3] = "中国最大红包厂,江南红包厂倒闭了,王八蛋老板吃喝嫖赌，欠下了3.5个亿，带着他的小姨子跑了!我们没有办法发红包...";
        switch ($keyword) {
            case "红包拿来":
                echo $api->responseText($rumors[$seed]);
                break;
            case "rumortest":
                $responseMsg = array(
                    "err" => "红包出错啦", //出错时提醒
                    "finish" => $rumors[$seed], //红包发完了
                    "repeat" =>"客官请不要这样,红包只有一次,就像那啥", //用户重复请求
                    "hint" => "大吉大利,红包就来" //红包发送后提示用户
                );

                $redpack = array(
                    "nick_name" => "BeeCloud",
                    "send_name" => "BeeCloud",
                    "total_amount" => 100,
                    "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
                    "act_name" => "BeeCloud红包雨",
                    "remark" => "BeeCloud"); //mch_id + yyyymmdd + timestamp
                $result = $api->sendRedpackWithBeeCloud($redpack, $responseMsg);// true means redpack has been sent, each one can get only one
                break;
            default:

        }
    }
    exit();
} catch (Exception $e) {
    if (BCSetting::$wxmpRedpackDebug) {
        echo BCWxmpApi::responseDebugText($e->getMessage());
    }
}

