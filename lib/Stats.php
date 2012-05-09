<?php
class HAProxy_Stats {
	
	/**
	 * Stores the tree
	 * @var unknown_type
	 */
	protected $proxy_tree = array();
	
	/**
	 * Sets the statistics from the raw CSV stats output from HAProxy
	 * @param string $raw_stats
	 * @return HAProxy_Stats $this
	 */
	public function setFromStatsString($raw_stats) {
		$this->setFromStatsCollection(new HAProxy_Stats_LineCollection($raw_stats));
		return $this;
	}
	
	/**
	 * Sets the statistics from a Stats LineCollection
	 * @param HAProxy_Stats_LineCollection $collection
	 * @return HAProxy_Stats $this
	 */
	public function setFromStatsCollection(HAProxy_Stats_LineCollection $collection) {
		$this->proxy_tree = array();
		foreach ($collection as $line) {
			/* @var $line HAProxy_Stats_Line */
			$service = HAProxy_Stats_Service::createFromLine($line);
			if (array_key_exists($service->info->proxy_name, $this->proxy_tree)) {
				$this->proxy_tree[$service->info->proxy_name][$service->info->service_name] = $service;
			} else {
				$this->proxy_tree[$service->info->proxy_name] = array($service->info->service_name => $service);
			}
		}
		return $this;
	}
	
	/**
	 * Returns true if the given backend exists
	 * @param string $backend Backend name
	 * @return boolean
	 */
	public function backendExists($backend) {
		return array_key_exists($backend, $this->proxy_tree);
	}
	
	/**
	 * Returns true if the given server exists on the given backend
	 * @param string $backend Backend name
	 * @param string $server Server name
	 * @return boolean
	 */
	public function serverExists($backend, $server) {
		return ($this->backendExists($backend) && array_key_exists($server, $this->proxy_tree[$backend]));
	}
	
	/**
	 * Gets all the services belonging to a given backend
	 * @param string $backend Backend name
	 * @return HAProxy_Stats_Service[]
	 * @throws HAProxy_Exception
	 */
	public function getBackendServices($backend) {
		if (!$this->backendExists($backend)) throw new HAProxy_Exception("Backend does not exist: $backend");
		return $this->proxy_tree[$backend];
	}
	
	/**
	 * Gets statistics for an individual service
	 * @param string $backend Backend name
	 * @param string $server Server name
	 * @return HAProxy_Stats_Service
	 * @throws HAProxy_Exception
	 * @see getBackendNames()
	 * @see getServerNames()
	 */
	public function getServiceStats($backend, $server) {
		if (!$this->backendExists($backend)) throw new HAProxy_Exception("Backend does not exist: $backend");
		if (!$this->serverExists($backend, $server)) throw new HAProxy_Exception("Server does not exist: $server");
		return $this->proxy_tree[$backend][$server];
	}
	
	/**
	 * Returns an array of all the backend names
	 * @return array
	 */
	public function getBackendNames() {
		$list = array();
		foreach ($this->proxy_tree as $name => $obj) {
			$list[] = $name;
		}
		return $list;
	}
	
	/**
	 * Returns an array of all the server names on a given backend
	 * @param string $backend Backend name
	 * @return array
	 * @throws HAProxy_Exception
	 */
	public function getServerNames($backend) {
		if (!$this->backendExists($backend)) throw new HAProxy_Exception("Backend does not exist: $backend");
		foreach ($this->proxy_tree[$backend] as $name => $obj) {
			$list[] = $name;
		}
		return $list;
	}
	
	/**
	 * Returns a human-readable ASCII art tree diagram showing the names of all the backends and servers
	 * @return string
	 */
	public function dumpServiceTree() {
		$out = "\n";
		$i = 0;
		foreach ($this->proxy_tree as $name => $services) {
			$out .= "+- $name\n";
			foreach ($services as $service_name => $server) {
				$out .= "|  +- $service_name ({$server->info->status})\n";
			}
			if (++$i < count($this->proxy_tree)) $out .= "|\n";
		}
		return $out;
	}
	
	public function getTree() {
		return $this->proxy_tree;
	}
	
	public static function get(HAProxy_Executor $exec) {
		$stats = new HAProxy_Stats();
		$stats->setFromStatsString($exec->execute(new HAProxy_Command_Stats()));
		return $stats;
	}
}