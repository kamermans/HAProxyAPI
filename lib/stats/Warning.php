<?php
class HAProxy_Stats_Warning extends HAProxy_Stats_Base {
	protected $map = array(
		'wretr' => 'retries',
		'wredis' => 'redispatches',
	);
	/**
	 * retries (warning)
	 * @var string
	 */
	public $retries;
	/**
	 * redispatches (warning)
	 * @var string
	 */
	public $redispatches;
}