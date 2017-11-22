<?php
/*
 * global param config
 */
namespace beecloud\rest;

class config {
    //php sdk verssion
    const PHP_SDK_VERSION = 'PHP_2.3.12';
    //api version
    const API_VERSION = '2';

	//online
    const URI_BILL = 'rest/bill'; //支付;支付订单查询(指定id)
    const URI_TEST_BILL = 'rest/sandbox/bill';
    const URI_BILLS = 'rest/bills'; //订单查询
    const URI_TEST_BILLS = 'rest/sandbox/bills';
    const URI_BILLS_COUNT = 'rest/bills/count'; //订单总数查询
    const URI_TEST_BILLS_COUNT = 'rest/sandbox/bills/count';
    const URI_BC_GATEWAY_BANKS = 'rest/bc_gateway/banks'; //获取银行列表

	const URI_REFUND = 'rest/refund';		//退款;预退款批量审核;退款订单查询(指定id)
	const URI_REFUNDS = 'rest/refunds';		//退款查询
	const URI_REFUNDS_COUNT = 'rest/refunds/count'; //退款总数查询
	const URI_REFUND_STATUS = 'rest/refund/status'; //退款状态更新

	const URI_TRANSFERS = 'rest/transfers'; //批量打款 - 支付宝
	const URI_TRANSFER = 'rest/transfer';  //单笔打款 - 支付宝/微信
	const URI_BC_TRANSFER_BANKS = 'rest/bc_transfer/banks'; //BC企业打款 - 支持银行
	const URI_BC_TRANSFER = 'rest/bc_transfer'; //代付 - 银行卡
	const URI_CJ_TRANSFER = 'rest/cj_transfer'; //畅捷代付
	const URI_JD_TRANSFER = 'rest/bc_user_transfer'; //京东代付
	const URI_GATEWAY_TRANSFER = 'rest/gateway/bc_transfer'; //BeePay自动打款 - 打款到银行卡

    //确认支付
	const URI_PAY_CONFIRM = 'rest/bill/confirm'; //确认支付

	//offline
	const URI_OFFLINE_BILL = 'rest/offline/bill'; //线下支付-撤销订单
	const URI_OFFLINE_BILL_STATUS = 'rest/offline/bill/status'; //线下订单状态查询
	const URI_OFFLINE_REFUND = 'rest/offline/refund'; //线下退款

	//international
	const URI_INTERNATIONAL_BILL = 'rest/international/bill';
	const URI_INTERNATIONAL_REFUND = 'rest/international/refund';

    //发送验证码
    const URI_SMS = 'sms';

	//auth
	const URI_AUTH = 'auth';

	//subscription
	const URI_SUBSCRIPTION = 'subscription';
	const URI_SUBSCRIPTION_PLAN = 'plan';
	const URI_SUBSCRIPTION_BANKS = 'subscription_banks';

    //代扣API
    const URI_CARD_CHARGE_SIGN = 'sign';

    //T1代付
    const URI_T1_EXPRESS_TRANSFER_BANKS = 'rest/t1express/transfer/banks';//代付银行列表接口
    const URI_T1_EXPRESS_TRANSFER = 'rest/t1express/transfer';//代付接口

    //user system
    const URI_USERSYS_USER = 'rest/user'; //单个用户注册接口
    const URI_USERSYS_MULTI_USERS = 'rest/users'; //批量用户导入接口／查询接口
    const URI_USERSYS_HISTORY_BILLS = 'rest/history_bills'; //历史数据补全接口（批量）

    //coupon
    const URI_COUPON = 'rest/coupon'; //发放卡券, 优惠券根据ID或其他条件查询
    const URI_COUPON_TEMP = 'rest/coupon/template'; //根据优惠券模板ID或其他条件查询

	const UNEXPECTED_RESULT = "非预期的返回结果:";
	const NEED_PARAM = "需要必填参数:";
	const NEED_VALID_PARAM = "字段值不合法:";
	const NEED_WX_JSAPI_OPENID = "微信公众号支付需要openid";
	const NEED_RETURN_URL = "当channel参数为 ALI_WEB 或 ALI_QRCODE 或 UN_WEB 或JD_WAP 或 JD_WEB 或 BC_WX_WAP 或 BC_ALI_WEB时 return_url为必填";
	const NEED_IDENTITY_ID = "当channel参数为 YEE_WAP时 identity_id为必填";
	const BILL_TIMEOUT_ERROR = "当channel参数为 JD* 不支持bill_timeout";
	const NEED_QR_PAY_MODE = '当channel参数为 ALI_QRCODE时 qr_pay_mode为必填';
	const NEED_CARDNO = '当channel参数为 YEE_NOBANKCARD时 cardno为必填';
	const NEED_CARDPWD = '当channel参数为 YEE_NOBANKCARD时 cardpwd为必填';
	const NEED_FRQID = '当channel参数为 YEE_NOBANKCARD时 frqid为必填';
	const NEED_TOTAL_FEE = '当channel参数为 BC_EXPRESS时 total_fee单位分,最小金额100分';
	const VALID_BC_PARAM = 'APP ID,APP Secret,Master Secret参数值均不能为空,请重新设置';
	const VALID_SIGN_PARAM = 'APP ID, timestamp,APP(Master/Test) Secret参数值均不能为空,请设置';
	const VALID_APP_ID = 'APP ID参数值不能为空,请设置';

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
     * card_type(string 类型) for channel BC_GATEWAY
    */

    static function get_card_type($type = ''){
        $card_type = array(
            '1' => '1',
            '2' => '2'
        );
        if($type && !in_array($type, $card_type)){
            exit('卡类型: 1代表信用卡, 2代表借记卡');
        }
        if($type){
            return $card_type[$type];
        }
        return $card_type;
    }

	/*
	 * bank(string 类型) for channel BC_GATEWAY
	*/
	static function get_bank($type = ''){
        $banks = array(
		    //信用卡
            '1' => array(
                '工商银行', '建设银行', '中国银行', '农业银行', '交通银行', '邮政储蓄银行', '招商银行', '中信银行', '浦发银行', '兴业银行', '民生银行',
                '光大银行', '平安银行', '华夏银行', '广发银行', '上海银行', '宁波银行', '杭州银行', '青岛银行', '北京银行', '浙江稠州银行',
            ),
            //借记卡
		    '2' => array('工商银行', '建设银行', '中国银行', '农业银行', '交通银行', '邮政储蓄银行', '招商银行', '中信银行', '浦发银行', '兴业银行',
                '民生银行', '光大银行', '平安银行', '华夏银行', '北京银行', '广发银行', '上海银行', '北京农商行', '重庆农商行', '上海农商行',
                '南京银行', '宁波银行', '杭州银行', '成都银行', '青岛银行', '恒丰银行', '渤海银行', '厦门银行', '陕西信合', '浙江稠州银行',
                '贵州农信')
        );
        $card_type = self::get_card_type($type);
        if(!is_array($card_type)){
            return $banks[$card_type];
        }
        return $banks;
	}

	/*
	 * 结算频率interval(string),
	 * 主要包含任一天，一周，一个月或一年。
	 */
	static function get_interval(){
		return array('day', 'week', 'month', 'year');
	}
}