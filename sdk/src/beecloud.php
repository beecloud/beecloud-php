<?php
/*
 * php version < 5.3
 *
 */

class APIConfig {

    const URI_BILL = '/2/rest/bill'; //支付;支付订单查询(指定id)
    const URI_TEST_BILL = '/2/rest/sandbox/bill';
    const URI_BILLS = '/2/rest/bills'; //订单查询
    const URI_TEST_BILLS = '/2/rest/sandbox/bills';
    const URI_BILLS_COUNT = '/2/rest/bills/count'; //订单总数查询
    const URI_TEST_BILLS_COUNT = '/2/rest/sandbox/bills/count';

    const URI_REFUND = "/2/rest/refund";		//退款;预退款批量审核;退款订单查询(指定id)
    const URI_REFUNDS = "/2/rest/refunds";		//退款查询
    const URI_REFUNDS_COUNT = "/2/rest/refunds/count"; //退款总数查询
    const URI_REFUND_STATUS = "/2/rest/refund/status"; //退款状态更新

    const URI_TRANSFERS = "/2/rest/transfers"; //批量打款 - 支付宝
    const URI_TRANSFER = "/2/rest/transfer";  //单笔打款 - 支付宝/微信
    const URI_BC_TRANSFER_BANKS = '/2/rest/bc_transfer/banks'; //BC企业打款 - 支持银行
    const URI_BC_TRANSFER = "/2/rest/bc_transfer"; //代付 - 银行卡

    const URI_OFFLINE_BILL = '/2/rest/offline/bill'; //线下支付-撤销订单
    const URI_OFFLINE_BILL_STATUS = '/2/rest/offline/bill/status'; //线下订单状态查询
    const URI_OFFLINE_REFUND = '/2/rest/offline/refund'; //线下退款

    const URI_INTERNATIONAL_BILL = "/2/rest/international/bill";
    const URI_INTERNATIONAL_REFUND = "/2/rest/international/refund";

    //subscription
    const URI_SUBSCRIPTION = "/2/subscription";
    const URI_SUBSCRIPTION_PLAN = "/2/plan";
    const URI_SUBSCRIPTION_BANKS = "/2/subscription_banks";

    //发送验证码
    const URI_SMS = "/2/sms";

    //auth
    const URI_AUTH = "/2/auth";


    const UNEXPECTED_RESULT = "非预期的返回结果:";
    const NEED_PARAM = "需要必填字段:";
    const NEED_VALID_PARAM = "字段值不合法:";
    const NEED_WX_JSAPI_OPENID = "微信公众号支付(WX_JSAPI) 需要openid字段";
    const NEED_RETURN_URL = "当channel参数为 ALI_WEB 或 ALI_QRCODE 或 UN_WEB 或JD_WAP 或 JD_WEB时 return_url为必填";
    const NEED_IDENTITY_ID = "当channel参数为 YEE_WAP时 identity_id为必填";
    const BILL_TIMEOUT_ERROR = "当channel参数为 JD* 不支持bill_timeout";
    const NEED_QR_PAY_MODE = '当channel参数为 ALI_QRCODE时 qr_pay_mode为必填';
    const NEED_CARDNO = '当channel参数为 YEE_NOBANKCARD时 cardno为必填';
    const NEED_CARDPWD = '当channel参数为 YEE_NOBANKCARD时 cardpwd为必填';
    const NEED_FRQID = '当channel参数为 YEE_NOBANKCARD时 frqid为必填';
    const NEED_TOTAL_FEE = '当channel参数为 BC_EXPRESS时 total_fee单位分,最小金额100分';
    const VALID_BC_PARAM = 'APP ID,APP Secret参数值均不能为空,请重新设置';
    const VALID_SIGN_PARAM = 'APP ID, timestamp,APP(Master) Secret参数值均不能为空,请设置';
    const VALID_MASTER_SECRET = 'Master Secret参数值不能为空,请设置';
    const VALID_APP_SECRET = 'APP Secret参数值不能为空,请设置';

    const VALID_PARAM_RANGE = '参数 %s 不在限定的范围内, 请重新设置';

    /*
	 * bank_code(int 类型) for channel JD_B2B
		9102    中国工商银行      9107    招商银行
		9103    中国农业银行      9108    光大银行
		9104    交通银行         9109    中国银行
		9105    中国建设银行		9110 	 平安银行
	*/
    static function get_bank_code(){
        return array(9102, 9103, 9104, 9105, 9107, 9108, 9109, 9110);
    }

