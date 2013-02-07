<?php

namespace HAProxy\Test;

use HAProxy\Command\Base;
use HAProxy\Stats\Base as StatUtil;
use HAProxy\Executor;

class MockExecutor extends Executor {
	
	public function __construct() {
		parent::__construct('example.com:10010', Executor::SOCKET);
	}
	
	protected function executeSocket(Base $command) {
		$type = StatUtil::stripNamespaces($command);
		switch ($type) {
			case 'Stats':
				return $this->getMockStatsData();
				break;
			default:
				throw new \HAProxy\Exception("Unable to execute command, type unknown: $type");
				break;
		}
	}
	
	protected function getMockStatsData() {
		static $data =
'# pxname,svname,qcur,qmax,scur,smax,slim,stot,bin,bout,dreq,dresp,ereq,econ,eresp,wretr,wredis,status,weight,act,bck,chkfail,chkdown,lastchg,downtime,qlimit,pid,iid,sid,throttle,lbtot,tracked,type,rate,rate_lim,rate_max,check_status,check_code,check_duration,hrsp_1xx,hrsp_2xx,hrsp_3xx,hrsp_4xx,hrsp_5xx,hrsp_other,hanafail,req_rate,req_rate_max,req_tot,cli_abrt,srv_abrt,
production-proxy,FRONTEND,,,0,876,8192,85528025,44705774510,40817785439,0,0,144247,,,,,OPEN,,,,,,,,,1,1,0,,,,0,1,0,1416,,,,0,84186478,1,1321255,20250,41,,1,1416,85528025,,,
production-nodes,node01.example.com,0,0,0,734,,43005039,22512008349,20543422993,,0,,0,104,780,7,UP,1,1,0,152,53,259322,34439,,1,2,1,,43004259,,2,0,,708,L7OK,200,4,0,42408148,0,593596,0,0,0,,,,90173,104,
production-nodes,node02.example.com,0,0,0,9,,4481,1878931,2106967,,0,,0,1,0,0,UP,1,0,1,157,54,4868,28064,,1,2,2,,4481,,2,0,,33,L7OK,200,22,0,3408,0,1018,54,0,0,,,,0,0,
production-nodes,node03.example.com,0,0,0,538,,42380248,22188916642,20244462235,,0,,0,1870,5260,422,UP,1,1,0,144,54,258985,286925,,1,2,3,,42374997,,2,1,,708,L7OK,200,3,0,41774922,1,582389,0,0,0,,,,94093,90,
production-nodes,node04.example.com,0,0,0,0,,0,0,0,,0,,0,0,0,0,UP,1,0,1,155,50,258985,289563,,1,2,4,,0,,2,0,,0,L7OK,200,22,0,0,0,0,0,0,0,,,,0,0,
production-nodes,BACKEND,0,0,0,875,0,85383733,44703010797,40790082295,0,0,,425,1976,6040,429,UP,2,2,2,,4,620646,39,,1,2,0,,85383737,,1,1,,1416,,,,0,84186478,1,1177003,20250,1,,,,,184266,194,
stats,FRONTEND,,,1,2,2000,2634,1039226,13621484,0,0,150,,,,,OPEN,,,,,,,,,1,3,0,,,,0,1,0,13,,,,0,1221,368,200,844,0,,1,13,2634,,,
stats,BACKEND,0,0,0,1,2000,844,1039226,13621484,0,0,,844,0,0,0,UP,0,0,0,,0,29145982,0,,1,3,0,,0,,1,0,,13,,,,0,0,0,0,844,0,,,,,0,0,';
		// Normalizing line feeds
		return preg_replace('/[\n\r]+/', "\n", $data);
	}
}
