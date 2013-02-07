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


abstract class Base {
	/**
	 * @var Line
	 */
	protected $line;
	protected $map = array();
	
	public function __construct(Line $line) {
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
		return self::stripNamespaces(self::getClassName());
	}
	
	protected static function getClassName() {
		return get_called_class();
	}
										
	/**
	 * Returns the class "\foo\bar\is\cool" without its namespaces, so like "cool"
	 * @param string|object $class
	 * @return string
	 */
	public static function stripNamespaces($class) {
		$class_name = is_string($class)? $class: get_class($class);
		$parts = explode('\\', $class_name);
		return array_pop($parts);
	}
	
}
