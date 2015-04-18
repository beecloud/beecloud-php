<?php
/**
* 	配置账号信息
*/

class BCPayConf {
	static public $appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
	static public $appSecret = "";
}


class BCSetting {
	static public $serverURL = "http://123.57.71.81:8080/1";
	static public function setDebug($flag) {
		if ($flag) {
			self::$serverURL = "http://api.beecloud.cn:8080/1";
		} else {
			self::$serverURL = "https://api.beecloud.cn/1";
		}
	}
}