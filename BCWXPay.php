<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 3/31/15
 * Time: 18:13
 */
$BeeCloudSdkPath = dirname(__FILE__);
set_include_path(get_include_path().PATH_SEPARATOR.$BeeCloudSdkPath);
include_once("dependency/WxPayPubHelper/WxPayPubHelper.php");
include_once("config/BCPayConfig.php");


class BCWXHttp {

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
                case "post":
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                case "get":
                    curl_setopt($ch, CURLOPT_URL, $url."?para=".urlencode(json_encode($data)));
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
}


class BCWXPayUtil {
    static public function getOpenID(array $data) {
        //使用jsapi接口
        $jsApi = new JsApi_pub();
        //=========步骤1：网页授权获取用户openid============
        //通过code获得openid
        if (!isset($data['code'])){
            //触发微信返回code码
            $url = $jsApi->createOauthUrlForCode(WxPayConf_pub::JS_API_CALL_URL);
            Header("Location: $url");
        } else {
            //获取code码，以获取openid
            $code = $data['code'];
            $jsApi->setCode($code);
            $openid = $jsApi->getOpenId();
            return $openid;
        }
    }
    static public function getPrepayParamFromServer(array $config) {
        //=========步骤2：使用统一支付接口，获取prepay_id============
        //使用统一支付接口
        $raw = BCWXHttp::request(BCSetting::$serverURL . "/pay/wxmp/prepare", "get", $config, 30);
        $result = json_decode($raw);
        $prepayObject = new stdClass();
        $params = array();

        if ($result != null && $result->resultCode == 0) {
            $prepayObject->result = true;
            foreach($result as $k=>$v) {
                if ( (0 != strcmp("resultCode", $k)) && (0 != strcmp("errMsg", $k)) ) {
                    $params[$k] = $v;
                }
            }
            $prepayObject->params = json_encode($params);
        } else {
            $prepayObject->result = false;
            if ($result == null) {
                $prepayObject->errMsg = $raw;
            } else {
                $prepayObject->errMsg = $result->errMsg;
            }
        }
        return $prepayObject;
    }

}

class BCWXPay  {
    private $openID;
    private $prepayParams;
    private $config = array();
    public function __construct(array $data) {
        $this->openID = BCWXPayUtil::getOpenID($data);
//        $this->openID = "o3kKrjlUsMnv__cK5DYZMl0JoAkY";
    }

    public function getOpenID() {
        return $this->openID;
    }

    final public function configProduct(array $config) {
        $timeStamp = time();
        $out_trade_no = WxPayConf_pub::APPID."$timeStamp";
        $this->config = array();
        foreach($config as $k => $v) {
            $this->config[$k] = $v;
        };
        $this->config["appId"] = BCPayConf::$appId;
        $this->config["appSign"] = md5(BCPayConf::$appId.BCPayConf::$appSecret);
        $this->config["openid"] = $this->openID;
        if (array_key_exists("out_trade_no", $config)) {
            $this->config["out_trade_no"] = $config["out_trade_no"];//商户订单号
        } else {
            $this->config["out_trade_no"] = "$out_trade_no";//商户订单号
        }

//        $this->config["notify_url"] = WxPayConf_pub::NOTIFY_URL;//
        $this->config["trade_type"] = "JSAPI";//交易类型
        return $this->config["out_trade_no"];
    }

    final public function getJsParams() {
        return BCWXPayUtil::getPrepayParamFromServer($this->config);
    }

}

class BCWXQrCode  {
    private $config = array();
    public function __construct() {

    }

    public function getOpenID() {
        return $this->openID;
    }

    final public function configProduct(array $config) {

        $this->config = array();
        foreach($config as $k => $v) {
            $this->config[$k] = $v;
        };
        $this->config["appId"] = BCPayConf::$appId;
        $this->config["appSign"] = md5(BCPayConf::$appId.BCPayConf::$appSecret);
       // $this->config["notify_url"] = WxPayConf_pub::NOTIFY_URL;//

        $this->config["trade_type"] = "NATIVE";//交易类型
        return true;
    }

    final public function getOrderResult($debugFlag) {
        return BCWXPayUtil::getPrepayParamFromServer($this->config, $debugFlag);
    }

}
