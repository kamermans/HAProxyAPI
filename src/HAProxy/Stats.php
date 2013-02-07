<?php
/**
 * HAProxyAPI is a PHP API to access/administrate HAProxy via
 * UNIX Sockets, TCP Sockets and/or the HAProxy HTTP interface.
 * 
 * HAProxyAPI was written by Steve Kamerman, 2012 and is distributed
 * via GitHub at https://github.com/kamermans/HAProxyAPI
 * 
 *  @author Steve Kamerman
 *  @copyright Steve Kamerman, 2012
 *  @license GNU GPLv3
 * 
 * This file is part of HAProxyAPI.
 *
 * HAProxyAPI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * HAProxyAPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HAProxyAPI.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace HAProxy;

use HAProxy\Stats\LineCollection;
use HAProxy\Stats\Service;

class Stats {
	
	/**
	 * Stores the tree
	 * @var array
	 */
	protected $proxy_tree = array();
	
	/**
	 * Sets the statistics from the raw CSV stats output from HAProxy
	 * @param string $raw_stats
	 * @return Stats $this
	 */
	public function setFromStatsString($raw_stats) {
		$this->setFromStatsCollection(new LineCollection($raw_stats));
		return $this;
	}
	
	/**
	 * Sets the statistics from a Stats LineCollection
	 * @param LineCollection $collection
	 * @return Stats $this
	 */
	public function setFromStatsCollection(LineCollection $collection) {
		$this->proxy_tree = array();
		foreach ($collection as $line) {
			/* @var $line Stats\Line */
			$service = Service::createFromLine($line);
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
	 * @return Stats\Service[]
	 * @throws Exception
	 */
	public function getBackendServices($backend) {
		if (!$this->backendExists($backend)) throw new Exception("Backend does not exist: $backend");
		return $this->proxy_tree[$backend];
	}
	
	/**
	 * Gets statistics for an individual service
	 * @param string $backend Backend name
	 * @param string $server Server name
	 * @return HAProxy\Stats\Service
	 * @throws Exception
	 * @see getBackendNames()
	 * @see getServerNames()
	 */
	public function getServiceStats($backend, $server) {
		if (!$this->backendExists($backend)) throw new Exception("Backend does not exist: $backend");
		if (!$this->serverExists($backend, $server)) throw new Exception("Server does not exist: $server");
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
	 * @throws Exception
	 */
	public function getServerNames($backend) {
		if (!$this->backendExists($backend)) throw new Exception("Backend does not exist: $backend");
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
	
	/**
	 * Gets statistics using the given executor
	 * @param Executor $exec
	 * @return HAProxy\Stats
	 */
	public static function get(Executor $exec) {
		$stats = new Stats();
		$stats->setFromStatsString($exec->execute(new Command\Stats()));
		return $stats;
	}
}