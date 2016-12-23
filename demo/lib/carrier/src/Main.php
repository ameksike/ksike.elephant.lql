<?php
/*
 *
 * @package: Carrier
 * @version: 0.1
 * @description: This is simple and light lib for php loader class
 * @authors: ing. Antonio Membrides Espinosa
 * @mail: ameksike@gmail.com
 * @created: 23/4/2011
 * @updated: 23/4/2011
 * @license: GPL v3
 * @require: PHP >= 5.2.*
 *
 */
class Carrier 
{
	static public $cfg = array( ''=>'lib/' );
	static public function load($name) {
		$ns = explode("\\", $name);
		$path = isset(self::$cfg[$ns[0]]) ? self::$cfg[$ns[0]] : '';
		$ns[0] = !empty($path) ? $path : $ns[0];
		$file = implode("/", $ns);
		if(file_exists($file.".php"))
			include_once($file.".php");
	}
	static public function active($cfg=false){
		static::setting($cfg);
		spl_autoload_register(get_class() .'::load');
	}
	static public function add($key, $path){
		self::$cfg[$key] = $path;
	}
	static public function setting($cfg=false){
		self::$cfg = is_array($cfg) ? $cfg : self::$cfg;
	}
}
