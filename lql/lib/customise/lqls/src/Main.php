<?php
/**
 * @author		Antonio Membrides Espinosa
 * @package    	executor
 * @date		20/07/2015
 * @copyright  	Copyright (c) 2015-2015
 * @license    	GPL
 * @version    	1.0
 */
namespace Ksike\lql\lib\customise\lqls\src;
use Ksike\lql\src\server\Main as LQL;
use Ksike\lql\lib\processor\sql\src\Main as Processor;
use Ksike\lql\lib\executor\secretary\src\Main as Executor;

class Main extends LQL
{
	protected static $cfgexecutor = 'mysql';
	protected static $cfgprocessor = false;
	
	public function __construct($executor=false, $processor=false){
		parent::__construct(
			new Executor($executor ? $executor : self::$cfgexecutor ), 
			new Processor($processor ? $processor : self::$cfgprocessor)
		);
	}

	static function setting($executor=false, $processor=false){
		self::$cfgexecutor = $executor ? $executor : 'mysql';
		self::$cfgprocessor = $processor;
	}
}
