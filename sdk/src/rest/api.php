<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 9/6/15
 * Time: 15:59
 */
namespace beecloud;

namespace beecloud\rest;

const UNEXPECTED_RESULT = "非预期的返回结果:";
const NEED_PARAM = "需要必填字段:";
const NEED_VALID_PARAM = "字段值不合法:";
const NEED_WX_JSAPI_OPENID = "微信公众号支付(WX_JSAPI) 需要openid字段";
const NEED_RETURN_URL = "当channel参数为 ALI_WEB 或 ALI_QRCODE 或 UN_WEB时 return_url为必填";
const BILL_TIMEOUT_ERROR = "当channel参数为 JD* 或 KUAIQIAN* 不支持bill_timeout";

class api {
    const URI_BILL = "/1/rest/bill";
    const URI_REFUND = "/1/rest/refund";
    const URI_BILLS = "/1/rest/bills";
    const URI_REFUNDS = "/1/rest/refunds";
    const URI_REFUND_STATUS = "/1/rest/refund/status";
    const URI_BILL_STATUS = "/1/rest/bill/";
    const URI_TRANSFERS = "/1/rest/transfers";
    const URI_TRANSFER = "/2/rest/transfer";
    static final private function baseParamCheck(array $data) {
        if (!isset($data["app_id"])) {
            throw new \Exception(NEED_PARAM . "app_id");
        }

        if (!isset($data["timestamp"])) {
            throw new \Exception(NEED_PARAM . "timestamp");
        }

        if (!isset($data["app_sign"])) {
            throw new \Exception(NEED_PARAM . "app_sign");
        }
    }

    static final protected function post($api, $data, $timeout, $returnArray) {
        $url = \beecloud\network::getApiUrl() . $api;
        $httpResultStr = \beecloud\network::request($url, "post", $data, $timeout);
        $result = json_decode($httpResultStr, !$returnArray ? false : true);
        if (!$result) {
            throw new \Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final protected function get($api, $data, $timeout, $returnArray) {
        $url = \beecloud\network::getApiUrl() . $api;
        $httpResultStr = \beecloud\network::request($url, "get", $data, $timeout);
        $result = json_decode($httpResultStr,!$returnArray ? false : true);
        if (!$result) {
            throw new \Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    static final public function bill(array $data) {
        //param validation
        self::baseParamCheck($data);

        switch ($data["channel"]) {
            case "WX_JSAPI":
                if (!isset($data["openid"])) {
                    throw new \Exception(NEED_WX_JSAPI_OPENID);
                }
                break;
            case "ALI_WEB":
            case "ALI_QRCODE":
            case "UN_WEB":
                if (!isset($data["return_url"])) {
                    throw new \Exception(NEED_RETURN_URL);
                }
                break;
            case "JD":
            case "JD_WEB":
            case "JD_WAP":
                if (isset($data["bill_timeout"])) {
                    throw new \Exception(BILL_TIMEOUT_ERROR);
                }
                break;
            case "KUAIQIAN":
            case "KUAIQIAN_WAP":
            case "KUAIQIAN_WEB":
                if (isset($data["bill_timeout"])) {
                    throw new \Exception(BILL_TIMEOUT_ERROR);
                }
                break;
            case "WX_APP":
            case "WX_NATIVE":
            case "ALI_APP":
            case "UN_APP":
            case "ALI_WAP":
            case "ALI_OFFLINE_QRCODE":
            case "YEE":
            case "YEE_WAP":
            case "YEE_WEB":
            case "YEE_NOBANKCARD":
            case "BD":
            case "BD_WAP":
            case "BD_WEB":
                break;
            default:
                throw new \Exception(NEED_VALID_PARAM . "channel");
                break;
        }

        if (!isset($data["total_fee"])) {
            throw new \Exception(NEED_PARAM . "total_fee");
        } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
            throw new \Exception(NEED_VALID_PARAM . "total_fee");
        }

        if (!isset($data["bill_no"])) {
            throw new \Exception(NEED_PARAM . "bill_no");
        } else if (32 < strlen(isset($data["bill_no"]))) {
            throw new \Exception(NEED_VALID_PARAM . "bill_no");
        }

        if (!isset($data["title"])) {
            //TODO: 字节数
            throw new \Exception(NEED_PARAM . "title");
        }

        return self::post(self::URI_BILL, $data, 30, false);
    }

    static final public function refund(array $data) {
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
                    break;
                default:
                    throw new \Exception(NEED_VALID_PARAM . "channel");
                    break;
            }
        }


        if (!isset($data["refund_no"])) {
            throw new \Exception(NEED_PARAM . "refund_no");
        }

        // TODO: refund_no validation

        return self::post(self::URI_REFUND, $data, 30, false);
    }


    static final public function bills(array $data) {
        //required param existence check
        self::baseParamCheck($data);
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
                case "JD":
                case "JD_WEB":
                case "JD_WAP":
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
                    break;
                default:
                    throw new \Exception(NEED_VALID_PARAM . "channel");
                    break;
            }
        }