    /*
	 * bank(string 类型) for channel BC_GATEWAY
	 * CMB	  招商银行    ICBC	工商银行   CCB   建设银行(暂不支持)
	 * BOC	  中国银行    ABC    农业银行   BOCM	交通银行
	 * SPDB   浦发银行    GDB	广发银行   CITIC	中信银行
	 * CEB	  光大银行    CIB	兴业银行   SDB	平安银行
	 * CMBC   民生银行    NBCB   宁波银行   BEA   东亚银行
	 * NJCB   南京银行    SRCB   上海农商行 BOB   北京银行
	*/
    static function get_bank(){
        return array(
            'CMB', 'ICBC', 'CCB', 'BOC', 'ABC', 'BOCM', 'SPDB', 'GDB', 'CITIC',
            'CEB', 'CIB', 'SDB', 'CMBC', 'NBCB', 'BEA', 'NJCB', 'SRCB', 'BOB'
        );
    }

    /*
	 * 结算频率interval(string),
	 * 主要包含任一天，一周，一个月或一年。
	 */
    static function get_interval(){
        return array('day', 'week', 'month', 'year');
    }
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

    static final public function post($api, $data, $timeout) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "post", $data, $timeout);
        $result = json_decode($httpResultStr);
        if (!$result) {
            throw new Exception(APIConfig::UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final public function get($api, $data, $timeout, $returnArray, $type = true) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, $type ? 'get' : 'new_get', $data, $timeout);
        $result = json_decode($httpResultStr,!$returnArray ? false : true);
        if (!$result) {
            throw new Exception(APIConfig::UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final public function put($api, $data, $timeout, $returnArray) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "put", $data, $timeout);
        $result = json_decode($httpResultStr,!$returnArray ? false : true);
        if (!$result) {
            throw new Exception(APIConfig::UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static public function delete($api, $data, $timeout, $returnArray) {
        $url = BCRESTUtil::getApiUrl() . $api;
        $httpResultStr = BCRESTUtil::request($url, "delete", $data, $timeout);
        $result = json_decode($httpResultStr,!$returnArray ? false : true);
        if (!$result) {
            throw new Exception(BCRESTUtil::UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
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
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            //设置 CURLINFO_HEADER_OUT 选项之后 curl_getinfo 函数返回的数组将包含 cURL
            //请求的 header 信息。而要看到回应的 header 信息可以在 curl_setopt 中设置
            //CURLOPT_HEADER 选项为 true
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLINFO_HEADER_OUT, false);

            //fail the request if the HTTP code returned is equal to or larger than 400
            //curl_setopt($ch, CURLOPT_FAILONERROR, true);
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
                case "new_get":
                    curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($data));
                    break;
                case "put":
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                case "delete":
                    curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($data));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
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

class BCRESTApi {

    //BeeCloud main pay params
    public static $app_id;
    public static $app_secret;
    public static $master_secret;
    public static $test_secret;

    //Test Model,只提供下单和支付订单查询的Sandbox模式
    public static $mode = false;

    static function getSandbox(){
        return self::$mode;
    }

    static function setSandbox($flag = false){
        self::$mode = $flag;
    }

    /*
	 * @param $app_id beecloud平台的APP ID
	 * @param $app_secret  beecloud平台的APP SECRET
	 * @param $master_secret  beecloud平台的MASTER SECRET
	 * @param $test_secret  beecloud平台的TEST SECRET
	 */
    static function registerApp($app_id, $app_secret, $master_secret = '', $test_secret = ''){
        if(empty($app_id) || empty($app_secret)){
            throw new Exception(APIConfig::VALID_BC_PARAM);
        }
        self::$app_id = $app_id;
        self::$app_secret = $app_secret;
        self::$master_secret = $master_secret;
        self::$test_secret = $test_secret;
    }

    static function get_sign($app_id, $timestamp, $secret){
        if(empty($app_id) || empty($timestamp) || empty($secret)){
            throw new Exception(APIConfig::VALID_SIGN_PARAM);
        }
        return md5($app_id.$timestamp.$secret);
    }

    /*
	 * 验证必填参数
	 */
    static public function verify_need_params($params, $data){
        if(is_string($params)){
            if(!isset($data[$params]) || empty($data[$params])){
                throw new Exception(APIConfig::NEED_PARAM . $params);
            }
        }else if(is_array($params)){
            foreach ($params as $field) {
                if(!isset($data[$field]) || empty($data[$field])){
                    throw new Exception(APIConfig::NEED_PARAM . $field);
                }
            }
        }
    }

    /*
	 * @desc 获取共同的必填参数app_id, app_sign, timestamp
	 * @param $data array
	 * @param $secret_type string
	 *  0: app_secret
	 * 	1: master_secret
	 *  2: test_secret
	 */
    static function get_common_params($data, $secret_type = '0'){
        $secret = '';
        switch($secret_type){
            case '1':
                $secret = self::$master_secret;
                break;
            case '2':
                $secret = self::$test_secret;
                break;
            case '0':
            default:
                $secret = self::$app_secret;
                break;
        }
        if(empty($secret)){
            throw new Exception(APIConfig::NEED_PARAM. 'APP(Master/Test) Secret, 请检查!');
        }
        $data["app_id"] = self::$app_id;
        $data["app_sign"] = self::get_sign(self::$app_id, $data["timestamp"], $secret);
        self::verify_need_params(array('app_id', 'timestamp', 'app_sign'), $data);
        return $data;
    }


    /*
	  * @desc 发送短信验证码,返回验证码记录的唯一标识,并且手机端接收到验证码,二者供创建subscription使用
	 * @param array $data, 主要包含以下四个参数:
	 *  app_id string APP ID
	 *  timestamp long 时间戳
	 *  app_sign string 签名验证
	 *  phone string 手机号
	 * @return json:
	 * 	result_code string
	 *  result_msg string
	 *  err_detail string
	 *  sms_id string
	 */
    static public function sms($data){
        $data = self::get_common_params($data);
        self::verify_need_params('phone', $data);
        return BCRESTUtil::post(APIConfig::URI_SMS, $data, 30, false);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    static public function bill($data, $method = 'post') {
        $data = self::$mode ? self::get_common_params($data, '2') : self::get_common_params($data, '0');
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
                    //case "BC_EXPRESS":
                    if (!isset($data["return_url"])) {
                        throw new Exception(APIConfig::NEED_RETURN_URL);
                    }
                    break;
            }

            switch ($data["channel"]) {
                case "WX_JSAPI":
                    if (!isset($data["openid"])) {
                        throw new Exception(APIConfig::NEED_WX_JSAPI_OPENID);
                    }
                    break;
                case "ALI_QRCODE":
                    if (!isset($data["qr_pay_mode"])) {
                        throw new Exception(APIConfig::NEED_QR_PAY_MODE);
                    }
                    break;
                case "JD_B2B":
                    if (!isset($data["bank_code"])) {
                        throw new Exception(APIConfig::NEED_PARAM.'bank_code');
                    }
                    if (!in_array($data["bank_code"], APIConfig::get_bank_code())) {
                        throw new Exception(APIConfig::NEED_VALID_PARAM.'bank_code');
                    }
                    break;
                case "YEE_WAP":
                    if (!isset($data["identity_id"])) {
                        throw new Exception(APIConfig::NEED_RETURN_URL);
                    }
                    break;
                case "YEE_NOBANKCARD":
                    if (!isset($data["cardno"])) {
                        throw new Exception(APIConfig::NEED_CARDNO);
                    }
                    if (!isset($data["cardpwd"])) {
                        throw new Exception(APIConfig::NEED_CARDPWD);
                    }
                    if (!isset($data["frqid"])) {
                        throw new Exception(APIConfig::NEED_FRQID);
                    }
                    break;
                case "JD_WEB":
                case "JD_WAP":
                    if (isset($data["bill_timeout"])) {
                        throw new Exception(APIConfig::BILL_TIMEOUT_ERROR);
                    }
                    break;
                case "KUAIQIAN_WAP":
                case "KUAIQIAN_WEB":
//                    if (isset($data["bill_timeout"])) {
//                        throw new Exception(APIConfig::BILL_TIMEOUT_ERROR);
//                    }
//                    break;
                case "BC_GATEWAY":
                    if (!isset($data["bank"])) {
                        throw new Exception(APIConfig::NEED_PARAM.'bank');
                    }
                    if (!in_array($data["bank"], APIConfig::get_bank())) {
                        throw new Exception(APIConfig::NEED_VALID_PARAM.'bank');
                    }
                    break;
                case "BC_EXPRESS" :
                    if ($data["total_fee"] < 100 || !is_int($data["total_fee"])) {
                        throw new Exception(APIConfig::NEED_TOTAL_FEE);
                    }
                    break;
            }
        }
        $url = BCRESTApi::getSandbox() ? APIConfig::URI_TEST_BILL : APIConfig::URI_BILL;
        switch ($method) {
            case 'get'://支付订单查询
                if (!isset($data["id"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "id");
                }
                $order_id = $data["id"];
                unset($data["id"]);
                return BCRESTUtil::get($url.'/'.$order_id, $data, 30, false);
                break;
            case 'post': // 支付
                if (!isset($data["channel"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "channel");
                }
                if (!isset($data["total_fee"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "total_fee");
                } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "total_fee");
                }

                if (!isset($data["bill_no"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "bill_no");
                }
                if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "bill_no");
                }

                if (!isset($data["title"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "title");
                }
                return BCRESTUtil::post($url, $data, 30, false);
                break;
            default :
                exit('No this method');
                break;
        }
    }

    static final public function bills(array $data) {
        $data = self::$mode ? self::get_common_params($data, '2') : self::get_common_params($data, '0');
        self::channelCheck($data);

        $url = BCRESTApi::getSandbox() ? APIConfig::URI_TEST_BILLS : APIConfig::URI_BILLS;
        //param validation
        return BCRESTUtil::get($url, $data, 30, false);
    }


    static final public function bills_count(array $data){
        $data = self::$mode ? self::get_common_params($data, '2') : self::get_common_params($data, '0');
        self::channelCheck($data);

        if (isset($data["bill_no"]) && !preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new Exception(APIConfig::NEED_VALID_PARAM . "bill_no");
        }
        $url = BCRESTApi::getSandbox() ? APIConfig::URI_TEST_BILLS_COUNT : APIConfig::URI_BILLS_COUNT;
        return BCRESTUtil::get($url, $data, 30, false);
    }

    static final public function refund(array $data, $method = 'post') {
        $data = $method == 'get' ? self::get_common_params($data, '0') : self::get_common_params($data, '1');

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
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "channel");
                    break;
            }
        }

        switch ($method){
            case 'put': //预退款批量审核
                if (!isset($data["channel"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "channel");
                }
                if (!isset($data["ids"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "ids");
                }
                if (!is_array($data["ids"])) {
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "ids(array)");
                }
                if (!isset($data["agree"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "agree");
                }
                return BCRESTUtil::put(APIConfig::URI_REFUND, $data, 30, false);
                break;
            case 'get'://退款订单查询
                if (!isset($data["id"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "id");
                }
                $order_id = $data["id"];
                unset($data["id"]);
                return BCRESTUtil::get(APIConfig::URI_REFUND.'/'.$order_id, $data, 30, false);
                break;
            case 'post': //退款
            default :
                if (!isset($data["bill_no"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "bill_no");
                }
                if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "bill_no");
                }

                if (!isset($data["refund_no"])) {
                    throw new Exception(APIConfig::NEED_PARAM . "refund_no");
                }
                if (!preg_match('/^\d{8}[0-9A-Za-z]{3,24}$/', $data["refund_no"]) || preg_match('/^\d{8}0{3}/', $data["refund_no"])) {
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "refund_no");
                }

                if(!is_int($data["refund_fee"]) || 1>$data["refund_fee"]) {
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "refund_fee");
                }
                return BCRESTUtil::post(APIConfig::URI_REFUND, $data, 30, false);
                break;
        }
    }


    static final public function refunds(array $data) {
        $data = self::get_common_params($data, '0');
        self::channelCheck($data);
        //param validation
        return BCRESTUtil::get(APIConfig::URI_REFUNDS, $data, 30, false);
    }

    static final public function refunds_count(array $data) {
        $data = self::get_common_params($data, '0');
        self::channelCheck($data);
        //param validation
        return BCRESTUtil::get(APIConfig::URI_REFUNDS_COUNT, $data, 30, false);
    }

    static final public function refundStatus(array $data) {
        $data = self::get_common_params($data, '0');

        switch ($data["channel"]) {
            case "WX":
            case "YEE":
            case "KUAIQIAN":
            case "BD":
                break;
            default:
                throw new Exception(APIConfig::NEED_VALID_PARAM . "channel");
                break;
        }

        if (!isset($data["refund_no"])) {
            throw new Exception(APIConfig::NEED_PARAM . "refund_no");
        }
        //param validation
        return BCRESTUtil::get(APIConfig::URI_REFUND_STATUS, $data, 30, false);
    }

    //单笔打款 - 支付宝/微信
    static final public function transfer(array $data) {
        $data = self::get_common_params($data, '1');
        switch ($data["channel"]) {
            case "WX_REDPACK":
                if (!isset($data['redpack_info'])) {
                    throw new Exception(APIConfig::NEED_PARAM . 'redpack_info');
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
                        throw new Exception(APIConfig::NEED_PARAM . $v);
                    }
                }
                break;
            default:
                throw new Exception(APIConfig::NEED_VALID_PARAM . "channel = ALI_TRANSFER | WX_TRANSFER | WX_REDPACK");
                break;
        }

        $requiedNames = array("transfer_no",
            "total_fee",
            "desc",
            "channel_user_id"
        );

        foreach($requiedNames as $v) {
            if (!isset($data[$v])) {
                throw new Exception(APIConfig::NEED_PARAM . $v);
            }
        }

        return BCRESTUtil::post(APIConfig::URI_TRANSFER, $data, 30, false);
    }

    //批量打款 - 支付宝
    static final public function transfers(array $data) {
        $data = self::get_common_params($data, '1');
        switch ($data["channel"]) {
            case "ALI":
                break;
            default:
                throw new Exception(APIConfig::NEED_VALID_PARAM . "channel only ALI");
                break;
        }

        if (!isset($data["batch_no"])) {
            throw new Exception(APIConfig::NEED_PARAM . "batch_no");
        }

        if (!isset($data["account_name"])) {
            throw new Exception(APIConfig::NEED_PARAM . "account_name");
        }

        if (!isset($data["transfer_data"])) {
            throw new Exception(APIConfig::NEED_PARAM . "transfer_data");
        }

        if (!is_array($data["transfer_data"])) {
            throw new Exception(APIConfig::NEED_VALID_PARAM . "transfer_data(array)");
        }

        return BCRESTUtil::post(APIConfig::URI_TRANSFERS, $data, 30, false);
    }

    //BC企业打款 - 支持bank
    static final public function bc_transfer_banks($data) {
        if (!isset($data["type"])) {
            throw new Exception(APIConfig::NEED_PARAM . "type");
        }

        if(!in_array($data['type'], array('P_DE', 'P_CR', 'C'))) throw new Exception(APIConfig::NEED_VALID_PARAM . 'type(P_DE, P_CR, C)');

        return BCRESTUtil::get(APIConfig::URI_BC_TRANSFER_BANKS, $data, 30, false);
    }

    //BC企业打款 - 银行卡
    static final public function bc_transfer(array $data) {
        $data = self::get_common_params($data, '1');
        $params = array(
            'total_fee', 'bill_no', 'title', 'trade_source', 'bank_fullname',
            'card_type', 'account_type', 'account_no', 'account_name'
        );
        foreach ($params as $v) {
            if (!isset($data[$v])) {
                throw new Exception(APIConfig::NEED_PARAM . $v);
            }
        }
        if(!in_array($data['card_type'], array('DE', 'CR'))) throw new Exception(APIConfig::NEED_VALID_PARAM . 'card_type(DE, CR)');
        if(!in_array($data['account_type'], array('P', 'C'))) throw new Exception(APIConfig::NEED_VALID_PARAM . 'account_type(P, C)');

        return BCRESTUtil::post(APIConfig::URI_BC_TRANSFER, $data, 30, false);
    }


    static final public function offline_bill(array $data) {
        $data = self::get_common_params($data, '0');
        if (isset($data["channel"])) {
            switch ($data["channel"]) {
                case "WX_SCAN":
                case "ALI_SCAN":
                case "BC_WX_SCAN":
                case "BC_ALI_SCAN":
                    if (!isset($data['method']) && !isset($data['auth_code'])) {
                        throw new Exception(APIConfig::NEED_PARAM . "auth_code");
                    }
                    break;
                case "WX_NATIVE":
                case "ALI_OFFLINE_QRCODE":
                case "SCAN":
                    break;
                default:
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "channel = WX_NATIVE | WX_SCAN | BC_WX_SCAN | ALI_OFFLINE_QRCODE | ALI_SCAN | BC_ALI_SCAN | SCAN | ");
                    break;
            }
        }

        if (!isset($data["bill_no"])) {
            throw new Exception(APIConfig::NEED_PARAM . "bill_no");
        }
        if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new Exception(APIConfig::NEED_VALID_PARAM . "bill_no");
        }

        if (!isset($data['method'])) {
            if (!isset($data["channel"])) {
                throw new Exception(APIConfig::NEED_PARAM . "channel");
            }
            if (!isset($data["total_fee"])) {
                throw new Exception(APIConfig::NEED_PARAM . "total_fee");
            } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
                throw new Exception(APIConfig::NEED_VALID_PARAM . "total_fee");
            }

            if (!isset($data["title"])) {
                throw new Exception(APIConfig::NEED_PARAM . "title");
            }
            return BCRESTUtil::post(APIConfig::URI_OFFLINE_BILL, $data, 30, false);
        }
        $bill_no = $data["bill_no"];
        unset($data["bill_no"]);
        return BCRESTUtil::post(APIConfig::URI_OFFLINE_BILL.'/'.$bill_no, $data, 30, false);
    }

    static final public function offline_bill_status(array $data) {
        $data = self::get_common_params($data, '0');

        if (isset($data["channel"])) {
            switch ($data["channel"]) {
                case "WX_SCAN":
                case "ALI_SCAN":
                case "WX_NATIVE":
                case "ALI_OFFLINE_QRCODE":
                    break;
                default:
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "channel = WX_NATIVE | WX_SCAN | ALI_OFFLINE_QRCODE | ALI_SCAN");
                    break;
            }
        }

        if (!isset($data["bill_no"])) {
            throw new Exception(APIConfig::NEED_PARAM . "bill_no");
        }
        if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new Exception(APIConfig::NEED_VALID_PARAM . "bill_no");
        }
        return BCRESTUtil::post(APIConfig::URI_OFFLINE_BILL_STATUS, $data, 30, false);
    }

    static final public function offline_refund(array $data){
        $data = self::get_common_params($data, '1');
        if (isset($data['channel'])) {
            switch ($data["channel"]) {
                case "ALI":
                case "WX":
                    break;
                default:
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "channel = ALI | WX");
                    break;
            }
        }

        if (!isset($data["refund_fee"])) {
            throw new Exception(APIConfig::NEED_PARAM . "refund_fee");
        } else if(!is_int($data["refund_fee"]) || 1>$data["refund_fee"]) {
            throw new Exception(APIConfig::NEED_VALID_PARAM . "refund_fee");
        }

        if (!isset($data["bill_no"])) {
            throw new Exception(APIConfig::NEED_PARAM . "bill_no");
        }
        if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new Exception(APIConfig::NEED_VALID_PARAM . "bill_no");
        }

        if (!isset($data["refund_no"])) {
            throw new Exception(APIConfig::NEED_PARAM . "refund_no");
        }
        if (!preg_match('/^\d{8}[0-9A-Za-z]{3,24}$/', $data["refund_no"]) || preg_match('/^\d{8}0{3}/', $data["refund_no"])) {
            throw new Exception(APIConfig::NEED_VALID_PARAM . "refund_no");
        }

        return BCRESTUtil::post(APIConfig::URI_OFFLINE_REFUND, $data, 30, false);
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
                case "UN_WAP":
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
                case "BC_APP" :
                case "BC_NATIVE" :
                case "BC_WX_WAP" :
                case "BC_WX_JSAPI" :
                case "BC_WX_SCAN" :
                case "BC_CARD_CHARGE" :
                case "BC_ALI_QRCODE" :
                case "BC_ALI_SCAN" :
                    break;
                default:
                    throw new Exception(APIConfig::NEED_VALID_PARAM . "channel");
                    break;
            }
        }
    }
}


