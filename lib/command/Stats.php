<?php
class HAProxy_Command_Stats extends HAProxy_Command_Base {
	
	public function getSocketCommand() {
		return 'show stat';
	}
	/**
	 * Represents an HTTP command
	 * @return HAProxy_Command_HttpModel
	 * @throws HAProxy_Command_NotImplementedException
	 */
	public function getHttpCommand() {
		return new HAProxy_Command_HttpModel(array('csv' => null), 'get');
	}
	
	public function processHttpResponse($response) {
		list($headers, $body) = preg_split('/\r\n\r\n/', $response, 2);
		return $body;
	}
}