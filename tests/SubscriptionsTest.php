<?php
class SubscriptionsTest extends PHPUnit_Framework_TestCase
{
	protected  $subscriptions;
	protected  $timestamp;

	protected function setUp()
	{
		if(version_compare(PHP_VERSION, '5.3.0','>')) {
			$this->subscriptions = new \beecloud\rest\Subscriptions();
		}else{
			$this->subscriptions = new Subscriptions();
		}
		$this->timestamp = time() * 1000;
		$this->subscriptions->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
	}

	public function testBanks()
    {
		$data["timestamp"] = $this->timestamp;
		$result = $this->subscriptions->banks($data);
		$this->assertTrue(isset($result->common_banks));
    }

	public function testQuery_plan()
	{
		$data["timestamp"] = $this->timestamp;
		$result = $this->subscriptions->query_plan($data);
		$this->assertTrue(isset($result->plans));
	}

	public function testQuery_subscription()
	{
		$data["timestamp"] = $this->timestamp;
		$result = $this->subscriptions->query_subscription($data);
		$this->assertTrue(isset($result->subscriptions));
	}
}
