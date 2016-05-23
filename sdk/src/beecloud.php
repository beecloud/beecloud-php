<?php
/*
 * php version < 5.3
 *
 */

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
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

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
                case "put":
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                default:
                    throw new Exception('不支持的HTTP方式');
                    break;
            }

            $result = curl_exec($ch);
            if (curl_errno($ch) > 0) {
                throw new Exception(curl_error($ch));
            }
            curl_close($ch);
            return $result;
        } catch (Exception $e) {
            return "CURL EXCEPTION: ".$e->getMessage();
        }
    }
}

/**
 * paypal pay
 */
class BCRESTInternational {
    const URI_BILL = "/1/rest/international/bill";
    const URI_REFUND = "/1/rest/international/refund";

    static final private function baseParamCheck(array $data) {
        if (!isset($data["app_id"])) {
            throw new Exception(NEED_PARAM . "app_id");
        }

        if (!isset($data["timestamp"])) {
            throw new Exception(NEED_PARAM . "timestamp");
        }

        if (!isset($data["app_sign"])) {
            throw new Exception(NEED_PARAM . "app_sign");
        }

        if (!isset($data["currency"])) {
            throw new Exception(NEED_PARAM . "currency");
        }
    }

    static final protected function post($api, $data, $timeout) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "post", $data, $timeout);
        $result = json_decode($httpResultStr);
        if (!$result) {
            throw new Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final protected function get($api, $data, $timeout) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "get", $data, $timeout);
        $result = json_decode($httpResultStr);
        if (!$result) {
            throw new Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final protected function put($api, $data, $timeout, $returnArray) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "put", $data, $timeout);
        $result = json_decode($httpResultStr,!$returnArray ? false : true);
        if (!$result) {
            throw new Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final public function bill(array $data) {
        //param validation
        self::baseParamCheck($data);

        switch ($data["channel"]) {
            case "PAYPAL_PAYPAL":
                if (!isset($data["return_url"])) {
                    throw new Exception(NEED_PARAM . "return_url");
                }
                break;
            case "PAYPAL_CREDITCARD":
                if (!isset($data["credit_card_info"])) {
                    throw new Exception(NEED_PARAM . "credit_card_info");
                }
                break;
            case "PAYPAL_SAVED_CREDITCARD":
                if (!isset($data["credit_card_id"])) {
                    throw new Exception(NEED_PARAM . "credit_card_id");
                }
                break;
            default:
                throw new Exception(NEED_VALID_PARAM . "channel");
                break;
        }

        if (!isset($data["total_fee"])) {
            throw new Exception(NEED_PARAM . "total_fee");
        } else if(!is_int($data["total_fee"]) || $data["total_fee"] < 1) {
            throw new Exception(NEED_VALID_PARAM . "total_fee");
        }

        if (!isset($data["bill_no"])) {
            throw new Exception(NEED_PARAM . "bill_no");
        }

        if (!isset($data["title"])) {
            throw new Exception(NEED_PARAM . "title");
        }

        return self::post(self::URI_BILL, $data, 30, false);
    }
}

class BCRESTApi {
    const URI_BILL = "/2/rest/bill";  			//支付
    const URI_REFUND = "/2/rest/refund";		//退款 预退款批量审核 退款订单查询(制定id)
    const URI_BILLS = "/2/rest/bills";			//订单查询
    const URI_BILLS_COUNT = "/2/rest/bills/count";		//订单总数查询
    const URI_REFUNDS = "/2/rest/refunds";			//退款查询
    const URI_REFUNDS_COUNT = "/2/rest/refunds/count"; //退款总数查询
    const URI_REFUND_STATUS = "/2/rest/refund/status"; //退款状态更新

    const URI_TRANSFERS = "/2/rest/transfers"; //批量打款 - 支付宝
    const URI_TRANSFER = "/2/rest/transfer";  //单笔打款 - 支付宝/微信
    const URI_BC_TRANSFER = "/2/rest/bc_transfer"; //代付 - 银行卡

