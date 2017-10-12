<?php
/**
 * @desc: 鉴权
 *
 * @author: jason
 * @since: 16/7/26
 */

require_once("../loader.php");
require_once("config.php");

Class AuthDemo{

	function __construct($app_id, $app_secret, $master_secret, $test_secret) {
		\beecloud\rest\Auths::registerApp($app_id, $app_secret, $master_secret, $test_secret);
	}

    /*
	 * @desc 二要素,三要素,四要素鉴权,如果鉴权成功，会自动在全局的card表中创建一条card记录
	 * 二要素: (name, id_no)
     * 三要素: (name, id_no, card_no)
     * 四要素: (name, id_no, card_no, mobile)
	 * @param array $data, 主要包含以下四个参数:
	 * 	name string 身份证姓名(必填)
	 *  id_no string 身份证号(必填)
	 *  card_no string 用户银行卡卡号
	 *  mobile string 手机号
	 * @return json
	 *  "card_id": "xxx", 要素认证成功返回
	 *  "auth_result": true, 要素认证是否成功
	 *  "auth_msg": "xxx不匹配", 返回给用户的直接让用户能看懂的鉴权结果消息
	 */
	function auth(){
		$data = array(
			'timestamp' => time() * 1000,
			'name' => 'jason',
			'card_no' => '6227856101009660xxx',
			'id_no' => '23082619860124xxxx',
			'mobile' => '1555551xxxx'
		);
		return \beecloud\rest\Auths::auth($data);
	}
}

try {
	$demo = new AuthDemo(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
	$auth = $demo->auth();
	if ($auth->result_code != 0) {
		print_r($auth);
		exit();
	}
	echo '<pre>';
	print_r($auth);die;
}catch(Exception $e){
	echo $e->getMessage();
}