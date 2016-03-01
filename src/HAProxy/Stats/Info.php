<?php
/**
 * HAProxyAPI is a PHP API to access/administrate HAProxy via
 * UNIX Sockets, TCP Sockets and/or the HAProxy HTTP interface.
 * 
 * HAProxyAPI was written by Steve Kamerman, 2016 and is distributed
 * via GitHub at https://github.com/kamermans/HAProxyAPI
 * 
 *  @author Steve Kamerman
 *  @copyright Steve Kamerman, 2016
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

namespace HAProxy\Stats;

class Info extends Base {
	
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