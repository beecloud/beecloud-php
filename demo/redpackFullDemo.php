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
$appSecret = ""; //BeeCloud appSecret 为了保密不给你看
$mchId = "1234275402";  //微信商户号
$salt = null;
try {
    $api = new BCWxmpApiDemo($appId, $appSecret, $mchId, $salt);
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"]; //post 原始数据
//    $postStr = "<xml><ToUserName><![CDATA[test]]></ToUserName><FromUserName><![CDATA[test]]></FromUserName><CreateTime>1429852898</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[12377test]]></Content><MsgId>6139023951558013395</MsgId></xml>";
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

    if (isset($msg->xmlObj->BCDebug)) {
        //并发测试 直接打印
        //发送过程忽略
        echo "BeeCloud Debug";
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
                $tempMsg = "向各位亲说句抱歉，今天遇到一些技术问题短时间内无法解决。我们约定周一中午12点，一次发放1000个红包。后台截图为证。向等候多时的各位亲道个歉。请关注我们的微博@BeeCloud，我们会持续不断发红包，会在新浪微博上发布红包消息的，请亲们关注~~";
                echo $api->responseText($tempMsg);
                break;
            case "12377test":
                $redpack = array(
                    "nick_name" => "BeeCloud",
                    "send_name" => "BeeCloud",
                    "total_amount" => 100,
                    "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
                    "act_name" => "BeeCloud红包雨",
                    "remark" => "BeeCloud",
                    "count_per_user" => 1
                    //    "period" => 300000,
                    //    "probability" => 0.3
                );
                $raw = $api->sendRedpack($redpack);// true means redpack has been sent, each one can get only one
//                $result = BCWxmpRedPackHttp::formatResponse($raw);
//                if (false == $result) {
//                    //发送失败
//                } else {
//                    if ($result->sendStatus) {
//                        //发送成功
//                    } else {
//
//                    }
//                }
                break;
            default:

        }
    }
    exit();
} catch (Exception $e) {
    if (BCWxmpRedPackSetting::$wxmpRedpackDebug) {
        //通过 微信显示信息,可以本地写log
        echo BCWxmpApi::responseDebugText($e->getMessage());
    }
}



class BCWxmpApiDemo {
    public $appSign;
    public $appId;
    public $msg;
    public $mchId;
    public function __construct($appId, $appSecret, $mchId, $salt) {
        if (empty($appId) || empty($appSecret) || empty($mchId)) {
            throw new BCException('除 lockPath ,请检查参数不能为空');
        }
        if (!is_string($appId) || !is_string($appSecret) || !is_string($mchId)) {
            throw new BCException('输入必须是String类型');
        }
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->appSign = md5($appId.$appSecret);
        $this->msg = new stdClass();
        $this->mchId = $mchId;
        $this->salt = $salt;
    }
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
    public function getCallMsg($postStr) {
//        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->msg = BCWxmpApi::_getCallMsg($postStr);
            if (BCWxmpRedPackSetting::$wxmpRedpackDebug) {
                BCWxmpApi::$debugMsg = $this->msg;
            }
            return $this->msg;
        }
        return false;
    }

    /**
     * @param $news {title, description, picUrl, linkUrl}
     * @return string
     */
    public function responseNews($news) {
        $fromUsername = $this->msg->fromUserName;
        $toUsername = $this->msg->toUserName;
        $uxtime = time();
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>1</ArticleCount>
        <Articles>
        <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
        </item>
        </Articles>
        </xml>";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $uxtime, $news->title, $news->description, $news->picUrl, $news->linkUrl);
        return $resultStr;
    }
    public function responseText($text) {
        $fromUsername = $this->msg->fromUserName;
        $toUsername = $this->msg->toUserName;
        $uxtime = time();
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        <FuncFlag>0<FuncFlag>
        </xml>";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $uxtime, $text);
        return $resultStr;
    }

    /**
     * 发送红包,啥都不管;云端处理所有红包的检查
     * @param array $redpack
     * @return mixed|string
     */
    public function sendRedpack(array $redpack) {
        $keyword = $this->msg->keyword;
        $fromUserName = $this->msg->fromUserName;
        //为不同发送红包设置不同salt,不同活动可以复用同一个关键字
        if (isset($this->salt) && !empty($this->salt)) {
            $keyword = ($this->msg->keyword).$this->salt;
        }

        return BCWxmpApi::sendWxmpRedpack(BCWxmpRedPackSetting::getServerRandomUrl(), $fromUserName, $redpack, $this, 30);
    }
}

