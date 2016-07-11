<?php
/*
 * php version >= 5.3
 *
 */
namespace beecloud\rest;

class api {

	//BeeCloud main pay params
	public static $app_id;
	public static $app_secret;
	public static $master_secret;
	public static $test_secret;

	//Test Model,只提供下单和支付订单查询的Sandbox模式
	public static $mode = false;

	static function getMode(){
		return self::$mode;
	}

	static function setMode($flag = false){
		self::$mode = $flag;
	}

	/*
	 * @param $app_id beecloud平台的APP ID
	 * @param $app_secret  beecloud平台的APP SECRET
	 * @param $master_secret  beecloud平台的MASTER SECRET
	 * @param $test_secret  beecloud平台的TEST SECRET
	 */
	static function registerApp($app_id, $app_secret, $master_secret, $test_secret = ''){
		if(empty($app_id) || empty($app_secret) || empty($master_secret)){
			throw new \Exception(\beecloud\rest\config::VALID_BC_PARAM);
		}
		self::$app_id = $app_id;
		self::$app_secret = $app_secret;
		self::$master_secret = $master_secret;
		self::$test_secret = $test_secret;
	}

	static function get_sign($app_id, $timestamp, $secret){
		if(empty($app_id) || empty($timestamp) || empty($secret)){
			throw new \Exception(\beecloud\rest\config::VALID_SIGN_PARAM);
		}
		return md5($app_id.$timestamp.$secret);
	}


