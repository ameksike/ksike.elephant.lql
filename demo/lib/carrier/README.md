# carrier-elephant
biblioteca simple y ligera que facilita la gestión de carga dinámica de clases a través de un listado de espacios de nombres previamente definidos. 


```php
	//... paso 1: cargar en memoria la clase principal de la biblioteca 
	include __DIR__ . "/lib/carrier/src/Main.php";
	
	//... paso 2: definir los espacios de nombres y sus respectivas rutas de localización	
	Carrier::active(array( 
		'Ksike'=> '/home/me/libs/ksike/lib/',
		'Cordova'=> '/home/me/libs/cordova/',
		'Scure'=> '/var/www/scure/' 
	));
	
	//... paso 3: utilizar las bibliotecas deseadas 
	use Ksike\secretary\src\server\Main as Secretary;
	print_r(Secretary::this());

```