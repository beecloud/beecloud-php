<?php
/*
 * global param config
 */
namespace beecloud\rest;

class config {
	//online
    const URI_BILL = '/2/rest/bill'; //支付;支付订单查询(指定id)
    const URI_TEST_BILL = '/2/rest/sandbox/bill';
    const URI_BILLS = '/2/rest/bills'; //订单查询
    const URI_TEST_BILLS = '/2/rest/sandbox/bills';
    const URI_BILLS_COUNT = '/2/rest/bills/count'; //订单总数查询
    const URI_TEST_BILLS_COUNT = '/2/rest/sandbox/bills/count';
    const URI_BC_GATEWAY_BANKS = '/2/rest/bc_gateway/banks'; //获取银行列表


	const URI_REFUND = "/2/rest/refund";		//退款;预退款批量审核;退款订单查询(指定id)
	const URI_REFUNDS = "/2/rest/refunds";		//退款查询
	const URI_REFUNDS_COUNT = "/2/rest/refunds/count"; //退款总数查询
	const URI_REFUND_STATUS = "/2/rest/refund/status"; //退款状态更新

	const URI_TRANSFERS = "/2/rest/transfers"; //批量打款 - 支付宝
	const URI_TRANSFER = "/2/rest/transfer";  //单笔打款 - 支付宝/微信
	const URI_BC_TRANSFER_BANKS = '/2/rest/bc_transfer/banks'; //BC企业打款 - 支持银行
	const URI_BC_TRANSFER = "/2/rest/bc_transfer"; //代付 - 银行卡
	const URI_CJ_TRANSFER = "/2/rest/cj_transfer"; //畅捷代付
	const URI_JD_TRANSFER = "/2/rest/bc_user_transfer"; //京东代付

	//offline
	const URI_OFFLINE_BILL = '/2/rest/offline/bill'; //线下支付-撤销订单
	const URI_OFFLINE_BILL_STATUS = '/2/rest/offline/bill/status'; //线下订单状态查询
	const URI_OFFLINE_REFUND = '/2/rest/offline/refund'; //线下退款

	//international
	const URI_INTERNATIONAL_BILL = "/2/rest/international/bill";
	const URI_INTERNATIONAL_REFUND = "/2/rest/international/refund";

    //发送验证码
    const URI_SMS = "/2/sms";

	//auth
	const URI_AUTH = "/2/auth";

	//subscription
	const URI_SUBSCRIPTION = "/2/subscription";
	const URI_SUBSCRIPTION_PLAN = "/2/plan";
	const URI_SUBSCRIPTION_BANKS = "/2/subscription_banks";

    //代扣API
    const URI_CARD_CHARGE_SIGN = "/2/sign";

	const UNEXPECTED_RESULT = "非预期的返回结果:";
	const NEED_PARAM = "需要必填字段:";
	const NEED_VALID_PARAM = "字段值不合法:";
	const NEED_WX_JSAPI_OPENID = "微信公众号支付(WX_JSAPI) 需要openid字段";
	const NEED_RETURN_URL = "当channel参数为 ALI_WEB 或 ALI_QRCODE 或 UN_WEB 或JD_WAP 或 JD_WEB 或 BC_WX_WAP时 return_url为必填";
	const NEED_IDENTITY_ID = "当channel参数为 YEE_WAP时 identity_id为必填";
	const BILL_TIMEOUT_ERROR = "当channel参数为 JD* 不支持bill_timeout";
	const NEED_QR_PAY_MODE = '当channel参数为 ALI_QRCODE时 qr_pay_mode为必填';
	const NEED_CARDNO = '当channel参数为 YEE_NOBANKCARD时 cardno为必填';
	const NEED_CARDPWD = '当channel参数为 YEE_NOBANKCARD时 cardpwd为必填';
	const NEED_FRQID = '当channel参数为 YEE_NOBANKCARD时 frqid为必填';
	const NEED_TOTAL_FEE = '当channel参数为 BC_EXPRESS时 total_fee单位分,最小金额100分';
	const VALID_BC_PARAM = 'APP ID,APP Secret,Master Secret参数值均不能为空,请重新设置';
	const VALID_SIGN_PARAM = 'APP ID, timestamp,APP(Master) Secret参数值均不能为空,请设置';
	const VALID_MASTER_SECRET = 'Master Secret参数值不能为空,请设置';
	const VALID_APP_SECRET = 'APP Secret参数值不能为空,请设置';

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