/**
 * paypal pay
 */
class BCRESTInternational extends BCRESTApi{

    static public function bill($data, $method = 'post') {
        $data = parent::get_common_params($data, '0');
        parent::verify_need_params('currency', $data);
        switch ($data["channel"]) {
            case "PAYPAL_PAYPAL":
                parent::verify_need_params('return_url', $data);
                break;
            case "PAYPAL_CREDITCARD":
                self::verify_need_params('credit_card_info', $data);
                break;
            case "PAYPAL_SAVED_CREDITCARD":
                self::verify_need_params('credit_card_id', $data);
                break;
            default:
                throw new Exception(APIConfig::NEED_VALID_PARAM . "channel");
                break;
        }

        self::verify_need_params(array('total_fee', 'bill_no', 'title'), $data);

        if(!is_int($data["total_fee"]) || $data["total_fee"] < 1) {
            throw new Exception(APIConfig::NEED_VALID_PARAM . "total_fee");
        }
        return BCRESTUtil::post(APIConfig::URI_INTERNATIONAL_BILL, $data, 30, false);
    }
}


class Subscriptions extends BCRESTApi{

    /*
	  * @desc 获取支持银行列表
	 * @param array $data, 主要包含以下三个参数:
	 * 	app_id string APP ID
	 * 	timestamp long 时间戳
	 * 	app_sign string 签名验证
	 * @return json:
	 * 	result_code string
	 *  result_msg string
	 *  err_detail string
	 *  banks list
	 *  common_banks list
	 */
    static public function banks($data){
        $data = parent::get_common_params($data);
        return BCRESTUtil::get(APIConfig::URI_SUBSCRIPTION_BANKS, $data, 30, false, false);
    }

