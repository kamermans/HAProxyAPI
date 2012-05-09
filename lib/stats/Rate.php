<?php
class HAProxy_Stats_Rate extends HAProxy_Stats_Base {
	protected $map = array(
		'rate' => 'current',
		'rate_max' => 'max',
		'rate_lim' => 'limit',
	);
	/**
	 * number of sessions per second over last elapsed second
	 * @var string
	 */
	public $current;
	/**
	 * max number of new sessions per second
	 * @var string
	 */
	public $max;
	/**
	 * limit on new sessions per second
	 * @var string
	 */
	public $limit;
}