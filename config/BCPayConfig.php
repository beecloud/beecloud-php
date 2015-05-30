<?php
/**
* 	配置账号信息
*/

class BCPayConf {
	static public $appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
	static public $appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
}


class BCSetting {
	static public $serverURL = "https://123.57.71.81/1";
	static public function setDebug($flag) {
		if ($flag) {
			self::$serverURL = "http://123.57.71.81:8080/1";
		} else {
			self::$serverURL = "https://123.57.71.81/1";
		}
	}
}