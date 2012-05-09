<?php
class HAProxy_Command_Base {
	
	protected $response_map = array(
		'DONE' => 'Action processed successfully.',
		'NONE' => 'Nothing has changed.',
		'EXCD' => 'Action not processed: the buffer couldn\'t store all the data. You should retry with less servers at a time.',
		'DENY' => 'Action denied.',
		'UNKN' => 'Unexpected error.'
	);
	
	public function getSocketCommand() {
		throw new HAProxy_Command_NotImplementedException();
	}
	/**
	 * Represents an HTTP command
	 * @return HAProxy_Command_HttpModel
	 * @throws HAProxy_Command_NotImplementedException
	 */
	public function getHttpCommand() {
		throw new HAProxy_Command_NotImplementedException();
	}
	
	public function processHttpResponse($response) {
		$response_lines = preg_split('/[\n\r]+/', $response);
		$http_code = $this->getHttpCode($response_lines);
		if ($http_code >= 400) {
			throw new HAProxy_Exception("Server responded with HTTP $http_code");
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