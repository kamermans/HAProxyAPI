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
class HAProxy_Loader {
	/**
	 * @var string The directory that this file is in.  Used by loadClass()	
	 */
	private static $base_path;
	const CLASS_PREFIX = 'HAProxy_';
	
	/**
	 * Loads Class files
	 * @param string $class_name
	 * @access private
	 */
	public static function loadClass($class_name) {
		if (self::$base_path === null) {
			self::$base_path = dirname(__FILE__);
		}
		if (strpos($class_name, self::CLASS_PREFIX) !== 0) {
			return;
		}
		$file = str_replace('_', DIRECTORY_SEPARATOR, substr($class_name, strlen(self::CLASS_PREFIX))).'.php';
		include self::$base_path.DIRECTORY_SEPARATOR.$file;
	}
}

spl_autoload_register(array('HAProxy_Loader', 'loadClass'), false);