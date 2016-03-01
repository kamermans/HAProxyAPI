<?php

namespace HAProxy;

class StatsTest extends \PHPUnit_Framework_TestCase {
	
	protected $exec;
	
	protected function setUp() {
		$this->exec = new MockExecutor();
	}
	
	public function testStatsInstantiate() {
		$stats = Stats::get($this->exec);
		
		$tree = $stats->getTree();
		$backends = implode(';', array_keys($tree));
		$this->assertEquals('production-proxy;production-nodes;stats', $backends);
		
		$server = $stats->getServiceStats('production-nodes','node03.example.com');
		
		$this->assertInstanceOf('HAProxy\\Stats\\Info', $server->info);
		$this->assertInstanceOf('HAProxy\\Stats\\Health', $server->health);
		$this->assertInstanceOf('HAProxy\\Stats\\Queue', $server->queue);
		$this->assertInstanceOf('HAProxy\\Stats\\Session', $server->session);
		$this->assertInstanceOf('HAProxy\\Stats\\Bytes', $server->bytes);
		$this->assertInstanceOf('HAProxy\\Stats\\Rate', $server->rate);
		$this->assertInstanceOf('HAProxy\\Stats\\Abort', $server->abort);
		$this->assertInstanceOf('HAProxy\\Stats\\Denied', $server->denied);
		$this->assertInstanceOf('HAProxy\\Stats\\Error', $server->error);
		$this->assertInstanceOf('HAProxy\\Stats\\Warning', $server->warning);
		$this->assertInstanceOf('HAProxy\\Stats\\HttpResponseCode', $server->http_response_code);
	}
	
	public function testProxyValues() {
		$stats = Stats::get($this->exec);
		$proxy = $stats->getServiceStats('production-proxy', 'FRONTEND');
		
		// Use these to see the actual values
		// echo $proxy->dump();
		
		$this->assertTrue($proxy->info->isFrontend());
		$this->assertEquals('OPEN', $proxy->health->status);
		$this->assertEquals('44705774510', $proxy->bytes->in);
		$this->assertEquals('1416', $proxy->rate->max);
	}
	
	public function testServerValues() {
		$stats = Stats::get($this->exec);
		$server = $stats->getServiceStats('production-nodes','node03.example.com');
		
		// Use these to see the actual values
		// echo $server->dump();
		
		$this->assertTrue($server->info->isServer());
		$this->assertEquals('UP', $server->health->status);
		$this->assertEquals('582389', $server->http_response_code->http_4xx);
		$this->assertEquals('422', $server->warning->redispatches);
	}
	
	
	protected function tearDown() {
		$this->exec = null;
	}
}

