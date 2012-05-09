<?php
class HAProxy_Stats_Health extends HAProxy_Stats_Base {
	
	protected $map = array(
		'status' => 'status',
		'act' => 'active',
		'bck' => 'backup',
		'chkfail' => 'check_failed',
		'chkdown' => 'up_down_transitions',
		'lastchg' => 'status_change',
		'downtime' => 'downtime',
		'throttle' => 'throttle',
		'lbtot' => 'selected_total',
		'check_status' => 'check_status',
		'check_code' => 'check_code',
		'check_duration' => 'check_duration',
		'hanafail' => 'check_fail_details',
	);
	
	/**
	 * status (UP/DOWN/NOLB/MAINT/MAINT(via)...)
	 * @var string
	 */
	public $status;
	/**
	 * server is active (server), number of active servers (backend)
	 * @var string
	 */
	public $active;
	/**
	 * server is backup (server), number of backup servers (backend)
	 * @var string
	 */
	public $backup;
	/**
	 * number of failed checks
	 * @var string
	 */
	public $check_failed;
	/**
	 * number of UP->DOWN transitions
	 * @var string
	 */
	public $up_down_transitions;
	/**
	 * last status change (in seconds)
	 * @var string
	 */
	public $status_change;
	/**
	 * total downtime (in seconds)
	 * @var string
	 */
	public $downtime;
	/**
	 * warm up status
	 * @var string
	 */
	public $throttle;
	/**
	 * total number of times a server was selected
	 * @var string
	 */
	public $selected_total;
	/**
	 * status of last health check
	 * @see getCheckStatusDescription()
	 * @var string
	 */
	public $check_status;
	/**
	 * layer5-7 code, if available
	 * @var string
	 */
	public $check_code;
	/**
	 * time in ms took to finish last health check
	 * @var string
	 */
	public $check_duration;
	/**
	 * failed health checks details
	 * @var string
	 */
	public $check_fail_details;
	
	protected $check_status_desc = array(
		'UNK' => 'unknown',
		'INI' => 'initializing',
		'SOCKERR' => 'socket error',
		'L4OK' => 'check passed on layer 4, no upper layers testing enabled',
		'L4TMOUT' => 'layer 1-4 timeout',
		'L4CON' => 'layer 1-4 connection problem, for example "Connection refused" (tcp rst) or "No route to host" (icmp)',
		'L6OK' => 'check passed on layer 6',
		'L6TOUT' => 'layer 6 (SSL) timeout',
		'L6RSP' => 'layer 6 invalid response - protocol error',
		'L7OK' => 'check passed on layer 7',
		'L7OKC' => 'check conditionally passed on layer 7, for example 404 with disable-on-404',
		'L7TOUT' => 'layer 7 (HTTP/SMTP) timeout',
		'L7RSP' => 'layer 7 invalid response - protocol error',
		'L7STS' => 'layer 7 response error, for example HTTP 5xx',
	);
	
	public function getCheckStatusDescription() {
		return $this->check_status_desc[$this->check_status];
	}
}