    /*
	  * @desc 发送短信验证码,返回验证码记录的唯一标识,并且手机端接收到验证码,二者供创建subscription使用
	 * @param array $data, 主要包含以下四个参数:
	 *  app_id string APP ID
	 *  timestamp long 时间戳
	 *  app_sign string 签名验证
	 *  phone string 手机号
	 * @return json:
	 * 	result_code string
	 *  result_msg string
	 *  err_detail string
	 *  sms_id string
	 */
    static public function sms($data){
        $data = parent::get_common_params($data);
        parent::verify_need_params('phone', $data);
        return BCRESTUtil::post(APIConfig::URI_SMS, $data, 30, false);
    }

    /*
	  * @desc 创建订阅计划plan
	 * @param array $data,主要包含参数:
	 *  fee int 单位分(必填), fee必须不小于 150分, 不大于5000000分
	 *  interval string 结算频率(必填), 主要包含任一天(day)/一周(week)/一个月(month)/一年(year)
	 *  name string 订阅计划的名称(必填)
	 *	currency string, 对照表请参考:https://github.com/beecloud/beecloud-rest-api/tree/master/international
	 *	interval_count 	int 每个订阅结算之间的时间间隔数。默认值1
	 * 		eg: 时间间隔=月，interval_count=3即每3个月。允许一年一次（1年，12个月或52周）的最大值。
	 *	trial_days 	int 指定试用期天数（整数）,默认是0
	 *  optional json格式
	 * @return json
	 */
    static public function plan($data){
        $data = parent::get_common_params($data);
        if(!in_array($data["interval"], APIConfig::get_interval())){
            throw new Exception(sprintf(APIConfig::VALID_PARAM_RANGE, "interval"));
        }
        parent::verify_need_params(array('fee', 'name'), $data);
        if(!is_int($data["fee"])){
            throw new Exception(APIConfig::NEED_VALID_PARAM);
        }
        return BCRESTUtil::post(APIConfig::URI_SUBSCRIPTION_PLAN, $data, 30, false);
    }

