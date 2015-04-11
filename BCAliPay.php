<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 4/3/15
 * Time: 11:09
 */
$BeeCloudSdkPath = dirname(__FILE__);
set_include_path(get_include_path().PATH_SEPARATOR.$BeeCloudSdkPath);

include_once("config/BCPayConfig.php");

class BCAliHttp {

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
class BCAliPayUtil {
    static public function getQrCodeFromServer(array $config) {
        //=========步骤2：使用统一支付接口，获取prepay_id============
        //使用统一支付接口
        $raw = BCAliHttp::request(BCSetting::$serverURL . "/pay/ali/qrsign", "get", $config, 30);
        $result = json_decode($raw);
        if ($result == null || $result->resultCode != 0) {
            return false;
        }

        $params = array("qrcode" => $result->qrcode,
                        "qrurl" => $result->qr_img_url);
        return $params;
    }
    static public function getImmediateHtmlFromRemote(array $config) {
        $raw = BCAliHttp::request(BCSetting::$serverURL . "/pay/ali/websign", "get", $config, 30);
        $result = json_decode($raw);

        if ($result == null || $result->resultCode != 0) {
            return false;
        }


        return $result->sbHtml;
    }
}

class BCAliQrCode {
    private $config = array();
    public function __construct() {

    }

    final public function configProduct(array $config) {
        $this->config = array();
        foreach($config as $k => $v) {
            $this->config[$k] = $v;
        };
        $this->config["appId"] = BCPayConf::$appId;
        $this->config["appSign"] = md5(BCPayConf::$appId.BCPayConf::$appSecret);
        return true;
    }

    final public function getQrCode() {
        return BCAliPayUtil::getQrCodeFromServer($this->config);
    }
}

class BCAliImmediate {
    private $config = array();
    public function __construct() {

    }

    final public function configProduct(array $config) {
        $this->config = array();
        foreach($config as $k => $v) {
            $this->config[$k] = $v;
        };
        $this->config["appId"] = BCPayConf::$appId;
        $this->config["appSign"] = md5(BCPayConf::$appId.BCPayConf::$appSecret);
        return true;
    }

    final public function getImmediateHtml() {
        return BCAliPayUtil::getImmediateHtmlFromRemote($this->config);
    }
}