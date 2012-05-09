<?php
abstract class HAProxy_Stats_Base {
	/**
	 * @var HAProxy_Stats_Line
	 */
	protected $line;
	protected $map = array();
	
	public function __construct(HAProxy_Stats_Line $line) {
		$this->line = $line;
		$this->assignValues();
	}
	
	public function __get($name) {
		return $this->line->$name;
	}
	
	protected function assignValues() {
		foreach ($this->map as $raw_name => $property) {
			$this->$property = $this->line->$raw_name;
		}
	}
	
	public function dump() {
		$out = '';
		if (function_exists('get_called_class')) {
			$out .= self::getNiceName()." Stats:\n";
		}
		foreach ($this->map as $raw_name => $prop) {
			$out .= "\t$prop: {$this->$prop}\n";
		}
		return $out;
	}
	public static function getNiceName() {
		return preg_replace('/^HAProxy_Stats_(.+)&/', '$1', self::getClassName());
	}
	protected static function getClassName() {
		return get_called_class();
	}
}