    /*
	 * @desc 通过ID查询订阅计划
	 * @param $objectid string 订阅记录的唯一标识(必填)
	 * @param $data array()
	 *  timestamp long 时间戳(必填)
	 *
	 * @desc 按条件查询订阅计划
	 * @param $data array()
	 *  name_with_substring string 按照订阅计划的名称模糊查询
	 *  interval string 结算频率, 主要包含任一天(day)/一周(week)/一个月(month)/一年(year)
	 *	interval_count 	int 每个订阅结算之间的时间间隔数。默认值1
	 * 		eg: 时间间隔=月，interval_count=3即每3个月。允许一年一次（1年，12个月或52周）的最大值。
	 *	trial_days 	int 指定试用期天数（整数）,默认是0
	 *  timestamp long 时间戳(必填)
	 */
    static function query_plan($data, $objectid = ''){
        if(!empty($objectid)){
            $url = APIConfig::URI_SUBSCRIPTION_PLAN.'/'.$objectid;
        }else{
            $url = APIConfig::URI_SUBSCRIPTION_PLAN;
        }
        $data = parent::get_common_params($data);
        return BCRESTUtil::get($url, $data, 30, false, false);
    }

    /*
	 * @desc 更新订阅计划
     * @param $objectid string 订阅记录的唯一标识(必填)
	 * @param $data array()
	 *  timestamp long 时间戳(必填)
	 *
	 *  name string 订阅计划的名称
	 *  optional json
	 */
    static function update_plan($data, $objectid){
        if(empty($objectid)){
            throw new Exception('请设置plan的唯一标识objectid');
        };
        $data = parent::get_common_params($data);
        return BCRESTUtil::put(APIConfig::URI_SUBSCRIPTION_PLAN.'/'.$objectid, $data, 30, false);
    }

