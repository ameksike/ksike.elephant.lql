<?php
	/*
	 * Ejemplo de utilización basica de la biblioteca Ksike/LQL 
	 * */
	 
	//... paso 1: definir el espacio de nombres Ksike en el manejador de carga denominado Carrier
	include __DIR__ . "/lib/carrier/src/Main.php";
	Carrier::active(array( 'Ksike'=> __DIR__ .'/../' ));
	
	//... paso 2: definir los espacios de nombres a utilizar
	use Ksike\lql\src\server\Main as LQL;
	use Ksike\lql\lib\processor\sql\src\Main as ProcessorSQL;
		
	//... paso 3: comenzar a utilizar el LQL
	/*
	 * configurar el LQL de forma general para todas las consultas, definiendo un procesador de consultas 
	 * para el lenguaje SQL,sin ejecutor, lo que implica que las consultas solo podran obtenerse en modo texto 
	 * y nunca seran ejecutas sobre un sistema gestor de bases de datos
	 * */
	LQL::setting(null, new ProcessorSQL);
	/*
	 * Creando una seleccion simple y obtener el sql
	 * */
	$sql = LQL::create()
		->select('count(j.action) as data1, s.denomination as name')
        ->from('mod_pykota.jobhistory j')
		->compile()
	;
	print_r($sql); echo "   <br> \n";
	
	/*
	 * Creando una seleccion simple con subconsultas anidadas
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
	print_r($sql); echo "   <br> \n";
	/*
	 * Creando una consulta compuesta, simulando un flush de Doctrine 2.0
	 * */
	$sql = LQL::create()
		->add(LQL::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mustan Tusa', 12, LQL::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQL::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mastik Tusa', 12, LQL::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQL::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mistao Tusa', 12, LQL::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQL::create()
			->update('person')
			->set(['server', 'serverid'], [45, 'LAST'])
			->where(true)
			->whereIn('name', ['Mistao Tusa', 'Mastik Tusa'])
		)
		->flush()
	;
	print_r($sql); echo "   <br> \n";
	/*
	 * Creando una consulta compuesta
	 * */
	$sql = LQL::create()
		->select("
					u.serverid,
					u.id as uid,
					count(j.id) as work,
					sum(jobsize) as page
				")
		->addSelect("u.fname", 'table')
		->addSelect(LQL::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'ALLOW')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"allow"
		)
		->addSelect(LQL::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'DENY')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"deny"
		)
		->addSelect(LQL::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'WARN')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"warn"
		)
		->addSelect(LQL::create()
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
	print_r($sql); echo "   <br> \n";
	