	static final private function baseParamCheck(array $data) {
		if (!isset($data["app_id"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "app_id");
		}

		if (!isset($data["timestamp"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "timestamp");
		}

		if (!isset($data["app_sign"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "app_sign");
		}
	}

	static final public function post($api, $data, $timeout, $returnArray) {
		$url = \beecloud\rest\network::getApiUrl() . $api;
		$httpResultStr = \beecloud\rest\network::request($url, "post", $data, $timeout);
		$result = json_decode($httpResultStr, !$returnArray ? false : true);
		if (!$result) {
			throw new \Exception(\beecloud\rest\config::UNEXPECTED_RESULT . $httpResultStr);
		}
		return $result;
	}

	static final public function get($api, $data, $timeout, $returnArray) {
		$url = \beecloud\rest\network::getApiUrl() . $api;
		$httpResultStr = \beecloud\rest\network::request($url, "get", $data, $timeout);
		$result = json_decode($httpResultStr,!$returnArray ? false : true);
		if (!$result) {
			throw new \Exception(\beecloud\rest\config::UNEXPECTED_RESULT . $httpResultStr);
		}
		return $result;
	}

	static final public function put($api, $data, $timeout, $returnArray) {
		$url = \beecloud\rest\network::getApiUrl() . $api;
		$httpResultStr = \beecloud\rest\network::request($url, "put", $data, $timeout);
		$result = json_decode($httpResultStr,!$returnArray ? false : true);
		if (!$result) {
			throw new \Exception(\beecloud\rest\config::UNEXPECTED_RESULT . $httpResultStr);
		}
		return $result;
	}

	/**
	 * @param array $data
	 * @param $method post(default),get
	 * @return mixed
	 * @throws \Exception
	 */
	static final public function bill(array $data, $method = 'post') {
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$mode ? self::$test_secret : self::$app_secret);
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
					//case "BC_EXPRESS":
					if (!isset($data["return_url"])) {
						throw new \Exception(\beecloud\rest\config::NEED_RETURN_URL);
					}
					break;
			}

			switch ($data["channel"]) {
				case "WX_JSAPI":
					if (!isset($data["openid"])) {
						throw new \Exception(\beecloud\rest\config::NEED_WX_JSAPI_OPENID);
					}
					break;
				case "ALI_QRCODE":
					if (!isset($data["qr_pay_mode"])) {
						throw new \Exception(\beecloud\rest\config::NEED_QR_PAY_MODE);
					}
					break;
				case "JD_B2B":
					if (!in_array($data["bank_code"], \beecloud\rest\config::get_bank_code())) {
						throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM.'bank_code');
					}
					break;
				case "YEE_WAP":
					if (!isset($data["identity_id"])) {
						throw new \Exception(\beecloud\rest\config::NEED_IDENTITY_ID);
					}
					break;
				case "YEE_NOBANKCARD":
					if (!isset($data["cardno"])) {
						throw new \Exception(\beecloud\rest\config::NEED_CARDNO);
					}
					if (!isset($data["cardpwd"])) {
						throw new \Exception(\beecloud\rest\config::NEED_CARDPWD);
					}
					if (!isset($data["frqid"])) {
						throw new \Exception(\beecloud\rest\config::NEED_FRQID);
					}
					break;
				case "JD_WEB":
				case "JD_WAP":
					if (isset($data["bill_timeout"])) {
						throw new \Exception(\beecloud\rest\config::BILL_TIMEOUT_ERROR);
					}
					break;
				case "KUAIQIAN_WAP":
				case "KUAIQIAN_WEB":
//	                if (isset($data["bill_timeout"])) {
//	                    throw new \Exception(BILL_TIMEOUT_ERROR);
//	                }
//	                break;
				case "BC_GATEWAY":
					if (!isset($data["bank"])) {
						throw new \Exception(\beecloud\rest\config::NEED_PARAM.'bank');
					}
					if (!in_array($data["bank"], \beecloud\rest\config::get_bank())) {
						throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM.'bank');
					}
					break;
				case "BC_EXPRESS" :
					if ($data["total_fee"] < 100 || !is_int($data["total_fee"])) {
						throw new \Exception(\beecloud\rest\config::NEED_TOTAL_FEE);
					}
					break;
			}
		}

		$url = \beecloud\rest\api::getMode() ? \beecloud\rest\config::URI_TEST_BILL : \beecloud\rest\config::URI_BILL;
		switch ($method) {
			case 'get'://支付订单查询
				if (!isset($data["id"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "id");
				}
				$order_id = $data["id"];
				unset($data["id"]);
				return self::get($url.'/'.$order_id, $data, 30, false);
				break;
			case 'post': // 支付
				if (!isset($data["channel"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "channel");
				}
				if (!isset($data["total_fee"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "total_fee");
				} else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "total_fee");
				}

				if (!isset($data["bill_no"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "bill_no");
				}
				if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "bill_no");
				}

				if (!isset($data["title"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "title");
				}
				return self::post($url, $data, 30, false);
				break;
			default :
				exit('No this method');
				break;
		}
	}

	static final public function bills(array $data) {
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$mode ? self::$test_secret : self::$app_secret);
		//required param existence check
		self::baseParamCheck($data);
		self::channelCheck($data);

		$url = \beecloud\rest\api::getMode() ? \beecloud\rest\config::URI_TEST_BILLS : \beecloud\rest\config::URI_BILLS;
		//param validation
		return self::get($url, $data, 30, false);
	}


	static final public function bills_count(array $data){
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$mode ? self::$test_secret : self::$app_secret);
		self::baseParamCheck($data);
		self::channelCheck($data);

		if (isset($data["bill_no"]) && !preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "bill_no");
		}

		$url = \beecloud\rest\api::getMode() ? \beecloud\rest\config::URI_TEST_BILLS_COUNT : \beecloud\rest\config::URI_BILLS_COUNT;
		return self::get($url, $data, 30, false);
	}

	static final public function refund(array $data, $method = 'post') {
		if(empty(self::$master_secret)){
			throw new \Exception(\beecloud\rest\config::VALID_MASTER_SECRET);
		}
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], $method == 'get' ? self::$app_secret : self::$master_secret);
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
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel");
					break;
			}
		}

		switch ($method){
			case 'put': //预退款批量审核
				if (!isset($data["channel"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "channel");
				}
				if (!isset($data["ids"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "ids");
				}
				if (!is_array($data["ids"])) {
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "ids(array)");
				}
				if (!isset($data["agree"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "agree");
				}
				return self::put(\beecloud\rest\config::URI_REFUND, $data, 30, false);
				break;
			case 'get'://退款订单查询
				if (!isset($data["id"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "id");
				}
				$order_id = $data["id"];
				unset($data["id"]);
				return self::get(\beecloud\rest\config::URI_REFUND.'/'.$order_id, $data, 30, false);
				break;
			case 'post': //退款
			default :
				if (!isset($data["bill_no"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "bill_no");
				}
				if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "bill_no");
				}

				if (!isset($data["refund_no"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "refund_no");
				}
				if (!preg_match('/^\d{8}[0-9A-Za-z]{3,24}$/', $data["refund_no"]) || preg_match('/^\d{8}0{3}/', $data["refund_no"])) {
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "refund_no");
				}

				if(!is_int($data["refund_fee"]) || 1>$data["refund_fee"]) {
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "refund_fee");
				}
				return self::post(\beecloud\rest\config::URI_REFUND, $data, 30, false);
				break;
		}
	}


	static final public function refunds(array $data) {
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$app_secret);
		//required param existence check
		self::baseParamCheck($data);
		self::channelCheck($data);
		//param validation
		return self::get(\beecloud\rest\config::URI_REFUNDS, $data, 30, false);
	}

	static final public function refunds_count(array $data) {
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$app_secret);
		//required param existence check
		self::baseParamCheck($data);
		self::channelCheck($data);
		//param validation
		return self::get(\beecloud\rest\config::URI_REFUNDS_COUNT, $data, 30, false);
	}


	static final public function refundStatus(array $data) {
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$app_secret);
		//required param existence check
		self::baseParamCheck($data);

		switch ($data["channel"]) {
			case "WX":
			case "YEE":
			case "KUAIQIAN":
			case "BD":
				break;
			default:
				throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel");
				break;
		}

		if (!isset($data["refund_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "refund_no");
		}
		//param validation
		return self::get(\beecloud\rest\config::URI_REFUND_STATUS, $data, 30, false);
	}

	//单笔打款 - 支付宝/微信红包
	static final public function transfer(array $data) {
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$master_secret);
		self::baseParamCheck($data);
		switch ($data["channel"]) {
			case "WX_REDPACK":
				if (!isset($data['redpack_info'])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . 'redpack_info');
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
						throw new \Exception(\beecloud\rest\config::NEED_PARAM . $v);
					}
				}
				break;
			default:
				throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel = ALI_TRANSFER | WX_TRANSFER | WX_REDPACK");
				break;
		}

		$requiedNames = array("transfer_no",
			"total_fee",
			"desc",
			"channel_user_id"
		);

		foreach($requiedNames as $v) {
			if (!isset($data[$v])) {
				throw new \Exception(\beecloud\rest\config::NEED_PARAM . $v);
			}
		}

		return self::post(\beecloud\rest\config::URI_TRANSFER, $data, 30, false);
	}

	//批量打款 - 支付宝
	static final public function transfers(array $data) {
		if(empty(self::$master_secret)){
			throw new \Exception(\beecloud\rest\config::VALID_MASTER_SECRET);
		}
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$master_secret);
		self::baseParamCheck($data);
		switch ($data["channel"]) {
			case "ALI":
				break;
			default:
				throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel only ALI");
				break;
		}

		if (!isset($data["batch_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "batch_no");
		}

		if (!isset($data["account_name"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "account_name");
		}

		if (!isset($data["transfer_data"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "transfer_data");
		}

		if (!is_array($data["transfer_data"])) {
			throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "transfer_data(array)");
		}

		return self::post(\beecloud\rest\config::URI_TRANSFERS, $data, 30, false);
	}

	//BC企业打款 - 支持bank
	static final public function bc_transfer_banks($data) {
		if (!isset($data["type"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "type");
		}

		if(!in_array($data['type'], array('P_DE', 'P_CR', 'C'))) throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . 'type(P_DE, P_CR, C)');

		return self::get(\beecloud\rest\config::URI_BC_TRANSFER_BANKS, $data, 30, false);
	}

	//BC企业打款 - 银行卡
	static final public function bc_transfer(array $data) {
		if(empty(self::$master_secret)){
			throw new \Exception(\beecloud\rest\config::VALID_MASTER_SECRET);
		}
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$master_secret);
		self::baseParamCheck($data);
		$params = array(
			'total_fee', 'bill_no', 'title', 'trade_source', 'bank_fullname',
			'card_type', 'account_type', 'account_no', 'account_name'
		);
		foreach ($params as $v) {
			if (!isset($data[$v])) {
				throw new \Exception(\beecloud\rest\config::NEED_PARAM . $v);
			}
		}
		if(!in_array($data['card_type'], array('DE', 'CR'))) throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . 'card_type(DE, CR)');
		if(!in_array($data['account_type'], array('P', 'C'))) throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . 'account_type(P, C)');

		return self::post(\beecloud\rest\config::URI_BC_TRANSFER, $data, 30, false);
	}


	static final public function offline_bill(array $data) {
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$app_secret);
		self::baseParamCheck($data);
		if (isset($data["channel"])) {
			switch ($data["channel"]) {
				case "WX_SCAN":
				case "ALI_SCAN":
					if (!isset($data['method']) && !isset($data['auth_code'])) {
						throw new \Exception(\beecloud\rest\config::NEED_PARAM . "auth_code");
					}
					break;
				case "WX_NATIVE":
				case "ALI_OFFLINE_QRCODE":
				case "SCAN":
					break;
				default:
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel = WX_NATIVE | WX_SCAN | ALI_OFFLINE_QRCODE | ALI_SCAN | SCAN");
					break;
			}
		}

		if (!isset($data["bill_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "bill_no");
		}
		if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "bill_no");
		}

		if (!isset($data['method'])) {
			if (!isset($data["channel"])) {
				throw new \Exception(\beecloud\rest\config::NEED_PARAM . "channel");
			}
			if (!isset($data["total_fee"])) {
				throw new \Exception(\beecloud\rest\config::NEED_PARAM . "total_fee");
			} else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
				throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "total_fee");
			}

			if (!isset($data["title"])) {
				throw new \Exception(\beecloud\rest\config::NEED_PARAM . "title");
			}
			return self::post(\beecloud\rest\config::URI_OFFLINE_BILL, $data, 30, false);
		}
		$bill_no = $data["bill_no"];
		unset($data["bill_no"]);
		return self::post(\beecloud\rest\config::URI_OFFLINE_BILL.'/'.$bill_no, $data, 30, false);
	}

	static final public function offline_bill_status(array $data) {
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$app_secret);
		self::baseParamCheck($data);

		if (isset($data["channel"])) {
			switch ($data["channel"]) {
				case "WX_SCAN":
				case "ALI_SCAN":
				case "WX_NATIVE":
				case "ALI_OFFLINE_QRCODE":
					break;
				default:
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel = WX_NATIVE | WX_SCAN | ALI_OFFLINE_QRCODE | ALI_SCAN");
					break;
			}
		}

		if (!isset($data["bill_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "bill_no");
		}
		if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "bill_no");
		}
		return self::post(\beecloud\rest\config::URI_OFFLINE_BILL_STATUS, $data, 30, false);
	}

	static final public function offline_refund(array $data){
		if(empty(self::$master_secret)){
			throw new \Exception(\beecloud\rest\config::VALID_MASTER_SECRET);
		}
		$data["app_id"] = self::$app_id;
		$data["app_sign"] = self::get_sign($data["app_id"], $data["timestamp"], self::$master_secret);
		self::baseParamCheck($data);
		if (isset($data['channel'])) {
			switch ($data["channel"]) {
				case "ALI":
				case "WX":
					break;
				default:
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel = ALI | WX");
					break;
			}
		}

		if (!isset($data["refund_fee"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "refund_fee");
		} else if(!is_int($data["refund_fee"]) || 1>$data["refund_fee"]) {
			throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "refund_fee");
		}

		if (!isset($data["bill_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "bill_no");
		}
		if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "bill_no");
		}

		if (!isset($data["refund_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "refund_no");
		}
		if (!preg_match('/^\d{8}[0-9A-Za-z]{3,24}$/', $data["refund_no"]) || preg_match('/^\d{8}0{3}/', $data["refund_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "refund_no");
		}
		return self::post(\beecloud\rest\config::URI_OFFLINE_REFUND, $data, 30, false);
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
					break;
				default:
					throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel");
					break;
			}
		}
	}
}

class international {

	static final private function baseParamCheck(array $data) {
		if (!isset($data["app_id"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "app_id");
		}

		if (!isset($data["timestamp"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "timestamp");
		}

		if (!isset($data["app_sign"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "app_sign");
		}

		if (!isset($data["currency"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "currency");
		}
	}

	/**
	 * @param array $data
	 * @return mixed
	 * @throws \Exception
	 */
	static final public function bill(array $data) {
		$data["app_id"] = \beecloud\rest\api::$app_id;
		$data["app_sign"] = \beecloud\rest\api::get_sign($data["app_id"], $data["timestamp"], \beecloud\rest\api::$app_secret);
		//param validation
		self::baseParamCheck($data);

		switch ($data["channel"]) {
			case "PAYPAL_PAYPAL":
				if (!isset($data["return_url"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "return_url");
				}
				break;
			case "PAYPAL_CREDITCARD":
				if (!isset($data["credit_card_info"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "credit_card_info");
				}
				break;
			case "PAYPAL_SAVED_CREDITCARD":
				if (!isset($data["credit_card_id"])) {
					throw new \Exception(\beecloud\rest\config::NEED_PARAM . "credit_card_id");
				}
				break;
			default:
				throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "channel");
				break;
		}

		if (!isset($data["total_fee"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "total_fee");
		} else if(!is_int($data["total_fee"]) || $data["total_fee"] < 1) {
			throw new \Exception(\beecloud\rest\config::NEED_VALID_PARAM . "total_fee");
		}

		if (!isset($data["bill_no"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "bill_no");
		}

		if (!isset($data["title"])) {
			throw new \Exception(\beecloud\rest\config::NEED_PARAM . "title");
		}
		return \beecloud\rest\api::post(\beecloud\rest\config::URI_INTERNATIONAL_BILL, $data, 30, false);
	}
}