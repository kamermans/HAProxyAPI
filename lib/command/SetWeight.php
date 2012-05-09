<?php
class HAProxy_Command_SetWeight extends HAProxy_Command_Base {
	
	protected $backend;
	protected $server;
	protected $weight;
	
	public function __construct($backend, $server, $weight) {
		$this->backend = $backend;
		$this->server = $server;
	}
	
	public function getSocketCommand() {
		return "set weight $this->backend/$this->server";
	}
}