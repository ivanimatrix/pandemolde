<?php
namespace Pan\Kore;

class View
{

    static private $data = array();
    static private $dir_views = 'views';
    static private $js_code = '';
    static private $css_code = '';


    /**
     * Renderiza una vista(template)
     * @param null $template string Nombre del fichero de la vista. Si no se especifica, se toma fichero declarado por defecto en App::setDefaultTemplate()
     * @param null $arr_data array Arreglo de datos que seran procesados para ser utilizados en la vista a renderizar
     * @throws \Exception
     */
    public static function render($template = null, $arr_data = null, $full_path = false)
    {

        if(!empty($template) or !is_null($template)){
            if(!preg_match('/\.php/i',$template)){
                $template .= '.php';
            }

            if ($full_path) {
                $_template = $template;
            } else {
                $_template = 'app' . DIRECTORY_SEPARATOR . Request::getModulo() . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
                if (!empty(Request::getGrupo())) {
                    $_template = 'app' . DIRECTORY_SEPARATOR . Request::getGrupo() .DIRECTORY_SEPARATOR . Request::getModulo() . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
                }
            }
            

            if (!is_file($_template))
                throw new \Exception('vista no encontrada');

            if (!is_null($arr_data) and is_array($arr_data)) {
                if (count(self::$data) == 0) {
                    self::$data = $arr_data;
                } else {
                    foreach ($arr_data as $k => $v) {
                        self::set($k, $v);
                    }
                }
            }    
        }else{
            if(!empty(App::getDefaultTemplate())){
                $_template = App::getDefaultTemplate();
            }else{
                throw new \Exception('No se ha definido un template por defecto. ');                
            }
        }

        if (is_array(self::$data) and count(self::$data) > 0)
            extract(self::$data);

        require $_template;

        echo self::$js_code;
    }

    /**
     * Procesar una vista para obtener su contenido
     * @param $template
     * @param null $arr_data
     * @param null $other_module
     * @return string
     */
    public static function fetchIt($template, $arr_data = null, $other_module = null)
    {
        ob_start();

        if (!is_null($arr_data) and is_array($arr_data)) {
            if (count(self::$data) == 0) {
                self::$data = $arr_data;
            } else {
                foreach ($arr_data as $k => $v) {
                    self::set($k, $v);
                }
            }
        }

        if (is_array(self::$data) and count(self::$data) > 0)
            extract(self::$data);

        if(!preg_match('/\.php/i',$template)){
            $template .= '.php';
        }

        if(!is_null($other_module)){
            $_template = 'app' . DIRECTORY_SEPARATOR . $other_module . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
        }else{
            $_template = 'app' . DIRECTORY_SEPARATOR . Request::getModulo() . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
            if (!empty(Request::getGrupo())) {
                $_template = 'app' . DIRECTORY_SEPARATOR . Request::getGrupo() . DIRECTORY_SEPARATOR . Request::getModulo() . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
            }
        }

        //$_template = 'app' . DIRECTORY_SEPARATOR . \pan\Request::getModulo() . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;

        require $_template;

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }


    /**
     * Asignar un valor a una variable para la vista
     * @param $name string Nombre de la variable en la vista
     * @param $val mixed Valor que $name debe tener en la vista
     */
    public static function set($name, $val)
    {
        //$this->data[$name] = $val;
        self::$data[$name] = $val;
    }


    /**
     * Agrega codigo CSS a la vista
     * @param $css
     */
    public static function addCSS($css)
    {
        if (!filter_var($css, FILTER_VALIDATE_URL) === false) {
            echo '<link type="text/css" href="' . $css . '" rel="stylesheet"/>';
        }else{
            $path = "app/" . Request::getModulo() . "/assets/css/" . $css;
            if (!empty(Request::getGrupo())) {
                $path = "app/" . Request::getGrupo() . "/" . Request::getModulo() . "/assets/css/".$css;
            }
            if (is_file($path) and is_readable($path)) {
                $css_content = file_get_contents($path);
                if (defined('ENVIRONMENT') and ENVIRONMENT == 'PROD') {
                    $css = str_replace('.css', '.min.css', $css);
                }

                $css_content = preg_replace("/[\n|\r|\n\r]/i","",$css_content);
                //$ccs_content = \pan\panMinify::minCSS($css_content);
                //echo '<link href="' . \pan\Uri::getHost() . $css . '?'.sha1($css.uniqid()).'" type="text/css" rel="stylesheets" />';
            } else {
                $css_content = $css;
            }
            echo '<style type="text/css">' . $css_content . '</style>';
        }

    }

    /**
     * Agrega codigo Javascript a la vista
     * @param $javascript
     * @param null $dir
     */
    public static function addJS($javascript, $dir = null)
    {

        if (!filter_var($javascript, FILTER_VALIDATE_URL) === false) {
            self::$js_code .= '<script type="text/javascript" src="'.$javascript.'" charset="' . App::getCharset() . '"></script>';
        } else {
            if(!is_null($dir)){
                $dir = trim($dir, DS);
                if (is_file($dir . DS . $javascript) and is_readable($dir . DS . $javascript)) {
                    $js_content = file_get_contents($dir . DS .$javascript);

                    self::$js_code .= "\n" . '<script type="text/javascript" charset="' . App::getCharset() . '">' . "\n" . $js_content . "\n" . '</script>' . "\n";

                } else {
                    self::$js_code .= "\n" . '<script type="text/javascript" charset="' . App::getCharset() . '">' . "\n" . 'console.error("Archivo '.$javascript.' no se ha cargado");'. "\n" . '</script>' . "\n";
                }
            }else{
                $path_js = "app/" . Request::getModulo() . "/assets/js/" . $javascript;
                
                if (!empty(Request::getGrupo())) {
                    $path_js = "app/" . Request::getGrupo() . "/" . Request::getModulo() . "/assets/js/" . $javascript;
                }

                if (is_file($path_js) and is_readable($path_js)) {
                    $js_content = file_get_contents($path_js);

                    self::$js_code .= "\n" . '<script type="text/javascript" charset="' . App::getCharset() . '">' . "\n" . $js_content . "\n" . '</script>' . "\n";

                } else {
                    self::$js_code .= "\n" . '<script type="text/javascript" charset="' . App::getCharset() . '">' . $javascript . ';</script>' . "\n";
                }
            }
        }

    }

}