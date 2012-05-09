<?php
class HAProxy_Command_ClearCounters extends HAProxy_Command_Base {
	
	protected $all = false;
	
	public function __construct($all=false) {
		$this->all = $all;
	}
	
	public function getSocketCommand() {
		return 'clear counters'.($this->all? ' all': '');
	}
}