    /*
	 * @desc 删除订阅计划
	 * @param $data array()
	 * 	objectid string 订阅计划的唯一标识
	 *  timestamp long 时间戳
	 */
    static function del_plan($data, $objectid){
        if(empty($objectid)){
            throw new Exception('请设置plan的唯一标识objectid');
        };
        $data = parent::get_common_params($data);
        return BCRESTUtil::delete(APIConfig::URI_SUBSCRIPTION_PLAN.'/'.$objectid, $data, 30, false);
    }

    /*
	  * @desc 创建订阅记录subscription
	 * @param array $data, 主要包含参数:
	 *  buyer_id string 订阅的buyer ID(必填)，可以是用户email，也可以是商户系统中的用户ID
	 *  plan_id string  订阅计划的唯一标识(必填)
	 *  card_id string  用于该订阅记录的的card
	 *	bank_name string 订阅用户银行名称（支持列表可参考API获取支持银行列表,即获取方法subscription_banks)
	 *	card_no string 	订阅用户银行卡号
	 *	id_name string 	订阅用户身份证姓名
	 *	id_no 	string 	订阅用户身份证号
	 *	mobile 	string 	订阅用户银行预留手机号
	 *  amount double 	金额用于正在创建的订阅,默认值1.0
	 *  coupon_id string 应用到该订阅的优惠券ID
	 *  trial_end long Unix时间戳表示试用期，客户将被指控的第一次之前拿到的结束。
	 * 		如果设置trial_end将覆盖客户预订了计划的默认试用期。特殊值现在可以提供立即停止客户的试用期。
	 *  optional json
	 * @remark:
	 *  1.card_id 与 {bank_name, card_no, id_name, id_no, mobile} 二者必填其一
	 *  2.card_id 为订阅成功时webhook返回里带有的字段，商户可保存下来下次直接使用
	 *  3.bank_name可参考下述API获取支持银行列表，选择传入
	 * @return json
	 */
    static public function subscription($data){
        $data = parent::get_common_params($data);
        parent::verify_need_params(array('buyer_id', 'plan_id'), $data);
        if(isset($data['card_id']) && !empty($data['card_id'])){

        }else{
            parent::verify_need_params(array('bank_name', 'card_no', 'id_name', 'id_no', 'mobile'), $data);
        }
        return BCRESTUtil::post(APIConfig::URI_SUBSCRIPTION, $data, 30, false);
    }

