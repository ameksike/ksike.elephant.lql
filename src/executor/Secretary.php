<?php
/**
 * @author		Antonio Membrides Espinosa
 * @package    	model
 * @date		20/07/2015
 * @copyright  	Copyright (c) 2015-2015 XETID
 * @license    	XETID
 * @version    	1.0
 */
namespace LQLS\src\executor;
use LQL\src\Executor as Executor;
use Secretary\src\server\Main as DBManager;

class Secretary extends Executor
{
	public function __construct($cfg='mysql'){
		$this->driver = new DBManager($cfg);
	}
	
	public function execute($data){
		return $this->driver->query($data);
	}
}
