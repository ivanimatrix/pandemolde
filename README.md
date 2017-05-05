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
Crea la estructura completa para comenzar a desarrollar el proyecto. Si ya se ha creado anteriormente, todo el contenido será borrado.

### Crear Módulo
~~~
$ php migas module::NOMBRE_MODULO

~~~
Se crea un módulo con el nombre puesto en NOMBRE_MODULO

### Crear Controlador
~~~
$ php migas controller::NOMBRE_MODULO/NOMBRE_CONTROLADOR

~~~
Crea un controlador llamado NOMBRE_CONTROLADOR dentro del módulo NOMBRE_MODULO

### Crear Entidad
~~~
$ php migas entity::NOMBRE_MODULO/NOMBRE_ENTIDAD

~~~
Crea una entidad llamanda NOMBRE_ENTIDAD dentro del módulo NOMBRE_MODULO