    const URI_OFFLINE_BILL = '/2/rest/offline/bill'; //线下支付-撤销订单
    const URI_OFFLINE_BILL_STATUS = '/2/rest/offline/bill/status'; //线下订单状态查询
    const URI_OFFLINE_REFUND = '/2/rest/offline/refund'; //线下退款

    static final private function baseParamCheck(array $data) {
        if (!isset($data["app_id"])) {
            throw new Exception(NEED_PARAM . "app_id");
        }

        if (!isset($data["timestamp"])) {
            throw new Exception(NEED_PARAM . "timestamp");
        }

        if (!isset($data["app_sign"])) {
            throw new Exception(NEED_PARAM . "app_sign");
        }
    }

    static final protected function post($api, $data, $timeout) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "post", $data, $timeout);
        $result = json_decode($httpResultStr);
        if (!$result) {
            throw new Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final protected function get($api, $data, $timeout) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "get", $data, $timeout);
        $result = json_decode($httpResultStr);
        if (!$result) {
            throw new Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final protected function put($api, $data, $timeout, $returnArray) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "put", $data, $timeout);
        $result = json_decode($httpResultStr,!$returnArray ? false : true);
        if (!$result) {
            throw new Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    static final public function bill(array $data, $method = 'post') {
        //param validation
        self::baseParamCheck($data);
        self::channelCheck($data);
        if (isset($data["channel"])) {
            switch($data["channel"]){
                case 'ALI_WEB':
                case 'ALI_QRCODE':
                case 'UN_WEB':
                case 'JD_WAP':
                case 'JD_WEB':
                case 'JD_B2B':
                case "BC_GATEWAY":
                case "BC_EXPRESS":
                    if (!isset($data["return_url"])) {
                        throw new Exception(NEED_RETURN_URL);
                    }
                    break;
            }

            switch ($data["channel"]) {
                case "WX_JSAPI":
                    if (!isset($data["openid"])) {
                        throw new Exception(NEED_WX_JSAPI_OPENID);
                    }
                    break;
                case "ALI_QRCODE":
                    if (!isset($data["qr_pay_mode"])) {
                        throw new Exception(NEED_QR_PAY_MODE);
                    }
                    break;
                case "JD_B2B":
                    if (!isset($data["bank_code"])) {
                        throw new Exception(NEED_PARAM.'bank_code');
                    }
                    if (!in_array($data["bank_code"], unserialize(BANK_CODE))) {
                        throw new Exception(NEED_VALID_PARAM.'bank_code');
                    }
                    break;
                case "YEE_WAP":
                    if (!isset($data["identity_id"])) {
                        throw new Exception(NEED_IDENTITY_ID);
                    }
                    break;
                case "YEE_NOBANKCARD":
                    if (!isset($data["cardno"])) {
                        throw new Exception(NEED_CARDNO);
                    }
                    if (!isset($data["cardpwd"])) {
                        throw new Exception(NEED_CARDPWD);
                    }
                    if (!isset($data["frqid"])) {
                        throw new Exception(NEED_FRQID);
                    }
                    break;
                case "JD_WEB":
                case "JD_WAP":
                    if (isset($data["bill_timeout"])) {
                        throw new Exception(BILL_TIMEOUT_ERROR);
                    }
                    break;
                case "KUAIQIAN_WAP":
                case "KUAIQIAN_WEB":
//                    if (isset($data["bill_timeout"])) {
//                        throw new Exception(BILL_TIMEOUT_ERROR);
//                    }
//                    break;
                case "BC_GATEWAY":
                    if (!isset($data["bank"])) {
                        throw new Exception(NEED_PARAM.'bank');
                    }
                    if (!in_array($data["bank"], unserialize(BANK))) {
                        throw new Exception(NEED_VALID_PARAM.'bank');
                    }
                    break;
                case "BC_EXPRESS" :
                    if ($data["total_fee"] < 100 || !is_int($data["total_fee"])) {
                        throw new Exception(NEED_TOTAL_FEE);
                    }
                    break;
            }
        }

        switch ($method) {
            case 'get'://支付订单查询
                if (!isset($data["id"])) {
                    throw new Exception(NEED_PARAM . "id");
                }
                $order_id = $data["id"];
                unset($data["id"]);
                return self::get(self::URI_BILL.'/'.$order_id, $data, 30, false);
                break;
            case 'post': // 支付
            default:
                if (!isset($data["channel"])) {
                    throw new Exception(NEED_PARAM . "channel");
                }
                if (!isset($data["total_fee"])) {
                    throw new Exception(NEED_PARAM . "total_fee");
                } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
                    throw new Exception(NEED_VALID_PARAM . "total_fee");
                }

                if (!isset($data["bill_no"])) {
                    throw new Exception(NEED_PARAM . "bill_no");
                }
                if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
                    throw new Exception(NEED_VALID_PARAM . "bill_no");
                }

                if (!isset($data["title"])) {
                    //TODO: 字节数
                    throw new Exception(NEED_PARAM . "title");
                }
                return self::post(self::URI_BILL, $data, 30, false);
                break;
        }
    }

    static final public function bills(array $data) {
        //required param existence check
        self::baseParamCheck($data);
        self::channelCheck($data);

        //param validation
        return self::get(self::URI_BILLS, $data, 30, false);
    }


    static final public function bills_count(array $data){
        self::baseParamCheck($data);
        self::channelCheck($data);

        if (isset($data["bill_no"]) && !preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new Exception(NEED_VALID_PARAM . "bill_no");
        }
        return self::get(self::URI_BILLS_COUNT, $data, 30, false);
    }

    static final public function refund(array $data, $method = 'post') {
        //param validation
        self::baseParamCheck($data);

        if (isset($data["channel"])) {
            switch ($data["channel"]) {
                case "ALI":
                case "UN":
                case "WX":
                case "JD":
                case "KUAIQIAN":
                case "YEE":
                case "BD":
                case "BC":
                    break;
                default:
                    throw new Exception(NEED_VALID_PARAM . "channel");
                    break;
            }
        }

        switch ($method){
            case 'put': //预退款批量审核
                if (!isset($data["channel"])) {
                    throw new Exception(NEED_PARAM . "channel");
                }
                if (!isset($data["ids"])) {
                    throw new Exception(NEED_PARAM . "ids");
                }
                if (!is_array($data["ids"])) {
                    throw new Exception(NEED_VALID_PARAM . "ids(array)");
                }
                if (!isset($data["agree"])) {
                    throw new Exception(NEED_PARAM . "agree");
                }
                return self::put(self::URI_REFUND, $data, 30, false);
                break;
            case 'get'://退款订单查询
                if (!isset($data["id"])) {
                    throw new Exception(NEED_PARAM . "id");
                }
                $order_id = $data["id"];
                unset($data["id"]);
                return self::get(self::URI_REFUND.'/'.$order_id, $data, 30, false);
                break;
            case 'post': //退款
            default :
                if (!isset($data["bill_no"])) {
                    throw new Exception(NEED_PARAM . "bill_no");
                }
                if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
                    throw new Exception(NEED_VALID_PARAM . "bill_no");
                }

                if (!isset($data["refund_no"])) {
                    throw new Exception(NEED_PARAM . "refund_no");
                }
                if (!preg_match('/^\d{8}[0-9A-Za-z]{3,24}$/', $data["refund_no"]) || preg_match('/^\d{8}0{3}/', $data["refund_no"])) {
                    throw new Exception(NEED_VALID_PARAM . "refund_no");
                }

                if(!is_int($data["refund_fee"]) || 1>$data["refund_fee"]) {
                    throw new Exception(NEED_VALID_PARAM . "refund_fee");
                }
                return self::post(self::URI_REFUND, $data, 30, false);
                break;
        }
    }


    static final public function refunds(array $data) {
        //required param existence check
        self::baseParamCheck($data);
        self::channelCheck($data);
        //param validation
        return self::get(self::URI_REFUNDS, $data, 30, false);
    }

    static final public function refunds_count(array $data) {
        //required param existence check
        self::baseParamCheck($data);
        self::channelCheck($data);
        //param validation
        return self::get(self::URI_REFUNDS_COUNT, $data, 30, false);
    }

    static final public function refundStatus(array $data) {
        //required param existence check
        self::baseParamCheck($data);

        switch ($data["channel"]) {
            case "WX":
            case "YEE":
            case "KUAIQIAN":
            case "BD":
                break;
            default:
                throw new Exception(NEED_VALID_PARAM . "channel");
                break;
        }

        if (!isset($data["refund_no"])) {
            throw new Exception(NEED_PARAM . "refund_no");
        }
        //param validation
        return self::get(self::URI_REFUND_STATUS, $data, 30, false);
    }

    //单笔打款 - 支付宝/微信
    static final public function transfer(array $data) {
        self::baseParamCheck($data);
        switch ($data["channel"]) {
            case "WX_REDPACK":
                if (!isset($data['redpack_info'])) {
                    throw new Exception(NEED_PARAM . 'redpack_info');
                }
                break;
            case "WX_TRANSFER":
                break;
            case "ALI_TRANSFER":
                $aliRequireNames = array(
                    "channel_user_name",
                    "account_name"
                );

                foreach($aliRequireNames as $v) {
                    if (!isset($data[$v])) {
                        throw new Exception(NEED_PARAM . $v);
                    }
                }
                break;
            default:
                throw new Exception(NEED_VALID_PARAM . "channel = ALI_TRANSFER | WX_TRANSFER | WX_REDPACK");
                break;
        }

        $requiedNames = array("transfer_no",
            "total_fee",
            "desc",
            "channel_user_id"
        );

        foreach($requiedNames as $v) {
            if (!isset($data[$v])) {
                throw new Exception(NEED_PARAM . $v);
            }
        }

        return self::post(self::URI_TRANSFER, $data, 30, false);
    }

    //批量打款 - 支付宝
    static final public function transfers(array $data) {
        self::baseParamCheck($data);
        switch ($data["channel"]) {
            case "ALI":
                break;
            default:
                throw new Exception(NEED_VALID_PARAM . "channel only ALI");
                break;
        }

        if (!isset($data["batch_no"])) {
            throw new Exception(NEED_PARAM . "batch_no");
        }

        if (!isset($data["account_name"])) {
            throw new Exception(NEED_PARAM . "account_name");
        }

        if (!isset($data["transfer_data"])) {
            throw new Exception(NEED_PARAM . "transfer_data");
        }

        if (!is_array($data["transfer_data"])) {
            throw new Exception(NEED_VALID_PARAM . "transfer_data(array)");
        }

        return self::post(self::URI_TRANSFERS, $data, 30, false);
    }

    //打款 - 银行卡
    static final public function bc_transfer(array $data) {
        self::baseParamCheck($data);
        $params = array(
            'total_fee', 'bill_no', 'title', 'trade_source', 'bank_code', 'bank_associated_code', 'bank_fullname',
            'card_type', 'account_type', 'account_no', 'account_name'
        );

        foreach ($params as $v) {
            if (!isset($data[$v])) {
                throw new Exception(NEED_PARAM . $v);
            }
            if(empty($data[$v])){
                if($v == 'bank_associated_code' && $data['total_fee'] < 5000000){
                    continue;
                }
                throw new Exception($v.FIELD_VALUE_EMPTY);
            }
        }
        if(!in_array($data['card_type'], array('DE', 'CR'))) throw new Exception(NEED_VALID_PARAM . 'card_type(DE, CR)');
        if(!in_array($data['account_type'], array('P', 'C'))) throw new Exception(NEED_VALID_PARAM . 'account_type(P, C)');

        return self::post(self::URI_BC_TRANSFER, $data, 30, false);
    }


    static final public function offline_bill(array $data) {
        self::baseParamCheck($data);
        if (isset($data["channel"])) {
            switch ($data["channel"]) {
                case "WX_SCAN":
                case "ALI_SCAN":
                    if (!isset($data['method']) && !isset($data['auth_code'])) {
                        throw new Exception(NEED_PARAM . "auth_code");
                    }
                    break;
                case "WX_NATIVE":
                case "ALI_OFFLINE_QRCODE":
                case "SCAN":
                    break;
                default:
                    throw new Exception(NEED_VALID_PARAM . "channel = WX_NATIVE | WX_SCAN | ALI_OFFLINE_QRCODE | ALI_SCAN | SCAN");
                    break;
            }
        }

        if (!isset($data["bill_no"])) {
            throw new Exception(NEED_PARAM . "bill_no");
        }
        if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new Exception(NEED_VALID_PARAM . "bill_no");
        }

        if (!isset($data['method'])) {
            if (!isset($data["channel"])) {
                throw new Exception(NEED_PARAM . "channel");
            }
            if (!isset($data["total_fee"])) {
                throw new Exception(NEED_PARAM . "total_fee");
            } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
                throw new Exception(NEED_VALID_PARAM . "total_fee");
            }

            if (!isset($data["title"])) {
                throw new Exception(NEED_PARAM . "title");
            }
            return self::post(self::URI_OFFLINE_BILL, $data, 30, false);
        }
        $bill_no = $data["bill_no"];
        unset($data["bill_no"]);
        return self::post(self::URI_OFFLINE_BILL.'/'.$bill_no, $data, 30, false);
    }

    static final public function offline_bill_status(array $data) {
        self::baseParamCheck($data);

        if (isset($data["channel"])) {
            switch ($data["channel"]) {
                case "WX_SCAN":
                case "ALI_SCAN":
                case "WX_NATIVE":
                case "ALI_OFFLINE_QRCODE":
                    break;
                default:
                    throw new Exception(NEED_VALID_PARAM . "channel = WX_NATIVE | WX_SCAN | ALI_OFFLINE_QRCODE | ALI_SCAN");
                    break;
            }
        }

        if (!isset($data["bill_no"])) {
            throw new Exception(NEED_PARAM . "bill_no");
        }
        if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new Exception(NEED_VALID_PARAM . "bill_no");
        }
        return self::post(self::URI_OFFLINE_BILL_STATUS, $data, 30, false);
    }

    static final private function offline_refund(array $data){
        self::baseParamCheck($data);
        if (isset($data['channel'])) {
            switch ($data["channel"]) {
                case "ALI":
                case "WX":
                    break;
                default:
                    throw new Exception(NEED_VALID_PARAM . "channel = ALI | WX");
                    break;
            }
        }

        if (!isset($data["refund_fee"])) {
            throw new Exception(NEED_PARAM . "refund_fee");
        } else if(!is_int($data["refund_fee"]) || 1>$data["refund_fee"]) {
            throw new Exception(NEED_VALID_PARAM . "refund_fee");
        }

        if (!isset($data["bill_no"])) {
            throw new Exception(NEED_PARAM . "bill_no");
        }
        if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new Exception(NEED_VALID_PARAM . "bill_no");
        }

        if (!isset($data["refund_no"])) {
            throw new Exception(NEED_PARAM . "refund_no");
        }
        if (!preg_match('/^\d{8}[0-9A-Za-z]{3,24}$/', $data["refund_no"]) || preg_match('/^\d{8}0{3}/', $data["refund_no"])) {
            throw new Exception(NEED_VALID_PARAM . "refund_no");
        }

        return self::post(self::URI_OFFLINE_BILL, $data, 30, false);
    }


    static final private function channelCheck($data){
        if (isset($data["channel"])) {
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
                case "WX_JSAPI":
                case "WX_NATIVE":
                case "WX_APP":
                case "JD":
                case "JD_WEB":
                case "JD_WAP":
                case "JD_B2B":
                case "YEE":
                case "YEE_WAP":
                case "YEE_WEB":
                case "YEE_NOBANKCARD":
                case "KUAIQIAN":
                case "KUAIQIAN_WAP":
                case "KUAIQIAN_WEB":
                case "BD":
                case "BD_WAP":
                case "BD_WEB":
                case "PAYPAL":
                case "PAYPAL_SANDBOX":
                case "PAYPAL_LIVE":
                case "BC" :
                case "BC_GATEWAY" :
                case "BC_EXPRESS" :
                    break;
                default:
                    throw new Exception(NEED_VALID_PARAM . "channel");
                    break;
            }
        }
    }
}