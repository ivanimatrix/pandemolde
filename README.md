# PANDEMOLDE
Pandemolde framework PHP : otro framework para desarrollos en PHP. Ojalá intituivo y rápido, ya que esa es la idea...

Usa la arquitectura **MVC** (Modelo - Vista - Controlador), pero basado en módulos. O sea, se deben crear módulos y cada uno de estos tendrá sus propios controladores, modelos y vistas.

Otra característica es que **Pandemolde** ocupa el concepto de **Entidad** para referirse a los "modelos", de otros frameworks.

## Migas
**migas** es un script que permite administrar y generar elementos como Módulos, Controladores y Entidades para tu proyecto. Se puede ejecutar en una consola/terminal tanto en Linux, MacOS y Windows (para Windows se debe usar el ejecutable del **PHP**).

A continuación, una mirada rápida a ciertos comandos que acepta **migas**

### Crear Proyecto
~~~
$ php migas app::create
~~~
Crea la estructura completa para comenzar a desarrollar el proyecto. Si ya se ha creado anteriormente, todo el contenido será borrado. La estructura es:

- `app` : Aquí deben ir todos los módulos que se desarrollen para la aplicación. También debe contener el archivo `app_config.php` y `app_database.php` para los parámetros de configuración y conexión a base de datos, respectivamente

- `pub` : Carpeta destinada a guardar y contener ficheros de uso público, como hojas de estilo, código javascript, etc.

- `libs` : Si tu proyecto usa librerías de PHP y que son invocadas por muchos módulos, entonces es aquí donde debiesen ir

- `sql` : Si quieres guardar tus script de SQL, podrías usar este directorio para hacerlo. La verdad, es que sólo existe por un tema de orden

- `store` : Para almacenar documentos, imágenes subidas, etc.

- `tmp` : Esta carpeta contiene los logs que se vayan generando durante la ejecución de la aplicación. Lo ideal, es que tenga permiso de escritura

- `pan` : Núcleo de Pandemolde. Si no está, no funciona.


### Crear Módulo
~~~
$ php migas module::NOMBRE_MODULO
~~~
Se crea un módulo con el nombre NOMBRE_MODULO, y una estructura de directorios como se visualiza:
~~~
app/
	NOMBRE_MODULO/
		assets/
			css/
			img/
			js/
			others/
		controllers/
		entities/
		libraries/
		views/
~~~

De similar manera, si se ha creado un "grupo" (carpeta para organizar módulos dentro de tu aplicación), se pueden crear sus respectivos módulos con el comando
~~~
$ php migas module::NOMBRE_MODULO@NOMBRE_GRUPO
~~~

### Crear Controlador
~~~
$ php migas controller::NOMBRE_MODULO/NOMBRE_CONTROLADOR
~~~
Crea un controlador llamado NOMBRE_CONTROLADOR dentro del directorio controllers, en módulo NOMBRE_MODULO, 

~~~
$ php migas controller::NOMBRE_MODULO/NOMBRE_CONTROLADOR actions::action_1,action_2,action_n
~~~
Crea un controlador llamado NOMBRE_CONTROLADOR dentro del directorio controllers, en módulo NOMBRE_MODULO, y además define la action_1, action_2...action_n como métodos dentro del controlador.

### Crear Entidad
~~~
$ php migas entity::NOMBRE_MODULO/NOMBRE_ENTIDAD
~~~
Crea una entidad llamanda NOMBRE_ENTIDAD dentro del directorio entities, en módulo NOMBRE_MODULO

~~~
$ php migas entity::NOMBRE_MODULO/NOMBRE_ENTIDAD table::nombre_tabla
~~~
Crea una entidad llamanda NOMBRE_ENTIDAD dentro del directorio entities, en módulo NOMBRE_MODULO, y se inicializa variable `$table` con el `nombre_tabla`.

~~~
$ php migas entity::NOMBRE_MODULO/NOMBRE_ENTIDAD table::nombre_tabla pk::nombre_primary_key
~~~
Crea una entidad llamanda NOMBRE_ENTIDAD dentro del directorio entities, en módulo NOMBRE_MODULO, y se inicializa variable `$table` con el `nombre_tabla`, y también se inicializa la variable `$primary_key` con `nombre_primary_key`.


Cada uno de los comandos mencionados anteriormente, pueden ser consultados con el comando
~~~
$ php migas help::me
~~~
Esto muestra una ayuda general de los comandos mencionados


## Trabajar en un proyecto con PANDEMOLDE
Como se mencionó anteriormente, los módulos, controladores, entidades, vistas, etc. que se vayan creando, tienen que ir dentro de la carpeta `app`.

