<?php
/**
 * 支付宝 return_url 获取支付状态
 */
$aliTradeSuccess = ($_GET["trade_status"] == "TRADE_SUCCESS" || $_GET["trade_status"] == "TRADE_FINISHED") ? true : false ;

/**
 * 银联 return_url 获取支付状态
 */

$unTradeSuccess = $_POST["respCode"] == "00" ? true : false;
