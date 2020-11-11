## \Pan\Kore\Request::class 
+ `Request::getParametros($parametro) : mixed` : Obtener los parámetros que se envían por POST de una petición.
    + `$parametro : string` : Opcional. Si no se define `$parametro`, se retorna un arreglo asociativo correspondiente a todos los parámetros enviados. Si se define `$parametro`, se retorna el valor que corresponde al parámetro solicitado.
    
+ `Request::getModulo() : string` : Retorna nombre del módulo.

+ `Request::getControlador() : string` : Retorna nombre del controlador.

+ `Request::getMetodo() : string` : Retorna nombre del método o acción solicitado.

+ `Request::getFiles($file) : string` : Obtener los ficheros que se han enviado en la petición.
    + `$file : string` : Opcional. Si no se define, se retorna el arreglo de `$_FILES` de `php`. Si se define, retorna el arreglo de datos del fichero correspondiente. 