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

class Rate extends Base {
	protected $map = array(
		'rate' => 'current',
		'rate_max' => 'max',
		'rate_lim' => 'limit',
	);
	
	public function __construct($line) {
		parent::__construct($line);
	}
	
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