    /*
	 * @desc 通过ID查询订阅记录
     * @param $objectid string 订阅记录的唯一标识(必填)
	 * @param $data array()
	 *  timestamp long 时间戳(必填)
	 *
	 * @desc 按条件查询订阅
	 * @param $data array()
	 *  buyer_id string 订阅的buyer ID，可以是用户email，也可以是商户系统中的用户ID
	 *  plan_id string  订阅计划的唯一标识(必填)
	 *  card_id string  用于该订阅记录的的card
	 *  timestamp long 时间戳(必填)
	 */
    static function query_subscription($data, $objectid = ''){
        if(!empty($objectid)){
            $url = APIConfig::URI_SUBSCRIPTION.'/'.$objectid;
        }else{
            $url = APIConfig::URI_SUBSCRIPTION;
        }
        $data = parent::get_common_params($data);
        return BCRESTUtil::get($url, $data, 30, false, false);
    }


    /*
	 * @desc 更新订阅
     * @param $objectid string 订阅记录的唯一标识(必填)
	 * @param $data array()
	 *  timestamp long 时间戳(必填)
	 *
	 *  buyer_id string 订阅的buyer ID，可以是用户email，也可以是商户系统中的用户ID
	 *  plan_id string  订阅计划的唯一标识
	 *  card_id string  用于该订阅记录的的card
	 *  amount double 	金额用于正在创建的订阅,默认值1.0
	 *  coupon_id string 应用到该订阅的优惠券ID
	 *  trial_end long Unix时间戳表示试用期，客户将被指控的第一次之前拿到的结束。
	 * 		如果设置trial_end将覆盖客户预订了计划的默认试用期。特殊值现在可以提供立即停止客户的试用期。
	 *  optional json
	 */
    static function update_subscription($data, $objectid){
        if(empty($objectid)){
            throw new Exception('请设置subscription的唯一标识objectid');
        };
        $data = parent::get_common_params($data);
        return BCRESTUtil::put(APIConfig::URI_SUBSCRIPTION.'/'.$objectid, $data, 30, false);
    }

