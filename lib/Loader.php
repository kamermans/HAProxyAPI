<?php
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