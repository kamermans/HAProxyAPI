<?php
class HAProxy_Command_HttpModel {
	public $method;
	public $data;
	public function __construct($data, $method='get') {
		$this->data = $data;
		$this->method = strtolower($method);
	}
}