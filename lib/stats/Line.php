<?php
class HAProxy_Stats_Line {
	
	protected $data;
	
	public function __construct($columns, $line) {
		$values = explode(',', $line);
		if (count($columns) != count($values)) {
			die('Column count mismatch: '.count($columns).' columns; '.count($values).' values');
		}
		$this->data = array_combine($columns, $values);
	}
	
	public function __get($name) {
		return $this->data[$name];
	}
	
	public function getAssoc() {
		return $this->data;
	}
}