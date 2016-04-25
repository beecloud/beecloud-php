<?php
class apiTest extends PHPUnit_Framework_TestCase
{
	protected  $api;
	protected  $appId;
	protected  $timestamp;
	protected  $appSign;

	protected function setUp()
	{
		if(version_compare(PHP_VERSION, '5.3.0','>')) {
			$this->api = new \beecloud\rest\api();
		}else{
			$this->api = new BCRESTApi();
		}
		$this->appId = APP_ID;
		$this->timestamp = time() * 1000;
		$this->appSign = md5($this->appId . $this->timestamp . APP_SECRET);
	}

	public function testBill()
    {
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["total_fee"] = 1;
		$data["bill_no"] = "bcdemo" . $data["timestamp"];
		$data["title"] = "白开水";
		$data["return_url"] = "http://payservice.beecloud.cn";
		$data["channel"] = "ALI_WEB";
		$result = $this->api->bill($data);
		$this->assertTrue(isset($result->result_code));
    }

	public function testBills()
	{
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["channel"] = "ALI";
		$data["limit"] = 10;
		$result = $this->api->bills($data);
		$this->assertGreaterThan(0, count($result->bills));
	}

	public function testBillCount()
	{
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["channel"] = "ALI";
		$result = $this->api->bills_count($data);
		$this->assertTrue(isset($result->count));
	}

	public function testRefund()
	{
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["bill_no"] = 'bcdemo1460634197000';
		$data["refund_no"] = '201604141460634675000';
		$data["refund_fee"] = 1;
		$data["channel"] = "ALI";
		$result = $this->api->refund($data);
		$this->assertNotEquals(0, $result->result_code);
	}

	public function testRefundStatus()
	{
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["channel"] = "WX";
		$data["refund_no"] = '201604121460463957000';
		$result = $this->api->refundStatus($data);
		print_r($result);
		//$this->assertNotEquals('SUCCESS', $result->refund_status);
	}

	public function testRefunds()
	{
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["channel"] = "ALI";
		$data["limit"] = 10;
		$result = $this->api->refunds($data);
		$this->assertGreaterThan(0, count($result->refunds));
	}

	public function testRefundCount()
	{
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["channel"] = "ALI";
		$result = $this->api->refunds_count($data);
		$this->assertTrue(isset($result->count));
	}

	/*public function testOfflineBill(){
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["total_fee"] = 1;
		$data["bill_no"] = "bcdemo" . $data["timestamp"];
		$data["title"] = "白开水";
		$data["channel"] = 'ALI_OFFLINE_QRCODE';
		$data["return_url"] = "http://payservice.beecloud.cn";
		$result = $this->api->offline_bill($data);
		$this->assertEquals(0, $result->result_code);
	}

	public function testOfflineBillStatus(){
		$data["app_id"] = $this->appId;
		$data["timestamp"] = $this->timestamp;
		$data["app_sign"] = $this->appSign;
		$data["bill_no"] = "bcdemo1460637009000";
		$data["channel"] = 'ALI_OFFLINE_QRCODE';
		$result = $this->api->offline_bill_status($data);
		$this->assertTrue(isset($result->result_code));
	}*/
}
