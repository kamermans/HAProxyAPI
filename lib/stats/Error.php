<?php
class HAProxy_Stats_Error extends HAProxy_Stats_Base {
	protected $map = array(
		'ereq' => 'requests',
		'eresp' => 'responses',
		'econ' => 'connections',
	);
	/**
	 * request errors
	 * @var string
	 */
	public $requests;
	/**
	 * response errors (among which srv_abrt)
	 * @var string
	 */
	public $responses;
	/**
	 * connection errors
	 * @var string
	 */
	public $connections;
}