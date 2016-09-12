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

	//二要素: (name, id_no)
    //三要素: (name, id_no, card_no)
    //四要素: (name, id_no, card_no, mobile)
    //如果鉴权成功，会自动在全局的card表中创建一条card记录
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