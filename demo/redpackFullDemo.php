<?php
set_time_limit(120);
include_once("../BCWxmpRedpack.php");

/**
 * 以下为配置红包参数 和 红包提示
 */
$redpackErrMsg = "抢的人多, 请再接再厉";
$redpackSuccessMsg = "红包给你,大吉大利";
$redpackRepeatMsg = "客官真健忘,你领过啦";
$redpackMissMsg = "很遗憾，没抢到红包，再接再厉,红包总会有的";
$redpackNotenoughMsg = "今天的抢红包活动已经结束，请关注我们的新浪微博@BeeCloud，获取最新的抢红包信息";

/**
 * BeeCloud参数设置
 */
$appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719"; //BeeCloud appId
$appSecret = ""; //BeeCloud appSecret 为了保密不给你看

/**
 * 微信商户号
 */
$mchId = "";  //微信商户号

/**
 * 请在函数内设置你的红包参数
 * @return array
 */
function BCGetWxmpRedpack() {
    //此处配置你的红包设置
    $randomAmount = 100 + rand(0, 5);
    $redpack = array(
        "nick_name" => "BeeCloud",
        "send_name" => "BeeCloud",
        "total_amount" => $randomAmount,
        "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
        "act_name" => "BeeCloud红包雨",
        "remark" => "BeeCloud",
        "count_per_user" => 1,
        "probability" => 0.3
        //    "period" => 300000,
    );
    return $redpack;
}

/**
 * 设置微信url时需要验证,以下为验证过程
 */
if (isset($_GET["signature"]) && isset($_GET["timestamp"])
    && isset($_GET["nonce"]) && isset($_GET["echostr"])) {
    $echoStr = $_GET["echostr"];
    if(BCWxmpApiUtil::checkSignature($_GET, "beecloud")){
        echo $echoStr;
    }
    exit();
}


try {
    $api = new BCWxmpApi($appId, $appSecret, $mchId);
    //模拟xml
//     $postStr = "<xml><ToUserName><![CDATA[gh_71e32cfe546c]]></ToUserName><FromUserName><![CDATA[o3kKrjlUsMnv__cK5DYZMl0JoAkY]]></FromUserName><CreateTime>1429494041</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[抢红包]]></Content><MsgId>6139023951558013395</MsgId></xml>";
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
        echo $api->responseText("红包出错了");
        exit();
    }

    $type = $msg->type;
    if ($type == "event") {
        $event = $msg->event;
        switch ($event) {
            case "subscribe":
                //关注事件
                echo $api->responseText("敬请期待红包哟!");
                break;
        }
        exit();
    } else if ($type == "text") {
        $keyword = $msg->keyword; //用户输入的字符串
        switch ($keyword) {
            case "红包拿来":
                /**
                 * $api->responseText 用于输出微信要求的xml
                 */
                $tempMsg = "输入\"抢红包\"，你懂的；输入\"剩余红包\"查询剩余红包个数。1000个红包已经到来！请关注我们的微博@BeeCloud，我们会持续不断发红包，会在新浪微博上发布红包消息的，请亲们关注~~";
                echo $api->responseText($tempMsg);
                break;
            case "抢红包":
                $redpack = BCGetWxmpRedpack();
                $raw = $api->sendRedpack($redpack);//
                $result = json_decode($raw);
                if (null == $result) {
                    //发送失败
                    echo $api->responseText($redpackErrMsg);
                    exit();
                } else {
                    if ($result->resultCode == 0) {
                        if ($result->sendStatus) {
                            //发送成功
                            echo $api->responseText($redpackSuccessMsg);
                        } else {
                            if (preg_match("/^该用户已达到发送红包上限/", $result->sendMsg)) {
                                echo $api->responseText($redpackRepeatMsg);
                            } else if (preg_match("/^该用户随机未成功/", $result->sendMsg)) {
                                echo $api->responseText($redpackMissMsg);
                            }
                        }
                    } else if ($result->errMsg == "WX_SERVER_ERROR" && $result->err_code=="NOTENOUGH") {
                        echo $api->responseText($redpackNotenoughMsg);
                    }
                }
                break;
            default:
                break;
        }
    }
    exit();
} catch (Exception $e) {
    if (BCWxmpRedPackSetting::$wxmpRedpackDebug) {
        //通过 微信显示debug信息,可以本地写log
        $msg =  BCWxmpApiUtil::responseDebugText($e->getMessage());
        if ($msg != false) {
            echo $msg;
        }
    }
}



