<?php
	/*
	 * Ejemplo de utilización basica de la biblioteca Ksike/LQL con Ksike/Secretary
	 * */
	 
	//... paso 1: definir el espacio de nombres Ksike en el manejador de carga denominado Carrier
	include __DIR__ . "/lib/carrier/src/Main.php";
	Carrier::active(array( 'Ksike'=> __DIR__ .'/../' ));
	
	//... paso 2: definir los espacios de nombres a utilizar
	use Ksike\lql\src\server\LQLS as LQL;
	
	//... paso 3: cargar las variables de configuracion 
	$config['db']["log"]		= "log/";
    $config['db']["driver"]	 	= "sqlite";				//... valores admitidos: pgsql|mysql|mysqli|sqlite|sqlsrv
	$config['db']["name"]		= "ploy";		        //... nombre de la base de datos a la cual debe conectarse
	$config['db']["path"]		= __DIR__ . "/data/";	//... ruta donde se encuentra la base de datos
	$config['db']["extension"]  = "db";					//... default value db
	//... paso 4: configurar la biblioteca
	
	//... paso 5: comenzar a utilizar el LQL
	/*
	 * Creando una seleccion simple y obtener el sql
	 * */
	$sql = LQL::create($config['db'])
		->select('name as nombre, age as edad')
		->from('user', 'p')
		->execute()
	;
	print_r($sql); echo "   <br> \n";
	/*
	 * Creando un update simple
	 * */
	$sql = LQL::create($config['db'])
		->update('user')
		->set('age', 15)
		->where("id", 12)
		->execute()
	;
	/*
	 * Creando un update de multiples campos
	 * */
	$sql = LQL::create($config['db'])
		->update('user')
		->set(['age', "name"], [12, "tieso"])
		->where("id", 12)
		->execute()
	;
	print_r($sql); echo "   <br> \n";
	/*
	 * Creando un insert simple
	 * */
	$sql = LQL::create($config['db'])
		->insert('user')
		->into('name', 'age', 'id')
		->values('Maria Tusa', 12, 24)
		->execute()
	;
	/*
	 * Creando un insert compuesto
	 * */
	$sql = LQL::create($config['db'])
		->insert('user')
		->into('name', 'age', 'id')
		->values('Maria Mria', 15, LQL::create($config['db'])->select('SUM(id) as cant')->from('user'))
		->execute()
	;
	print_r($sql); echo "   <br> \n";
	/*
	 * Creando una consulta de eliminacion con condiciones
	 * */
	$sql = LQL::create($config['db'])
		->delete('user')
		->where('id', 4)
		->persist()
	;
	/*
	 * Creando una consulta compuesta, simulando un flush de Doctrine 2.0
	 * */
	LQL::setting($config['db']);
	$sql = LQL::create()
		->add(LQL::create()
			->insert('user')
			->into('name', 'age', 'id')
			->values('Mustan MMM', 23, LQL::create()->select('COUNT(id) as cant')->from('user'))
		)
		->add(LQL::create()
			->insert('user')
			->into('name', 'age', 'id')
			->values('Mastik TTTT', 14, LQL::create()->select('COUNT(id) as cant')->from('user'))
		)
		->add(LQL::create()
			->insert('user')
			->into('name', 'age', 'id')
			->values('Mistao Tusa', 55, LQL::create()->select('COUNT(id) as cant')->from('user'))
		)
		->add(LQL::create()
			->update('user')
			->set(['name', 'age'], ['LAST', 45])
			->where(TRUE)
			->whereIn('name', ['Mastik TTTT', 'Mastik MMM'])
		)
		->flush()
	;