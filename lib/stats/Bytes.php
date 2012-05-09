<?php
class HAProxy_Stats_Bytes extends HAProxy_Stats_Base {
	protected $map = array(
		'bin' => 'in',
		'bout' => 'out',
	);
	/**
	 * inbound traffic in bytes
	 * @var string
	 */
	public $in;
	/**
	 * outbound traffic in bytes
	 * @var string
	 */
	public $out;
}