<?php
/**
 * @author		Antonio Membrides Espinosa
 * @package    	executor
 * @date		20/07/2015
 * @copyright  	Copyright (c) 2015-2015
 * @license    	GPL
 * @version    	1.0
 */
namespace Ksike\lql\src\server\executor;
use Ksike\lql\src\server\Executor as Executor;
use Ksike\secretary\src\server\Main as DBManager;

class Secretary extends Executor
{
	public function __construct($cfg='mysql'){
		$this->driver = new DBManager($cfg);
	}
	
	public function execute($data){
		return $this->driver->query($data);
	}
}
