<?php
/**
 * @author		Antonio Membrides Espinosa
 * @package    	executor
 * @date		20/07/2015
 * @copyright  	Copyright (c) 2015-2015
 * @license    	GPL
 * @version    	1.0
 */
namespace Ksike\lql\lib\executor\secretary\src;
use Ksike\lql\src\server\Executor as Executor;
use Ksike\secretary\src\server\Main as DBManager;

class Main extends Executor
{
	public function __construct($cfg='sqlite'){
		$this->driver = new DBManager($cfg);
	}
	
	public function execute($data){
		return $this->driver->query($data);
	}
}
