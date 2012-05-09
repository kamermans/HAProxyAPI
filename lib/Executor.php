<?php
class HAProxy_Executor {
	
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
	
	public function execute(HAProxy_Command_Base $command) {
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
	
	protected function executeSocket(HAProxy_Command_Base $command) {
		$this->openSocket();
		fwrite($this->socket, $command->getSocketCommand()."\n");
		$response = '';
		while (!feof($this->socket)) {
			$response .= fgets($this->socket, 1024);
		}
		$this->closeSocket();
		return $response;
	}
	
	protected function executeHttp(HAProxy_Command_Base $command) {
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
			throw new HAProxy_Exception("Unable to contact server: cURL Error: $curl_error");
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
			throw new HAProxy_Exception("Could not open a connection to \"$this->connection_string\": the connection string is invalid");
		}
		if (!$this->socket) {
			throw new HAProxy_Exception("Could not open a connection to \"$this->connection_string\": $errstr ($errno)");
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