<?php
/**
 * @author		Antonio Membrides Espinosa
 * @package    	executor
 * @date		20/07/2015
 * @copyright  	Copyright (c) 2015-2015
 * @license    	GPL
 * @version    	1.0
 */
namespace Ksike\lql\src\server;
use Ksike\lql\src\server\Main as LQL;
use Ksike\lql\src\server\processor\SQL as Processor;
use Ksike\lql\src\server\executor\Secretary as Executor;
	
class LQLS extends LQL
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
