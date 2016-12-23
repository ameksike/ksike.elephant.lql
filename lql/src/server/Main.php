<?php
/**
 * @description LQL it's alternative Light Query Language suport for PHP library
 * @author		Antonio Membrides Espinosa
 * @package    	model
 * @date		15/05/2013
 * @license    	GPL
 * @version    	1.0
 * @require		SQLDriver
 */
namespace Ksike\lql\src\server;
class Main
{
	public $executor;
	public $processor;	
	protected $commands;
	protected static $obj = false;
	
	static function setting($executor=null, $processor=null){
		static::this($executor, $processor);
	}
	static function create($executor=null, $processor=null){
		$executor = $executor ? $executor : (static::$obj ? static::$obj->executor : $executor);
		$processor = $processor ? $processor : (static::$obj ? static::$obj->processor : $processor);
		return new static($executor, $processor);
	}
	static function this($executor=null, $processor=null){
		static::$obj = static::$obj ? static::$obj : static::create($executor, $processor);
		return static::$obj;
	}
	
	public function __construct($executor=null, $processor=null){
		$this->commands = array();
		$this->executor = $executor ? $executor : new Executor();
		$this->processor = $processor ? $processor : new Processor();
		$this->executor->clear();
		$this->processor->clear();
	}
	public function __call($action, $arguments){
	   if(!method_exists($this, $action)){
		  $this->commands[$action][] = $arguments;
		  return $this;
	   }
	}
	
	public function clear(){
		unset($this->commands);
		$this->commands = array();
		return $this;
	}
	public function compile($force=false){
		return $this->processor->compile($this->commands, $force);
	}
	public function execute($data=false, $force=false){
		$data = is_file($data) ? file_get_contents($data) : $data;
		return $this->executor->execute(
			$this->processor
				 ->setting($data)
				 ->compile($this->commands, $force)
		);
	}
	/* compatibility with other libs similar to execute function */
	public function query($sql=false, $force=false){
		return $this->execute($sql, $force);
	}
	public function flush($sql=false, $force=false){
		return $this->execute($sql, $force);
	}
	public function persist($sql=false, $force=false){
		return $this->execute($sql, $force);
	}
	public function fetchAll($sql=false, $force=false){
		return $this->execute($sql, $force);
	}
	public function fetchArray($sql=false, $force=false){
		return $this->execute($sql, $force);
	}
}
/**
 * @description LQL Executor Iface
 * @author		Antonio Membrides Espinosa
 * @package    	model
 * @date		15/05/2013
 * @license    	GPL
 * @version    	1.0
 * @require		SQLDriver
 */
class Executor
{
	protected $cache;
	protected $driver;
	
	public function clear(){
		$this->cache = '';
	}
	public function execute($data){
		return $data;
	}
	public function setting($cache=false){ return $this;}
	public function __call($action, $params){
        if(method_exists($this->driver, $action))
           return call_user_func_array(array($this->driver, $action), $params);
    }
}
/**
 * @description LQL Processor Iface
 * @author		Antonio Membrides Espinosa
 * @package    	model
 * @date		15/05/2013
 * @license    	GPL
 * @version    	1.0
 * @require		SQLDriver
 */
class Processor
{
	protected 	$cache;
	protected 	$jobs;
	
	protected 	function evaluate($value, $quote=false){}
	protected 	function generate(&$tmp=false, $jobs=false){}
	protected 	function format($key, $value){}
	
	public 		function clear(){}
	public 		function setting($cache=false){ return $this;}
	public 		function compile($struct, $force=false){return $struct;}
}
