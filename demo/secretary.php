<?php
	/*
	 * Ejemplo de utilización basica de la biblioteca Ksike/LQL con Ksike/Secretary
	 * */
	 
	//... paso 1: definir el espacio de nombres Ksike en el manejador de carga denominado Carrier
	include __DIR__ . "/lib/carrier/src/Main.php";
	Carrier::active(array( 'Ksike'=> __DIR__ .'/../' ));
	
	//... paso 2: definir los espacios de nombres a utilizar
	use Ksike\lql\lib\customise\lqls\src\Main as LQL;
	
	//... paso 3: cargar las variables de configuracion
	$config["db"]["host"]		= "localhost";		    //... servidor o proveedor de bases de datos
	$config["db"]["user"]		= "postgres";		    //... usuario de una cuenta activa en el servidor de bases de datos
	$config["db"]["pass"]		= "postgres";			//... contraseña requerida para la cuenta activa en el servidor de bases de datos
	$config["db"]["name"]		= "mydb";		        //... nombre de la base de datos a la cual debe conectarse
    $config["db"]["driver"]		= "pgsql";				//... pgsql|mysql|sqlite|sqlsrv|mysqli
	//... paso 4: comenzar a utilizar el LQL
	/*
	 * Creando una seleccion simple y obtener el sql
	 * */
	$sql = LQL::create($config['db'])
		->select('name as nombre, age as edad, serverid')
		->from('person', 'p')
		->compile()
	;
	print_r($sql); echo "   <br> \n";
	/*
	 * Creando una seleccion simple y obtener el resultado
	 * */
	$sql = LQL::create($config['db'])
		->select('name as nombre, age as edad, serverid')
		->from('person', 'p')
		->compile()
	;
	print_r($sql); echo "   <br> \n";
	/*
	 * Creando una seleccion compuesta sobre la tabla person
	 * */
	$sql = LQL::create($config['db'])
		->select('t.nombre as mio, t.edad as era')
		->from(LQL::create($config['db'])
			->select('name as nombre, age as edad, serverid')
			->from('person', 'p'), 't'
		)
		->limit(5)
		->offset(1)
		->compile()
	;
	print_r($sql); echo "   <br> \n";
	/*
	 * Creando un update simple
	 * */
	$sql = LQL::create($config['db'])
		->update('person')
		->set('age', 15)
		->compile()
	;
	print_r($sql); echo "   <br> \n";
	