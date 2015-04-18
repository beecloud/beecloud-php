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
                echo $api->responseText("敬请期待红包哟!");
                break;
        }
        exit();
    } else if ($type == "text") {
        $keyword = $msg->keyword; //用户输入的字符串
        $rumors = array();
        $seed = rand(0,3);
        $rumors[0] = "客官们。。。手真快";
        $rumors[1] = "老板光荣滴跑了,没钱只能调戏你作为补偿了";
        $rumors[2] = "老板再富,也被我发完了";
        $rumors[3] = "红包虽好,架不住人多";
        switch ($keyword) {
            case "红包拿来":
                $tempMsg = "向各位亲说句抱歉，今天遇到一些技术问题短时间内无法解决。我们约定周一中午12点，一次发放1000个红包。后台截图为证。向等候多时的各位亲道个歉。请关注我们的微博@BeeCloud，我们会持续不断发红包，会在新浪微博上发布红包消息的，请亲们关注~~";
                echo $api->responseText($tempMsg);
                break;
            case "12377test":
                $responseMsg = array(
                    "err" => "红包出错啦", //出错时提醒
                    "finish" => $rumors[$seed], //红包发完了
                    "repeat" =>"客官,红包虽好,请不要贪杯哦", //用户重复请求
                    "hint" => "大吉大利,红包给你" //红包发送后提示用户
                );
                $redpack = array(
                    "nick_name" => "BeeCloud",
                    "send_name" => "BeeCloud",
                    "total_amount" => 100,
                    "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
                    "act_name" => "BeeCloud红包雨",
                    "remark" => "BeeCloud"); //mch_id + yyyymmdd + timestamp
                $result = $api->sendRedpackRapidWithBeeCloud($redpack, $responseMsg);// true means redpack has been sent, each one can get only one
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

    public function sendRedpackWithBeeCloud(array $redpack, array $responseMsg) {
        $errMsg = isset($responseMsg["err"]) ? $responseMsg["err"] : "红包出错啦";
        $finishMsg = isset($responseMsg["finish"]) ? $responseMsg["finish"] : "手慢,红包发完咯";
        $repeatMsg = isset($responseMsg["repeat"]) ? $responseMsg["repeat"] : "客官请不要这样,红包只有第一次,就像那啥";
        $hintMsg = $responseMsg["hint"];

        $fromUserName = $this->msg->fromUserName;
        $keyword = $this->msg->keyword;

        if (isset($this->salt) && !empty($this->salt)) {
            $keyword = ($this->msg->keyword).$this->salt;
        }


        $queryData = array(
            "appId" => $this->appId,
            "appSign" => $this->appSign,
            "table" => "wechat_red_package__",
            "conditions" => array(BCCondition("openid", "e", "$fromUserName"),BCCondition("redpackage","e", "$keyword")
            ),
            "conditionConnector" => "AND",
        );

        $raw = BCWxmpRedPackHttp::request(BCWxmpRedPackSetting::$serverURL."/query/byCondition", "get", $queryData, 30);
        $result = BCWxmpRedPackHttp::formatResponse($raw);

        if (!$result) {
            $bcErrMsg = BCGetExecErr();
            if ($bcErrMsg != "TABLE_NOT_EXIST") {
                echo $this->responseText($errMsg);
                return false;
            }
        }
        if(!empty($result->results)) {
            // this user has got same red-pack before
            echo $this->responseText($repeatMsg);
        } else {
            // Mutual  lock  when sending redpack on single server.
            // In distributed system, do with database's column - uuid and createdat, later and in another method
            $path = isset($this->lockPath) ?  $this->lockPath : "/tmp/";
            $file = fopen($path.$fromUserName.$keyword,"w");
            if ($file == FALSE) {
                return false;
            }
            if (!flock($file,LOCK_EX)) {
                fclose($file);
                return false;
            }

            $raw = BCWxmpApi::sendWxmpRedpack(BCWxmpRedPackSetting::$serverURL, $fromUserName, $redpack, $this, 30);
            $result = json_decode($raw);
            if ($result == null) {
                // release lock
                echo $this->responseText($errMsg);
            }
            if ($result != null) {
                if ($result->resultCode == 0) {
                    if ($result->return_code == "SUCCESS") {
                        if (isset($hintMsg)) {
                            echo $this->responseText($hintMsg);
                        }
                        $insertData = array(
                            "appId" => $this->appId,
                            "appSign" => $this->appSign,
                            "table" => "wechat_red_package__",
                            "columns" => array(BCColumn("openid", "s", "$fromUserName"),BCColumn("redpackage", "s", "$keyword")
                            )
                        );

                        BCWxmpRedPackHttp::request(BCWxmpRedPackSetting::$serverURL."/insert", "post", $insertData, 30);
                    } else {
                        // release lock
                        echo $this->responseText($errMsg);
                    }
                } else if ($result->errMsg == "WX_SERVER_ERROR" && $result->err_code=="NOTENOUGH") {
                    echo $this->responseText($finishMsg);
                }
            }
            // release lock
            flock($file,LOCK_UN);
            fclose($file);
        }
        return true;
    }

    /**
     * @param array $redpack
     * @param array $responseMsg
     * @return bool
     * @alert rapid method do not have mutual lock when sending redpack.So one user might get multiple redpack;
     */
    public function sendRedpackRapidWithBeeCloud(array $redpack, array $responseMsg) {
        $errMsg = isset($responseMsg["err"]) ? $responseMsg["err"] : "红包出错啦";
        $finishMsg = isset($responseMsg["finish"]) ? $responseMsg["finish"] : "手慢,红包发完咯";
        $repeatMsg = isset($responseMsg["repeat"]) ? $responseMsg["repeat"] : "客官请不要这样,红包只有第一次,就像那啥";
        $hintMsg = $responseMsg["hint"];

        $fromUserName = $this->msg->fromUserName;
        $keyword = $this->msg->keyword;

        // reuse keyword
        if (isset($this->salt) && !empty($this->salt)) {
            $keyword = ($this->msg->keyword).$this->salt;
        }


        $queryData = array(
            "appId" => $this->appId,
            "appSign" => $this->appSign,
            "table" => "wechat_red_package__",
            "conditions" => array(BCCondition("openid", "e", "$fromUserName"),BCCondition("redpackage","e", "$keyword")
            ),
            "conditionConnector" => "AND",
        );
        $serverUrl = BCWxmpRedPackSetting::getServerRandomUrl();
        $raw = BCWxmpRedPackHttp::request($serverUrl."/query/byCondition", "get", $queryData, 5);
        $result = BCWxmpRedPackHttp::formatResponse($raw);



        if (!$result) {
            $bcErrMsg = BCGetExecErr();
            if ($bcErrMsg != "TABLE_NOT_EXIST") {
                echo $this->responseText($errMsg."condition:".$raw);
                return false;
            }
        }
        if(!empty($result->results)) {
            // this user has got same red-pack before
            echo $this->responseText($repeatMsg);
            return false;
        }

        $insertData = array(
            "appId" => $this->appId,
            "appSign" => $this->appSign,
            "table" => "wechat_red_package__",
            "columns" => array(BCColumn("openid", "s", "$fromUserName"),BCColumn("redpackage", "s", "$keyword")
            )
        );

        $raw = BCWxmpRedPackHttp::request($serverUrl."/insert", "post", $insertData, 5);
        $result = BCWxmpRedPackHttp::formatResponse($raw);
        if (!$result) {
            echo $this->responseText($errMsg."insert:".$raw);
            return false;
        }


        $raw = BCWxmpApi::sendWxmpRedpack($serverUrl, $fromUserName, $redpack, $this, 5);
        $result = BCWxmpRedPackHttp::formatResponse($raw);
        if (!$result) {
            echo $this->responseText($errMsg."redpack:".$raw);
            return false;
        }

        if ($result->resultCode == 0) {
            if ($result->return_code == "SUCCESS") {
                if (isset($hintMsg)) {
                    echo $this->responseText($hintMsg);
                }
            } else {
                echo $this->responseText($errMsg);
            }
        } else if ($result->errMsg == "WX_SERVER_ERROR" && $result->err_code=="NOTENOUGH") {
            echo $this->responseText($finishMsg);
        }

        return true;
    }
}

