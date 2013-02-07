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

use HAProxy\Stats\Line;

class LineCollection implements \Iterator {
	
	protected $lines = array();
	protected $columns = array();
	protected $storage = array();
	protected $iter_index = 0;
	
	public function __construct($stats_string) {
		$raw_lines = $this->splitRawStats($stats_string);
		$this->columns = $this->getColumnsFromHeader(array_shift($raw_lines));
		$this->lines = $this->createLines($this->columns, $raw_lines);
	}
	
	protected function splitRawStats($stats_string) {
		return preg_split('/\r?\n/', $stats_string);
	}
	
	protected function getColumnsFromHeader($line) {
		$line = preg_replace('/^[# ]+/', '', $line);
		return explode(',', $line);
	}
	
	protected function createLines(array $columns, array $raw_lines) {
		$lines = array();
		foreach ($raw_lines as $line) {
			if ((substr_count($line, ',') + 1) != count($columns)) continue;
			$lines[] = new Line($columns, $line);
		}
		return $lines;
	}
	
	public function getColumns() {
		return $this->columns;
	}
	
	public function count() {
		return count($this->lines);
	}
	
	public function getLine($line_num) {
		return $this->lines[$line_num];
	}
	
	public function getValue($line_num, $name) {
		return $this->lines[$line_num]->$name;
	}
	
	public function __toString() {
		$this->dump();
	}
	
	public function dump() {
		$out = array();
		foreach ($this->lines as $line_num => $line) {
			$out[] = $line->getAssoc();
		}
		return var_export($out, true);
	}
	
	public function rewind() {
		$this->iter_index = 0;
	}
	
	public function valid() {
		return isset($this->lines[$this->iter_index]);
	}
	
	public function current() {
		return $this->lines[$this->iter_index];
	}
	
	public function key() {
		return $this->iter_index;
	}
	
	public function next() {
		$this->iter_index++;
	}
}