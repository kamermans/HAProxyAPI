<?php

namespace HAProxy;

class StatsFunctionalHttpTest extends \PHPUnit_Framework_TestCase {

    protected $host = "http://haproxy:10010/stats";
    protected $type = Executor::HTTP;
    protected $username = "testuser";
    protected $password = "testpass";

    public function setUp() {
        if (`grep -c docker /proc/1/cgroup` == 0) {
            $this->markTestSkipped("The functional tests are only run in Docker");
        }
    }

    public function testStatsInstantiate() {
        $exec = new Executor($this->host, $this->type);
        $exec->setCredentials($this->username, $this->password);
        $stats = Stats::get($exec);

        $tree = $stats->getTree();
        $backends = implode(";", array_keys($tree));
        $this->assertEquals("unit-testing;local-backend;stats", $backends);

        $server = $stats->getServiceStats("local-backend","server1");

        $this->assertInstanceOf("HAProxy\\Stats\\Info", $server->info);
        $this->assertInstanceOf("HAProxy\\Stats\\Health", $server->health);
        $this->assertInstanceOf("HAProxy\\Stats\\Queue", $server->queue);
        $this->assertInstanceOf("HAProxy\\Stats\\Session", $server->session);
        $this->assertInstanceOf("HAProxy\\Stats\\Bytes", $server->bytes);
        $this->assertInstanceOf("HAProxy\\Stats\\Rate", $server->rate);
        $this->assertInstanceOf("HAProxy\\Stats\\Abort", $server->abort);
        $this->assertInstanceOf("HAProxy\\Stats\\Denied", $server->denied);
        $this->assertInstanceOf("HAProxy\\Stats\\Error", $server->error);
        $this->assertInstanceOf("HAProxy\\Stats\\Warning", $server->warning);
        $this->assertInstanceOf("HAProxy\\Stats\\HttpResponseCode", $server->http_response_code);
    }

    public function testProxyValues() {
        $exec = new Executor($this->host, $this->type);
        $exec->setCredentials($this->username, $this->password);
        $stats = Stats::get($exec);
        $proxy = $stats->getServiceStats("unit-testing", "FRONTEND");

        // Use these to see the actual values
        // echo $proxy->dump();

        $this->assertTrue($proxy->info->isFrontend());
        $this->assertEquals("OPEN", $proxy->health->status);
        $this->assertEquals("0", $proxy->bytes->in);
        $this->assertEquals("0", $proxy->rate->max);
    }

    public function testServerValues() {
        $exec = new Executor($this->host, $this->type);
        $exec->setCredentials($this->username, $this->password);
        $stats = Stats::get($exec);
        $server = $stats->getServiceStats("local-backend","server1");

        // Use these to see the actual values
        // echo $server->dump();

        $this->assertTrue($server->info->isServer());
        $this->assertEquals("no check", $server->health->status);
        $this->assertEquals("0", $server->http_response_code->http_4xx);
        $this->assertEquals("0", $server->warning->redispatches);
    }

}

