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

namespace HAProxy\Stats;

class Service {
	/**
	 * @var Info
	 */
	public $info;
	/**
	 * @var Health
	 */
	public $health;
	/**
	 * @var Queue
	 */
	public $queue;
	/**
	 * @var Session
	 */
	public $session;
	/**
	 * @var Bytes
	 */
	public $bytes;
	/**
	 * @var Rate
	 */
	public $rate;
	/**
	 * @var Abort
	 */
	public $abort;
	/**
	 * @var Denied
	 */
	public $denied;
	/**
	 * @var Error
	 */
	public $error;
	/**
	 * @var Warning
	 */
	public $warning;
	/**
	 * @var HttpResponseCode
	 */
	public $http_response_code;
	
	/**
	 * Creates a new Service Stats object from a given status line
	 * @param Line $line
	 * @return Service
	 */
	public static function createFromLine(Line $line) {
		$instance = new self();
		$instance->setFromLine($line);
		return $instance;
	}
	
	public function setFromLine(Line $line) {
		$this->info = new Info($line);
		$this->health = new Health($line);
		$this->queue = new Queue($line);
		$this->session = new Session($line);
		$this->bytes = new Bytes($line);
		$this->rate = new Rate($line);
		$this->abort = new Abort($line);
		$this->denied = new Denied($line);
		$this->error = new Error($line);
		$this->warning = new Warning($line);
		$this->http_response_code = new HttpResponseCode($line);
	}
	
	public function dump() {
		$out = '';
		$out .= $this->info->dump();
		$out .= $this->health->dump();
		$out .= $this->queue->dump();
		$out .= $this->session->dump();
		$out .= $this->bytes->dump();
		$out .= $this->rate->dump();
		$out .= $this->denied->dump();
		$out .= $this->error->dump();
		$out .= $this->warning->dump();
		$out .= $this->http_response_code->dump();
		return $out;
	}
}