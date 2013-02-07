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
use HAProxy\Command\Base;

class Executor {
	
	const SOCKET = 'socket';
	const HTTP = 'http';
	
	protected $connection_string;
	protected $method;
	protected $http_auth;
	protected $socket;
	
	public function __construct($connection_string, $method) {
		$this->connection_string = $connection_string;
		$this->method = $method;
	}
	
	public function setCredentials($username, $password) {
		$this->http_auth = $username.':'.$password;
	}
	
	public function execute(Base $command) {
		switch ($this->method) {
			case self::SOCKET:
				return $command->processSocketResponse($this->executeSocket($command));
				break;
			default:
			case self::HTTP:
				return $command->processHttpResponse($this->executeHttp($command));
				break;
		}
	}
	
	protected function executeSocket(Base $command) {
		$this->openSocket();
		fwrite($this->socket, $command->getSocketCommand()."\n");
		$response = '';
		while (!feof($this->socket)) {
			$response .= fgets($this->socket, 1024);
		}
		$this->closeSocket();
		return $response;
	}
	
	protected function executeHttp(Base $command) {
		$data_model = $command->getHttpCommand();
		$ch = curl_init();
		$url = $this->connection_string;
		if ($data_model->method == 'get') {
			foreach ($data_model->data as $key => $value) {
				if ($value === null) {
					$url .= ";$key";
				} else {
					$url .= ";$key=$value";
				}
			}
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->http_auth);
		if ($data_model->method == 'post') {
			curl_setopt($ch, CURLOPT_POST, count($data_model->data));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_model->data));
		}
		$response = curl_exec($ch);
//echo "Request: ".var_export(array('url'=>$url,'post'=>http_build_query($data_model->data)), true)."\n";
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);
		if ($curl_errno !== 0) {
			throw new Exception("Unable to contact server: cURL Error: $curl_error");
		}
//echo "Response: \n$response";
		return $response;
	}
	
	protected function openSocket() {
		if (strpos($this->connection_string, ':')) {
			// TCP Socket
			$this->socket = stream_socket_client('tcp://'.$this->connection_string, $errorno, $errorstr);
		} else if (@filetype(realpath($this->connection_string)) == 'socket') {
			// UNIX Domain Socket
			$this->socket = stream_socket_client('unix://'.realpath($this->connection_string), $errorno, $errorstr);
		} else {
			throw new Exception("Could not open a connection to \"$this->connection_string\": the connection string is invalid");
		}
		if (!$this->socket) {
			throw new Exception("Could not open a connection to \"$this->connection_string\": $errstr ($errno)");
		}
	}
	
	protected function closeSocket() {
		if ($this->socket) {
			@fwrite($this->socket, "quit\n");
			@fclose($this->socket);
		}
	}
	
	public function __destruct() {
		$this->closeSocket();
	}
}