    /*
	 * @desc 取消订阅
     * @param $objectid string 订阅记录的唯一标识
	 * @param $data array()
	 *  timestamp long 时间戳
	 *  at_period_end boolean 默认false,设置为true将推迟预订的取消，直到当前周期结束。
	 */
    static function cancel_subscription($data, $objectid){
		if(empty($objectid)){
			throw new Exception('请设置subscription的唯一标识objectid');
		};
        $data = parent::get_common_params($data);
        return BCRESTUtil::delete(APIConfig::URI_SUBSCRIPTION.'/'.$objectid, $data, 30, false);
    }
}


class Auths extends BCRESTApi{
    /*
	 * @desc 二要素, 三要素, 四要素鉴权, 如果鉴权成功，会自动在全局的card表中创建一条card记录
     * 二要素: (name, id_no)
     * 三要素: (name, id_no, card_no)
     * 四要素: (name, id_no, card_no, mobile)
	 * @param array $data, 主要包含以下四个参数:
	 * 	name string 身份证姓名(必填)
	 *  id_no string 身份证号(必填)
	 *  card_no string 用户银行卡卡号
	 *  mobile string 手机号
	 * @return json
	 *  "card_id": "xxx", 要素认证成功返回card_id(即该记录的唯一标识)
	 *  "auth_result": true, 要素认证是否成功
	 *  "auth_msg": "xxx不匹配", 返回给用户的直接让用户能看懂的鉴权结果消息
	 */
    static public function auth($data){
        $data = parent::get_common_params($data);
        parent::verify_need_params(array('name', 'id_no'), $data);
        return BCRESTUtil::post(APIConfig::URI_AUTH, $data, 30, false);
    }
}
