<?php
class HAProxy_Stats_Abort extends HAProxy_Stats_Base {
	protected $map = array(
		'cli_abrt' => 'client',
		'srv_abrt' => 'server',
	);
	/**
	 * number of data transfers aborted by the client
	 * @var string
	 */
	public $client;
	/**
	 * number of data transfers aborted by the server (inc. in eresp)
	 * @var string
	 */
	public $server;
}