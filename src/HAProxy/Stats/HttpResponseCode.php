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

class HttpResponseCode extends Base {
	protected $map = array(
		'hrsp_1xx' => 'http_1xx',
		'hrsp_2xx' => 'http_2xx',
		'hrsp_3xx' => 'http_3xx',
		'hrsp_4xx' => 'http_4xx',
		'hrsp_5xx' => 'http_5xx',
	);
	
	public function __construct($line) {
		parent::__construct($line);
	}
	
	/**
	 * number of HTTP 1xx (Informational) responses sent
	 * @var string
	 */
	public $http_1xx;
	/**
	 * number of HTTP 2xx (Successful) responses sent
	 * @var string
	 */
	public $http_2xx;
	/**
	 * number of HTTP 3xx (Redirection) responses sent
	 * @var string
	 */
	public $http_3xx;
	/**
	 * number of HTTP 4xx (Client Error) responses sent
	 * @var string
	 */
	public $http_4xx;
	/**
	 * number of HTTP 5xx (Internal Server Error) responses sent
	 * @var string
	 */
	public $http_5xx;
	
}