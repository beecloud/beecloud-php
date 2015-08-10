<?php
class BCRESTErrMsg {
    const UNEXPECTED_RESULT = "非预期的返回结果:";
    const NEED_PARAM = "需要必填字段:";
    const NEED_VALID_PARAM = "字段值不合法:";
    const NEED_WX_JSAPI_OPENID = "微信公众号支付(WX_JSAPI) 需要openid字段";
    const NEED_RETURN_URL = "当channel参数为 ALI_WEB 或 ALI_QRCODE 或 UN_WEB时 return_url为必填";
}

class BCRESTUtil {
    static final public function getApiUrl() {
        $domainList = array("apibj.beecloud.cn", "apisz.beecloud.cn", "apiqd.beecloud.cn", "apihz.beecloud.cn");
        //apibj.beecloud.cn	北京
        //apisz.beecloud.cn	深圳
        //apiqd.beecloud.cn	青岛
        //apihz.beecloud.cn	杭州

        $random = rand(0, 3);
        return "https://" . $domainList[$random];
    }

    static final public function request($url, $method, array $data, $timeout) {
        try {
            $timeout = (isset($timeout) && is_int($timeout)) ? $timeout : 20;
            $ch = curl_init();
            /*支持SSL 不验证CA根验证*/
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            /*重定向跟随*/
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
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
            return "CURL EXCEPTION:".$e->getMessage();
        }
    }
}

class BCRESTApi {
    const URI_BILL = "/1/rest/bill";
    const URI_REFUND = "/1/rest/refund";
    const URI_BILLS = "/1/rest/bills";
    const URI_REFUNDS = "/1/rest/refunds";
    const URI_REFUND_STATUS = "/1/rest/refund/status";
    const URI_BILL_STATUS = "/1/rest/bill/";
    const URI_TRANSFERS = "/1/rest/transfers";

    static final private function baseParamCheck(array $data) {
        if (!isset($data["app_id"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "app_id");
        }

        if (!isset($data["timestamp"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "timestamp");
        }

        if (!isset($data["app_sign"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "app_sign");
        }

        switch ($data["channel"]) {
            case "ALI":
            case "ALI_WEB":
            case "ALI_WAP":
            case "ALI_QRCODE":
            case "ALI_APP":
            case "ALI_OFFLINE_QRCODE":
            case "UN":
            case "UN_WEB":
            case "UN_APP":
            case "WX":
            case "WX_APP":
            case "WX_JSAPI":
            case "WX_NATIVE":
                break;
            default:
                throw new Exception(BCRESTErrMsg::NEED_VALID_PARAM . "channel");
                break;
        }
    }

    static final protected function post($api, $data, $timeout) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "post", $data, $timeout);
        $result = json_decode($httpResultStr);
        if (!$result) {
            throw new Exception(BCRESTErrMsg::UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final protected function get($api, $data, $timeout) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "get", $data, $timeout);
        $result = json_decode($httpResultStr);
        if (!$result) {
            throw new Exception(BCRESTErrMsg::UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    static final public function bill(array $data) {
        //param validation
        self::baseParamCheck($data);

        switch ($data["channel"]) {
            case "WX_JSAPI":
                if (!isset($data["openid"])) {
                    throw new Exception(BCRESTErrMsg::NEED_WX_JSAPI_OPENID);
                }
                break;
            case "ALI_WEB":
            case "ALI_QRCODE":
            case "UN_WEB":
                if (!isset($data["return_url"])) {
                    throw new Exception(BCRESTErrMsg::NEED_RETURN_URL);
                }
                break;
            case "WX_APP":
            case "WX_NATIVE":
            case "ALI_APP":
            case "UN_APP":
            case "ALI_WAP":
            case "ALI_OFFLINE_QRCODE":
                break;
            default:
                throw new Exception(BCRESTErrMsg::NEED_VALID_PARAM . "channel");
                break;
        }

        if (!isset($data["total_fee"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "total_fee");
        } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
            throw new Exception(BCRESTErrMsg::NEED_VALID_PARAM . "total_fee");
        }

        if (!isset($data["bill_no"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "bill_no");
        } else if (32 < strlen(isset($data["bill_no"]))) {
            throw new Exception(BCRESTErrMsg::NEED_VALID_PARAM . "bill_no");
        }

        if (!isset($data["title"])) {
            //TODO: 字节数
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "title");
        }

        return self::post(self::URI_BILL, $data, 30);
    }

    static final public function refund(array $data) {
        //param validation
        self::baseParamCheck($data);

        if (!isset($data["refund_no"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "refund_no");
        }

        // TODO: refund_no validation

        return self::post(self::URI_REFUND, $data, 30);
    }


    static final public function bills(array $data) {
        //required param existence check
        self::baseParamCheck($data);
        //param validation
        return self::get(self::URI_BILLS, $data, 30);
    }

    static final public function refunds(array $data) {
        //required param existence check
        self::baseParamCheck($data);
        //param validation
       return self::get(self::URI_REFUNDS, $data, 30);
    }

    static final public function refundStatus(array $data) {
        //required param existence check
        self::baseParamCheck($data);

        if (!isset($data["refund_no"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "refund_no");
        }
        //param validation
        return self::get(self::URI_REFUND_STATUS, $data, 30);
    }

    static final public function transfers(array $data) {
        self::baseParamCheck($data);
        switch ($data["channel"]) {
            case "ALI":
                break;
            default:
                throw new Exception(BCRESTErrMsg::NEED_VALID_PARAM . "channel only ALI");
                break;
        }


        if (!isset($data["batch_no"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "batch_no");
        }

        if (!isset($data["account_name"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "account_name");
        }

        if (!isset($data["transfer_data"])) {
            throw new Exception(BCRESTErrMsg::NEED_PARAM . "transfer_data");
        }

        if (!is_array($data["transfer_data"])) {
            throw new Exception(BCRESTErrMsg::NEED_VALID_PARAM . "transfer_data(array)");
        }

        return self::post(self::URI_TRANSFERS, $data, 30);
    }

//    static final public function billStatus(array $data) {
//        //required param existence check
//        self::baseParamCheck($data);
//        switch ($data["channel"]) {
//            case "ALI_OFFLINE_QRCODE":
//                break;
//            default:
//                throw new Exception(BCRESTErrMsg::NEED_VALID_PARAM . "channel only ALI_OFFLINE_QRCODE");
//                break;
//        }
//
//        if (!isset($data["bill_no"])) {
//            throw new Exception(BCRESTErrMsg::NEED_PARAM . "bill_no");
//        }
//
//        if (!isset($data["method"])) {
//            throw new Exception(BCRESTErrMsg::NEED_PARAM . "method");
//        }
//        switch($data["method"]) {
//            case "UPDATE":
//            case "REVERT":
//                break;
//            default:
//                throw new Exception(BCRESTErrMsg::NEED_VALID_PARAM . "method only UPDATE|REVERT");
//                break;
//        }
//        //param validation
//        return self::post(self::URI_BILL_STATUS . $data["bill_no"], $data, 30);
//    }

}