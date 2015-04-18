<?php
$BeeCloudSdkPath = dirname(__FILE__);
set_include_path(get_include_path().PATH_SEPARATOR.$BeeCloudSdkPath);
$BeeCloudLastErr = null;

function BCSetExecErr($msg) {
    global $BeeCloudLastErr;
    $BeeCloudLastErr = $msg;
}

function BCGetExecErr() {
    global $BeeCloudLastErr;
    return $BeeCloudLastErr;
}
/**
 * 在表中一下为保留字段，请不要使用(不区分大小写)
 *  TABLE, KEYSPACE
 */
/**
 *  Func to create BeeCloud condition-obj(stdClass)
 *   @return stdClass return an object
 *   @param string $name conditon query target's column name
 *   @param mixed  $value value to be compared with
 *   @param string $type column compare behavior as below
 *                  "e"- equal with(==)
 *                  "n"- not equal(!=)
 *                  "l"- less than(<)
 *                  "le"-less or equal (<=)
 *                  "g"-great than(>)
 *                  "ge"-great or equal(>=)
 *                  "c"-contained in
 *                  "gl"-geopoint is not more than ? meter away
 *                  "pre"-string prefix
 *                  "suf"-string suffix
 *                  "sub"-sub string
 *                  "reg"-match with regular expression
 *   @param string $op operation for this column
 */
function BCCondition($name, $type, $value) {
    $that = new stdClass();
    $that->cname = $name;
    $that->type = $type;
    $that->value = $value;
    return $that;
}

/**
 *  Func to create BeeCloud column-obj(stdClass)
 *   @return stdClass return an object
 *   @param string $name column name
 *   @param mixed  $value（according to type）column value
 *   @param string $type column type as below
 *                  "s"-string
 *                  "i"-int
 *                  "l"-long
 *                  "f"-float
 *                  "d"-double
 *                  "b"-boolean
 *                  "t"-timestamp
 *                  "g"-geopoint ("latitude, longitude")
 *                  "n"-null
 *   @param string $op operation for this column
 */
function BCColumn($name, $type, $value, $op) {
    $that = new stdClass();
    $that->cname = $name;
    $that->type = $type;
    switch ($type) {
        case "i":
            $that->value = intval($value);
            break;
        case "f":
            $that->value = floatval($value);
            break;
        case "b":
            $that->value = boolval($value);
            break;
        default:
            $that->value = $value;
    }

    if (isset($op)) {
        $that->op = $op;
    }
    return $that;
}

class BCSetting {
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
            BCSetExecErr($raw);
        } else {
            BCSetExecErr($result->errMsg);
        }
        return false;
    }
}

class BCWxmpApi {
    static public $debugMsg = null;

    static public function checkSignature(array $data, $token)
    {
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

        return BCWxmpRedPackHttp::request($serverUrl . "/pay/wxmp/redPack", "get", $redpack, $timeout);
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
        return $msgObj;
    }
}

