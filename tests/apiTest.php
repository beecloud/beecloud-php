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
		$this->timestamp = time() * 1000;
		$this->api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
		$this->api->setSandbox(false);
	}

	public function testBill()
    {
		$data["timestamp"] = $this->timestamp;
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
		$data["timestamp"] = $this->timestamp;
		$data["channel"] = "ALI";
		$data["limit"] = 10;
		$result = $this->api->bills($data);
		$this->assertGreaterThan(0, count($result->bills));
	}

	public function testBillCount()
	{
		$data["timestamp"] = $this->timestamp;
		$data["channel"] = "ALI";
		$result = $this->api->bills_count($data);
		$this->assertTrue(isset($result->count));
	}

	public function testRefund()
	{
		$data["timestamp"] = $this->timestamp;
		$data["bill_no"] = 'bcdemo1460634197000';
		$data["refund_no"] = '201604141460634675000';
		$data["refund_fee"] = 1;
		$data["channel"] = "ALI";
		$result = $this->api->refund($data);
		$this->assertNotEquals(0, $result->result_code);
	}

	public function testRefundStatus()
	{
		$data["timestamp"] = $this->timestamp;
		$data["channel"] = "WX";
		$data["refund_no"] = '20168888888888888888';
		$result = $this->api->refundStatus($data);
		$this->assertEquals('NO_SUCH_REFUND', $result->result_msg);
	}

	public function testRefunds()
	{
		$data["timestamp"] = $this->timestamp;
		$data["channel"] = "ALI";
		$data["limit"] = 10;
		$result = $this->api->refunds($data);
		$this->assertGreaterThan(0, count($result->refunds));
	}

	public function testRefundCount()
	{
		$data["timestamp"] = $this->timestamp;
		$data["channel"] = "ALI";
		$result = $this->api->refunds_count($data);
		$this->assertTrue(isset($result->count));
	}
}
