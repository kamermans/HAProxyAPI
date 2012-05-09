<?php
class HAProxy_Stats_Info extends HAProxy_Stats_Base {
	
	const TYPE_FRONTEND = 0;
	const TYPE_BACKEND = 1;
	const TYPE_SERVER = 2;
	const TYPE_SOCKET = 4;
	
	protected $map = array(
		'pxname' => 'proxy_name',
		'svname' => 'service_name',
		'weight' => 'weight',
		'pid' => 'process_id',
		'iid' => 'proxy_id',
		'sid' => 'service_id',
		'tracked' => 'tracked',
		'type' => 'type',
	);
	
	/**
	 * type of service (0=frontend, 1=backend, 2=server, 3=socket)
	 * @var string
	 */
	public $type;
	/**
	 * proxy name
	 * @var string
	 */
	public $proxy_name;
	/**
	 * service name (FRONTEND for frontend, BACKEND for backend, any name for server)
	 * @var string
	 */
	public $service_name;
	/**
	 * process id (0 for first instance, 1 for second, ...)
	 * @var string
	 */
	public $process_id;
	/**
	 * unique proxy id
	 * @var string
	 */
	public $proxy_id;
	/**
	 * service id (unique inside a proxy)
	 * @var string
	 */
	public $service_id;
	/**
	 * server weight (for servers) or total weight (for backends)
	 * @var string
	 */
	public $weight;
	/**
	 * id of proxy/server if tracking is enabled
	 * @var string
	 */
	public $tracked;
	
	/**
	 * true if this is a frontend proxy service (NOT an individual server)
	 * @return boolean
	 */
	public function isFrontend() {
		return ($this->type == self::TYPE_FRONTEND);
	}
	/**
	 * true if this is a backend proxy service (NOT an individual server)
	 * @return boolean
	 */
	public function isBackend() {
		return ($this->type == self::TYPE_BACKEND);
	}
	/**
	 * true if this is an individual server
	 * @return boolean
	 */
	public function isServer() {
		// These can never both be true, so they must both be false, therefore this is a server :)
		return ($this->type == self::TYPE_SERVER);
	}
}