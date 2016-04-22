<?php
/**
 * paypal
 */
namespace beecloud;

namespace beecloud\rest;

class international {
    const URI_BILL = "/1/rest/international/bill";
    const URI_REFUND = "/1/rest/international/refund";

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

        if (!isset($data["currency"])) {
            throw new \Exception(NEED_PARAM . "currency");
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
            case "PAYPAL_PAYPAL":
                if (!isset($data["return_url"])) {
                    throw new \Exception(NEED_PARAM . "return_url");
                }
                break;
            case "PAYPAL_CREDITCARD":
                if (!isset($data["credit_card_info"])) {
                    throw new \Exception(NEED_PARAM . "credit_card_info");
                }
                break;
            case "PAYPAL_SAVED_CREDITCARD":
                if (!isset($data["credit_card_id"])) {
                    throw new \Exception(NEED_PARAM . "credit_card_id");
                }
                break;
            default:
                throw new \Exception(NEED_VALID_PARAM . "channel");
                break;
        }

        if (!isset($data["total_fee"])) {
            throw new \Exception(NEED_PARAM . "total_fee");
        } else if(!is_int($data["total_fee"]) || $data["total_fee"] < 1) {
            throw new \Exception(NEED_VALID_PARAM . "total_fee");
        }

        if (!isset($data["bill_no"])) {
            throw new \Exception(NEED_PARAM . "bill_no");
        }

        if (!isset($data["title"])) {
            //TODO: 字节数
            throw new \Exception(NEED_PARAM . "title");
        }

        return self::post(self::URI_BILL, $data, 30, false);
    }
    //TODO:
//    static final public function refund(array $data) {
//        //param validation
//        self::baseParamCheck($data);
//
//        if (isset($data["channel"])) {
//            switch ($data["channel"]) {
//                case "PAYPAL":
//                case "PAYPAL_PAYPAL":
//                case "PAYPAL_CREDITCARD":
//                case "PAYPAL_SAVED_CREDITCARD":
//                    break;
//                default:
//                    throw new \Exception(NEED_VALID_PARAM . "channel");
//                    break;
//            }
//        }
//
//
//        if (!isset($data["refund_no"])) {
//            throw new \Exception(NEED_PARAM . "refund_no");
//        }
//
//        // TODO: refund_no validation
//
//        return self::post(self::URI_REFUND, $data, 30, false);
//    }

}