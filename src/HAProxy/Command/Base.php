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

namespace HAProxy\Command;

class Base {
	
	protected $response_map = array(
		'DONE' => 'Action processed successfully.',
		'NONE' => 'Nothing has changed.',
		'EXCD' => 'Action not processed: the buffer couldn\'t store all the data. You should retry with less servers at a time.',
		'DENY' => 'Action denied.',
		'UNKN' => 'Unexpected error.'
	);
	
	public function getSocketCommand() {
		throw new NotImplementedException();
	}
	/**
	 * Represents an HTTP command
	 * @return HttpModel
	 * @throws NotImplementedException
	 */
	public function getHttpCommand() {
		throw new NotImplementedException();
	}
	
	public function processHttpResponse($response) {
		$response_lines = preg_split('/[\n\r]+/', $response);
		$http_code = $this->getHttpCode($response_lines);
		if ($http_code >= 400) {
			throw new HAProxy\Exception("Server responded with HTTP $http_code");
		}
		return $this->getResponseMessage($response_lines);
	}
	
	public function processSocketResponse($response) {
		return $response;
	}
	
	protected function getHttpCode($response_lines) {
		if (preg_match('#HTTP/\d\.\d (\d\d\d) #', $response_lines[0], $matches)) {
			return (int)$matches[0];
		}
	}
	
	protected function getResponseMessage($response_lines) {
		//var_export($response_lines);
		foreach ($response_lines as $line) {
			if (preg_match('/^[Ll]ocation.+;st=(DONE|NONE|EXCD|DENY|UNKN)/', $line, $matches)) {
				return $this->response_map[$matches[1]];
			}
		}
		return 'Unable to parse server response';
	}
}