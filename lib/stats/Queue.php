<?php
class HAProxy_Stats_Queue extends HAProxy_Stats_Base {
	protected $map = array(
		'qcur' => 'current',
		'qmax' => 'max',
		'qlimit' => 'limit',
	);
	/**
	 * current queued requests
	 * @var string
	 */
	public $current;
	/**
	 * max queued requests
	 * @var string
	 */
	public $max;
	/**
	 * queue limit
	 * @var string
	 */
	public $limit;
}