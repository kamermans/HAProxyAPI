<?php
class HAProxy_Stats_Denied extends HAProxy_Stats_Base {
	protected $map = array(
		'dreq' => 'requests',
		'dresp' => 'responses',
	);
	/**
	 * denied requests
	 * @var string
	 */
	public $requests;
	/**
	 * denied responses
	 * @var string
	 */
	public $responses;
}