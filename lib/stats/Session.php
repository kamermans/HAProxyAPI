<?php
class HAProxy_Stats_Session extends HAProxy_Stats_Base {
	protected $map = array(
		'scur' => 'current',
		'smax' => 'max',
		'slim' => 'limit',
	);
	/**
	 * current sessions
	 * @var string
	 */
	public $current;
	/**
	 * max sessions
	 * @var string
	 */
	public $max;
	/**
	 * sessions limit
	 * @var string
	 */
	public $limit;
	/**
	 * total sessions
	 * @var string
	 */
	public $total;
}