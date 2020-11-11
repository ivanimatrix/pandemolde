# \Pan\Kore\View::class
+ `View::render([$path_template][, $arr_data][, $full_path])` : Renderiza vistas.
    + `$path_template : string` : Opcional. Carga las vista indicada por `$path_template` que se encuentra dentro de la carpeta `views` del módulo correspondiente. Si no se pasa parámetro, se renderizará la vista definida por defecto en `$app_config['app_default_template']`. Si la vista no existe, habrá error 404.
    + `$arr_data : array` : Opcional. Arreglo con los valores de las variables que serán visualizadas en el template. 
    + `$full_path : string` : Opcional. Indica si se debe ocupar la ruta completa del template a renderizar. Esto puede servir para cargar templates de otros módulos o vistas que se encuentren de manera independiente.
    
+ `View::set($var_template, $val)` : Asigna valores a las variables que se definan dentro de las vistas:
    + `$var_template : string` : Nombre de la variable en la vista
    + `$val : mixed` : Valor asignado a `$var_template`
    
+ `View::addCSS($css[, $path_css])` : Inserta dentro de la vista código CSS. Sus parámetros son:
    + `$css : string` : puede corresponder a 3 opciones:
        + url directa de un archivo css (externo)
        + nombre (ruta) de un archivo css que se encuentra dentro del módulo correspondiente, en la carpeta `assets/css`
        + código CSS (similar al usado dentro de etiqueta `<style>` de HTML)
    + `$path_css : string` : Opcional. Indica la ruta relativa de archivos css que se alojan en la carpeta `pub/css` para uso general.
    
+ `View::addJS($js, [$path_js])` : Inserta dentro de la vista código Javascript. Sus parámetros son:
    + `$js : string` : puede corresponder a 3 opciones:
        + url directa de un archivo js (externo)
        + nombre (ruta) de un archivo js que se encuentra dentro del módulo correspondiente, en la carpeta `assets/js`
        + código Javascript (similar al usado dentro de etiqueta `<script>` de HTML)
    + `$path_js : string` : Opcional. Indica la ruta relativa de archivos js que se alojan en la carpeta `pub/css` para uso general, o también dentro de algún módulo en específico.

+ `View::fetchIt($path_template [, $data [, $module]]) : string` : Procesa el contenido de una vista para guardarla y usarla como variable.
    + `$path_template : string` : Ruta de la vista que se desea procesar. Por defecto, se asume que la vista se encuentra dentro de la carpeta `views` del módulo
    + `$data : array` : Opcional. Corresponde a un arreglo asociativo con los nombres de las variables y sus valores que pertenecen a la vista a procesar.
    + `$module : string` : Opcional. Nombre del módulo si la vista a procesar pertenece a ese módulo. Se asume de igual manera que la vista se encuentra en la carpeta `views` de ese módulo.