Para ir conociendo los distintos elementos de PANDEMOLDE, se creará un proyecto de prueba:
~~~
$ php migas app::create
~~~

A continuación, se creará un módulo llamado `Test`:
~~~
$ php migas module::Test
~~~

Creamos un controlador llamado `TestController`:
~~~
$ php migas controller::Test/TestController
~~~
Ahora dentro de la carpeta `controller` del módulo `Test` existe un archivo llamado `TestController.php` con el siguiente contenido:
~~~
namespace App\Test;


class TestController extends \pan\Controller{

	public function __construct(){
		parent::__construct();
	}

}
~~~

Agreguemos una acción (método) para ser consultado en el navegador:
~~~
namespace App\Test;


class TestController extends \pan\Controller{

    public function __construct(){
        parent::__construct();
    }

    public function foo()
    {
        echo 'Hola mundo';
    }

}
~~~
Y para acceder a la acción del controlador, en la url escribimos: 
~~~
URL_PROYECTO/Test/TestController/foo
~~~

Para poder mostrar una vista, se puede usar `View::render()` o `$this->view->render()`, si el controlador hereda del `__construct()` de `\Pan\Controller`.

La vista debe crearse dentro de la carpeta `views` del módulo, como un archivo `.php`. Por ejemplo creamos la vista llamada `bar.php`:
~~~
<html>
    <body>
        <h1>Título principal</h1>
        
        <h2>Título secundario</h2>
        
        <p>Esto es un párrafo que se puede leer</p>
    </body>
</html>
~~~
Agregamos al controlador la acción que cargará esta vista:
~~~

    // Este método renderizará la vista usando View::render()
    public function booVista()
    {
        \Pan\View::render('bar');
    }
    
    // Este método renderizará la vista usando $this->view->render()
    public function booVista2()
    {
        $this->view->render('bar');
    }    
    
~~~
Al renderizar una vista que es un archivo `php`, no es necesario agregarle la extensión cuando se vaya a mostrar.

Si se quieren cargar datos en la vista, se debe ocupar `View::set(variable_vista, valor)` o `$this->view->set(variable_vista, valor)`, en donde `variable_vista` es el nombre de la variable dentro de la vista, y `valor` es el valor que se mostrará:
~~~
    
    public function booVista()
    {
        \Pan\View::set('tituto_principal', 'Título Principal');
        \Pan\View::render('bar');
    }
    
~~~
Y en la vista quedaría:
~~~
<html>
    <body>
        <h1><?php echo $titulo_principal?></h1>
        
        <h2>Título secundario</h2>
        
        <p>Esto es un párrafo que se puede leer</p>
    </body>
</html>
~~~
También en el controlador se da la posibilidad de agregar código Javascript o CSS, tanto cargando la ruta de un archivo, o un trozo de código:
~~~
    public function booVista()
    {
        // se supone que el archivo se encuentra dentro de la carpeta assets/js del mismo modulo
        \Pan\View::addJS('archivo.js');
        
        // se supone que el archivo se encuentra dentro de la carpeta assets/css del mismo modulo
        \Pan\View::addCSS('estilos.css');
        
        \Pan\View::render('bar');
    }

~~~

### Archivos de configuración y base de datos
Los archivos de configuración y base de datos se definen dentro de la carpeta `app`, y se crean automáticamente al crear proyecto con comando de `migas`. Estos son: 
- `app_config.php` : contiene un array asociativo con distintas opciones que son necesarias para la ejecución de la aplicación. Si es necesario, se pueden agregar otras opciones personalizadas creando nuevos índices asociativos al array.
- `app_database.php` : contiene definiciones de constantes para las credenciales de conexión a base de datos, si la aplicación lo necesita.

### Trabajar con Entity en el proyecto
Con `migas` creamos un Entity para nuestro proyecto:
~~~
php migas entity::Test/Usuarios table::usuarios pk::id
~~~

Con esto, creamos la entity Usuarios, dentro de la carpeta `entities` del módulo `Test`
~~~
namespace App\Test\Entity;

class Usuarios extends \pan\Entity{

 	protected $table = 'usuarios';

	protected $primary_key = 'id';

}
~~~

Para usar la entity dentro del controlador, lo podemos hacer de la siguiente manera, creando una instancia de su clase:
~~~
public function barVista()
{
    $usuarios = new \App\Test\Entity\Usuarios();
    
    // resto de codigo
}
~~~

Las Entity poseen métodos predefinidos que ayudan a trabajar con las clases creadas para consultar y/o modificar datos de una tabla de la base de datos. Estos métodos se verán más adelante.


    

