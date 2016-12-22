<?php
/**
 * @author		Antonio Membrides Espinosa
 * @package    	model
 * @date		20/07/2015
 * @copyright  	Copyright (c) 2015-2015 XETID
 * @license    	XETID
 * @version    	1.0
 */
namespace LQLS\src;
use LQL\src\Main as LQL;
use LQL\src\processor\SQL as Processor;
use LQL\src\executor\Secretary as Executor;
	
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
