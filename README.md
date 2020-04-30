# LQL Elephant
LQL also know as Light Query Language it is a query generator independent of data access layer, it belong  Ksike Framework Elephant distribution, oriented to the PHP programing language. Note that in general a query languages or data query languages (DQLs) are computer languages used to make queries in databases and information systems. 

![Screenshot](README/lql-full.svg)

## LQL consists of three fundamental concepts, which are listed below:
+ Executor: Relates the execute part that establishes the persistence of the data and defines how it is accessed. Due to the proposed plugin architecture, as many executors can be defined as necessary, by default one is defined using the Secretary library. [for more information about Secretary library access this link](https://github.com/ameksike/ksike.elephant.secretary) 

+ Processor: Relates the part that is responsible for analyzing and structuring the defined query based on the provided executor. By default the implemented processor is focused on the SQL language taking into account that it integrates with several database management systems. Like executives, they can be redefined by developers based on their needs.

+ Customize: refers to customizations of the library itself, generally when using LQL you must specify a compatible processor and executor for it to work, this process can be tedious by having to repeat it several times, instead it is customized the library for a specific processor and executor as its use may become more practical. Such is the case of the provided LQLS example which is nothing more than the native integration of LQL and Secretary as a data access layer.

## How to use the LQL library

### How load LQL library and configure it 
In this case, the resource called Carrier is used, which abstracts the developers from the process of loading the required library into memory, through the association of routes with namespaces. [for more information about Carrier library access this link](https://github.com/ameksike/ksike.elephant.carrier) 

```php
//... step 1: include the loader and the utilities functions (cfg | show)
include __DIR__ . "/lib/carrier/src/Main.php";
include "lib/utils.php";

//... step 2: configure the Loader specifying the addresses of the dependencies based on Ksike namespaces
Carrier::active(array( 'Ksike'=> __DIR__ .'/../' ));

//... step 3: define the namespaces to use
use Ksike\lql\src\server\Main as LQL;
use Ksike\lql\lib\processor\sql\src\Main as ProcessorSQL;
use Ksike\lql\lib\executor\secretary\src\Main as ExecutorSQL;

//... step 4: load the configuration variables
$config['db']["log"]		= "log/";
$config['db']["driver"]	 	= "sqlite";				//... admitted values: pgsql|mysql|mysqli|sqlite|sqlsrv
$config['db']["name"]		= "ploy";		        //... name of the database to connect to
$config['db']["path"]		= __DIR__ . "/data/";	//... path where the database is located
$config['db']["extension"]  = "db";					//... default value for database extension

//... step 5: finally configure the LQL in a general way for all queries
LQL::setting(new ExecutorSQL(), new ProcessorSQL);
```

### Creating a simple item selection query
```php
$sql = LQL::create()
	->select('count(j.action) as data1, s.denomination as name')
	->from('mod_pykota.jobhistory j')
	->compile()
;
```
Note that when using the compile function instead of flush or execute, you are being instructed to only return the value thrown by the processor, in this case the output would be SQL code


### Creating a more complex item selection query including limits and nested subqueries
```php
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
```

### How to run queries
```php
//... run query specifying SQL in clear text
LQLS::create()->execute('SELECT comando as cmd FROM cambios');

//... run query from external file in SQL format
LQLS::create()->execute('data/select.sql');
```

## How to use LQLS customization, it is equivalent to LQL over Secretary
```php
//... step 1: include the loader 
include __DIR__ . "/lib/carrier/src/Main.php";

//... step 2: configure the Loader specifying the addresses of the dependencies based on LQL and Secretary namespaces
Carrier::active(array(
	'Secretary'=>'lib/secretary',
	'LQL'=>'lib/lql'
));

///... step 3: define the namespaces to use
use LQL\src\LQLS as LQLS;

//... step 4: load the configuration variables
$config['db']["log"]		= "log/";
$config['db']["driver"]	 	= "sqlite";	
$config['db']["name"]		= "ploy";
$config['db']["path"]		= __DIR__ . "/data/";	
$config['db']["extension"]  = "db";	
```
### Creating a simple selection and get the SQL as output
```php
$sql = LQLS::create($config['db'])
	->select('name as nombre, age as edad, serverid')
	->from('person', 'p')
	->compile()
;
```

### Other practical examples of table queries
```php
//... simple query name, age and serverid using aliases
$sql = LQLS::create($config['db'])
	->select('name as nombre, age as edad, serverid')
	->from('person', 'p')
	->execute()
;

//... Creating a compound selection on the person table
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

//... Creating a simple update
$sql = LQLS::create($config['db'])
	->update('person')
	->set('age', 15)
	->compile()
;

//... Creating an update with multiple attributes and with conditions
$sql = LQLS::create()
	->update('person')
	->set(['age', 'name'], [30, 'Aqui actualice'])
	->where('id', '3')
	->execute()
;

//... Creating a simple insert
$sql = LQLS::create($config['db'])
	->insert('person')
	->into('name', 'age', 'id')
	->values('Maria Tusa', 12, 24)
	->compile()
;

//... Creating a composite insert
$sql = LQLS::create($config['db'])
	->insert('person')
	->into('name', 'age', 'id')
	->values('Maria Mria', 12, LQLS::create($config['db'])->select('SUM(id) as cant')->from('person'))
	->compile()
;

//... Creating a delete query with conditions
$sql = LQLS::create($config['db'])
	->delete('person')
	->where('id', 2)
	->persist()
;

//... Creating a query to delete the changes table
$sql = LQLS::create($config['db'])
	->drop('tieso')
	->execute()
;
```

### How to configure LQLS in a general way for all queries
```php
LQLS::setting($config['db']);

//... Creating a compound query, simulating a Doctrine 2.0 flush
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
```

### How to create complex queries using relations between join tables
```php
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
```

The flush operation incorporates a performance improvement to the queries because in a single interaction with the database server it executes all the nested queries, an approach to the use of transactions.
