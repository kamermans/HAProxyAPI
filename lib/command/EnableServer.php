<?php
class HAProxy_Command_EnableServer extends HAProxy_Command_Base {
	
	protected $backend;
	protected $server;
	protected $action = 'enable';
	
	public function __construct($backend, $server) {
		$this->backend = $backend;
		$this->server = $server;
	}
	
	public function getSocketCommand() {
		return "$this->action server $this->backend/$this->server";
	}
	
	public function getHttpCommand() {
		return new HAProxy_Command_HttpModel(array(
			's' => $this->server,
			'action' => $this->action,
			'b' => $this->backend,
		), 'post');
	}
}