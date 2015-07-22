<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 7/22/15
 * Time: 10:43
 */

/**
 * 支付宝 return_url 获取支付状态
 */
$aliTradeSuccess = ($_GET["trade_status"] == "TRADE_SUCCESS" || $_GET["trade_status"] == "TRADE_FINISH") ? true : false ;

/**
 * 银联 return_url 获取支付状态
 */

$unTradeSuccess = ($_POST["respCode"] == "00" && $_POST["respMsg"] == "success") ? true : false;
