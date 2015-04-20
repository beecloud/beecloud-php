<?php
$BeeCloudWxmpSdkPath = dirname(__FILE__);
set_include_path(get_include_path().PATH_SEPARATOR.$BeeCloudWxmpSdkPath);
$BeeCloudWxmpLastErr = null;

function BCWxmpSetExecErr($msg) {
    global $BeeCloudWxmpLastErr;
    $BeeCloudWxmpLastErr = $msg;
}

function BCWxmpGetExecErr() {
    global $BeeCloudWxmpLastErr;
    return $BeeCloudWxmpLastErr;
}


class BCWxmpRedPackSetting {
    static public $serverURL = "https://api.beecloud.cn/1";
    static public $wxmpRedpackDebug = false;
    static public function setWxmpRedpackDebug($debug) {
        self::$wxmpRedpackDebug = !!$debug;
    }

    static public function getServerRandomUrl() {
        $ipList = array(0=>"https://120.24.222.220/1",
                1=>"https://115.28.40.236/1",
                2=>"https://123.57.71.81/1",
                3=>"https://121.41.120.98/1");
        $seed = rand(0,3);
        return $ipList[$seed];
    }
}

class BCWxmpRedPackHttp {
    static final public function request($url, $method, array $data, $timeout) {
        try {
            $timeout = (isset($timeout) && is_int($timeout)) ? $timeout : 20;
            $ch = curl_init();
            /*支持SSL 不验证CA根验证*/
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            /*重定向跟随*/
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_REFERER, "BCPhp");
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            if (!empty($timeout)) {
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            } else {
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            }

            //设置 CURLINFO_HEADER_OUT 选项之后 curl_getinfo 函数返回的数组将包含 cURL
            //请求的 header 信息。而要看到回应的 header 信息可以在 curl_setopt 中设置
            //CURLOPT_HEADER 选项为 true
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLINFO_HEADER_OUT, false);

            //fail the request if the HTTP code returned is equal to or larger than 400
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            $header = array("Content-Type:application/json;charset=utf-8;", "Connection: keep-alive;");
            $methodIgnoredCase = strtolower($method);
            switch ($methodIgnoredCase) {
                case "raw_post":
                    $header = array("Content-Type:application/json;charset=utf-8;", "Connection: keep-alive;");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POST, true);
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //POST数据
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                case "post":
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                case "put":
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                case "get":
                    curl_setopt($ch, CURLOPT_URL, $url."?para=".urlencode(json_encode($data)));
                    break;
                case "raw_get":
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                default:
                    throw new Exception('不支持的HTTP方式');
            }

            $result = curl_exec($ch);
            if (curl_errno($ch) > 0) {
                throw new Exception(curl_error($ch));
            }
            curl_close($ch);
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    static public function formatResponse($raw) {
        $result = json_decode($raw);
        if (isset($result) && $result->resultCode == 0) {
            return $result;
        }
        if (!isset($result) || $result == null){
            BCWxmpSetExecErr($raw);
        } else {
            BCWxmpSetExecErr($result->errMsg);
        }
        return false;
    }
}

class BCWxmpApiUtil {
    static public $debugMsg = null;

    static public function checkSignature(array $data, $token){
        $signature = $data["signature"];
        $timestamp = $data["timestamp"];
        $nonce = $data["nonce"];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    static public function responseDebugText($text) {
        $fromUsername = self::$debugMsg->fromUserName;
        if (!isset($fromUsername))
            return false;
        $toUsername = self::$debugMsg->toUserName;
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

    static public function sendWxmpRedpack($serverUrl, $fromUsername, $redpack, $beecloud, $timeout) {
        $timeout = isset($timeout) ? $timeout : 30;
        $now = time();
        $redpack["appId"] = $beecloud->appId;
        $redpack["appSign"] = $beecloud->appSign;
        $redpack["re_openid"] = "$fromUsername";
        $redpack["mch_billno"] = ($beecloud->mchId) . date("Ymd", $now) . $now;//mch_id + yyyymmdd + timestamp

        return BCWxmpRedPackHttp::request($serverUrl . "/pay/wxmp/redPackExtra", "get", $redpack, $timeout);
    }


    static public function _getCallMsg($msgStr) {
        $msgObj = new stdClass();
        $postObj = simplexml_load_string($msgStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $msgObj->keyword = trim($postObj->Content);
        $msgObj->type = $postObj->MsgType;
        $msgObj->event = $postObj->Event;
        $msgObj->xmlObj = $postObj;
        $msgObj->fromUserName = $postObj->FromUserName;
        $msgObj->toUserName = $postObj->ToUserName;
        //xmlObj->BCDebug for debug use
        return $msgObj;
    }
}



class BCWxmpApi {
    public $appSign;
    public $appId;
    public $msg;
    public $mchId;
    public function __construct($appId, $appSecret, $mchId) {
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
            $this->msg = BCWxmpApiUtil::_getCallMsg($postStr);
            if (BCWxmpRedPackSetting::$wxmpRedpackDebug) {
                BCWxmpApiUtil::$debugMsg = $this->msg;
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
        $fromUserName = $this->msg->fromUserName;
        return BCWxmpApiUtil::sendWxmpRedpack(BCWxmpRedPackSetting::getServerRandomUrl(), $fromUserName, $redpack, $this, 30);
    }
}

