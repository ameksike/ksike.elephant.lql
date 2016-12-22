# lql-elephant
generarador de consultas independiente de la capa de acceso a datos, orientado al lenguage PHP

```php

	/*
	 * Ejemplo de utilización 
	 * */
	 
	//... paso 1: incluir el Loader y las funciones utilies (cfg|show)
	include "lib/loader/Main.php";
	include "lib/utils.php";
	
	//... paso 2: los espacios de nombres a utilizar
	use Loader\Main as Loader;
	use LQL\src\Main as LQL;
	use LQL\src\processor\SQL as ProcessorSQL;
	
	//... paso 3: configurar el Loader especificandole las direcciones de las dependencias
	Loader::active(array('LQL'=>'lib/lql'));
	
	//... paso 4: cargar las variables de configuracion 
	$config = cfg();

	//... paso 5: comenzar a utilizar el LQL
	/*
	 * configurar el LQL de forma general para todas las consultas
	 * */
	LQL::setting(null, new ProcessorSQL);
	/*
	 * Creando una selección simple y obtener el sql
	 * */
	$sql = LQL::create()
		->select('count(j.action) as data1, s.denomination as name')
        ->from('mod_pykota.jobhistory j')
		->compile()
	;
	show($sql);
	/*
	 * Creando una selección simple y obtener el sql
	 * */
	$sql = LQL::create()
		->select('t.nombre as mio, t.edad as era')
		->from(LQL::create()
			->select('name as nombre, age as edad, serverid')
			->from('person', 'p'), 't'
		)
		->limit(5)
		->offset(1)
		->compile()
	;
	show($sql);
	/*
	 * Ejecutando una consulta SQL  
	 * */
	show(LQLS::create()->execute('SELECT comando as cmd FROM cambios'));
	/*
	 * Ejecutando una consulta SQL almacenada en un archivo de texto
	 * */
	show(LQLS::create()->execute('data/select.sql'));
	
	
	/*
	 * utilidad denominada show utilizada para inspeccionar el comportamiento del código fuente
	 * */
	function show($obj, $dump=false){
		echo "<pre>";
		if($dump) var_dump($obj);
		else print_r($obj);
		echo "</pre>";
	}
```
	<b> ************************************************************ </b> 
	<h3> LQL + Secretary = OK </h3>
```php
	/*
	 * Ejemplo de utilización, equivalente a: LQL sobre Secretary
	 * */

	//... paso 1: incluir el Loader y las funciones utilies (cfg|show)
	include "lib/loader/Main.php";
	include "lib/utils.php";
	
	//... paso 2: los espacios de nombres a utilizar
	use Loader\Main as Loader;
	use LQL\src\LQLS as LQLS;
	
	//... paso 3: configurar el Loader especificandole las direcciones de las dependencias
	Loader::active(array(
		'Secretary'=>'lib/secretary',
		'LQL'=>'lib/lql'
	));
	//... paso 4: cargar las variables de configuracion
	$config["db"]["host"]		= "localhost";		    //... servidor o proveedor de bases de datos
	$config["db"]["user"]		= "postgres";		    //... usuario de una cuenta activa en el servidor de bases de datos
	$config["db"]["pass"]		= "postgres";			//... contraseña requerida para la cuenta activa en el servidor de bases de datos
	$config["db"]["name"]		= "mydb";		        //... nombre de la base de datos a la cual debe conectarse
    $config["db"]["driver"]		= "pgsql";				//... pgsql|mysql|sqlite|sqlsrv|mysqli

	//... paso 5: comenzar a utilizar el LQLS
	/*
	 * Creando una selección simple y obtener el sql
	 * */
	$sql = LQLS::create($config['db'])
		->select('name as nombre, age as edad, serverid')
		->from('person', 'p')
		->compile()
	;
	show($sql);
	/*
	 * Creando una selección simple y obtener el resultado
	 * */
	$sql = LQLS::create($config['db'])
		->select('name as nombre, age as edad, serverid')
		->from('person', 'p')
		->execute()
	;
	/*
	 * Creando una selección compuesta sobre la tabla person
	 * */
	$sql = LQLS::create($config['db'])
		->select('t.nombre as mio, t.edad as era')
		->from(LQLS::create($config['db'])
			->select('name as nombre, age as edad, serverid')
			->from('person', 'p'), 't'
		)
		->limit(5)
		->offset(1)
		->execute()
	;
	show($sql);
	/*
	 * Creando un update simple
	 * */
	$sql = LQLS::create($config['db'])
		->update('person')
		->set('age', 15)
		->compile()
	;
	/*
	 * Creando un update de multiples atributos y con condiciones
	 * */
	$sql = LQLS::create()
		->update('person')
		->set(['age', 'name'], [30, 'Aqui actualice'])
		->where('id', '3')
		->execute()
	;
	show($sql, true);
	/*
	 * Creando un insert simple
	 * */
	$sql = LQLS::create($config['db'])
		->insert('person')
		->into('name', 'age', 'id')
		->values('Maria Tusa', 12, 24)
		->compile()
	;
	//show($sql, true);
	/*
	 * Creando un insert compuesto
	 * */
	$sql = LQLS::create($config['db'])
		->insert('person')
		->into('name', 'age', 'id')
		->values('Maria Mria', 12, LQLS::create($config['db'])->select('SUM(id) as cant')->from('person'))
		->compile()
	;
	show($sql, true);
	/*
	 * Creando una consulta de eliminacion con condiciones
	 * */
	$sql = LQLS::create($config['db'])
		->delete('person')
		->where('id', 2)
		->persist()
	;
	show($sql, true);
	/*
	 * Creando una consulta para eliminar la tabla cambios
	 * */
	$sql = LQLS::create($config['db'])
		->drop('tieso')
		->execute()
	;
	echo "drop: "; var_dump($sql);
	/*
	 * Configurando el LQLS de forma general para todas las consultas
	 * */
	LQLS::setting($config['db']);
	/*
	 * Creando una consulta compuesta, simulando un flush de Doctrine 2.0
	 * */
	$sql = LQLS::create()
		->add(LQLS::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mustan Tusa', 12, LQLS::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQLS::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mastik Tusa', 12, LQLS::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQLS::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mistao Tusa', 12, LQLS::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQLS::create()
			->update('person')
			->set(['server', 'serverid'], [45, 'LAST'])
			->where(true)
			->whereIn('name', ['Mistao Tusa', 'Mastik Tusa'])
		)
		->flush()
	;
	show($sql);
	/*
	 * Creando una consulta compuesta
	 * */
	$sql = LQLS::create()
		->select("
					u.serverid,
					u.id as uid,
					count(j.id) as work,
					sum(jobsize) as page
				")
		->addSelect("u.fname", 'table')
		->addSelect(LQLS::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'ALLOW')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"allow"
		)
		->addSelect(LQLS::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'DENY')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"deny"
		)
		->addSelect(LQLS::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'WARN')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"warn"
		)
		->addSelect(LQLS::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'CANCEL')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"cancel"
		)
		->from('mod_pykota.jobhistory j')
		->innerJoin("mod_pykota.table u ", 'u.id', "j.fid")
		->groupBy("j.fid, u.fname, u.serverid, u.id")
		->compile()
	;
	show($sql);
```