        //param validation
        return self::get(self::URI_BILLS, $data, 30, false);
    }

    static final public function refunds(array $data) {
        //required param existence check
        self::baseParamCheck($data);
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
                case "JD":
                case "JD_WEB":
                case "JD_WAP":
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
                    break;
                default:
                    throw new \Exception(NEED_VALID_PARAM . "channel");
                    break;
            }
        }
        //param validation
        return self::get(self::URI_REFUNDS, $data, 30, false);
    }

    static final public function refundStatus(array $data) {
        //required param existence check
        self::baseParamCheck($data);

        switch ($data["channel"]) {
            case "WX":
                break;
            default:
                throw new \Exception(NEED_VALID_PARAM . "channel");
                break;
        }

        if (!isset($data["refund_no"])) {
            throw new \Exception(NEED_PARAM . "refund_no");
        }
        //param validation
        return self::get(self::URI_REFUND_STATUS, $data, 30, false);
    }
    static final public function transfer(array $data) {
        self::baseParamCheck($data);
        switch ($data["channel"]) {
            case "WX_REDPACK":
                $wxReruieNames = array(
                    "redpack_info"
                );
                foreach($wxReruieNames as $v) {
                    if (!isset($data[$v])) {
                        throw new \Exception(NEED_PARAM . $v);
                    }
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
                        throw new \Exception(NEED_PARAM . $v);
                    }
                }
                break;
            default:
                throw new \Exception(NEED_VALID_PARAM . "channel = ALI_TRANSFER | WX_TRANSFER | WX_REDPACK");
                break;
        }

        $requiedNames = array("transfer_no",
            "total_fee",
            "desc",
            "channel_user_id"
            );

        foreach($requiedNames as $v) {
            if (!isset($data[$v])) {
                throw new \Exception(NEED_PARAM . $v);
            }
        }

        return self::post(self::URI_TRANSFER, $data, 30, false);
    }

    static final public function transfers(array $data) {
        self::baseParamCheck($data);
        switch ($data["channel"]) {
            case "ALI":
                break;
            default:
                throw new \Exception(NEED_VALID_PARAM . "channel only ALI");
                break;
        }


        if (!isset($data["batch_no"])) {
            throw new \Exception(NEED_PARAM . "batch_no");
        }

        if (!isset($data["account_name"])) {
            throw new \Exception(NEED_PARAM . "account_name");
        }

        if (!isset($data["transfer_data"])) {
            throw new \Exception(NEED_PARAM . "transfer_data");
        }

        if (!is_array($data["transfer_data"])) {
            throw new \Exception(NEED_VALID_PARAM . "transfer_data(array)");
        }

        return self::post(self::URI_TRANSFERS, $data, 30, false);
    }
}