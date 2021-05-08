<?php
namespace Pan\Kore;

use Pan\Kore\App as App;

class Request
{
    private static $_grupo;
    private static $_modulo;
    private static $_controlador;
    private static $_metodo;
    private static $_parametros;

    public function __construct()
    {
        
        if (isset($_GET['url'])) {
            $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            $url = array_filter($url);

            self::$_controlador = array_shift($url);
            self::$_metodo = array_shift($url);
            self::$_parametros = $url;
        } else {
            
            if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) {

                $url = explode('/index.php', $_SERVER['REQUEST_URI']);
                $query_string = array_pop($url);
                if (empty($query_string)) {
                    self::$_modulo = App::getDefaultModule();
                    self::$_controlador = App::getDefaultController();
                    self::$_metodo = App::getDefaultAction();
                } else {
                    $url = explode("/", trim($query_string, '/'));
                    $tmp_module = array_shift($url);
                    if (strpos($tmp_module,'@')  !== false) {
                        $group = explode('@', $tmp_module);
                        self::$_grupo = $group[1];
                        self::$_modulo = $group[0];
                    } else {
                        self::$_modulo = $tmp_module;
                    }

                    self::$_controlador = array_shift($url);
                    self::$_metodo = array_shift($url);
                    self::$_parametros = $url;
                }

            } else {
                
                //$url = trim($_SERVER['REQUEST_URI'], "/");
                $url_explode = array();
                if (isset($_SERVER['PATH_INFO'])) {
                    $url_explode = explode('/', trim($_SERVER['PATH_INFO'],'/'));
                }
                
                //$url_explode = explode('/', $url);
                //array_shift($url_explode);

                if (count($url_explode) > 0) {
                    $tmp_module = array_shift($url_explode);

                    if (strpos($tmp_module,'@') !== false) {
                        $group = explode('@', $tmp_module);
                        self::$_grupo = $group[1];
                        self::$_modulo = $group[0];
                    } else {
                        self::$_modulo = $tmp_module;
                    }

                    self::$_controlador = array_shift($url_explode);
                    self::$_metodo = array_shift($url_explode);
                    self::$_parametros = $url_explode;
                } else {
                    self::$_modulo = App::getDefaultModule();
                    self::$_controlador = App::getDefaultController();
                    self::$_metodo = App::getDefaultAction();
                }

            }


        }

        if (!self::$_modulo) {
            self::$_modulo = App::getDefaultModule();
        }

        if (!self::$_controlador) {
            self::$_controlador = App::getDefaultController();
        }

        if (!self::$_metodo) {
            self::$_metodo = App::getDefaultAction();
        }

        if (!isset(self::$_parametros)) {
            self::$_parametros = array();
        }

    }


    public static function getGrupo()
    {
        return self::$_grupo;
    }

    public static function getModulo()
    {   
        return self::$_modulo;
    }

    public static function getControlador()
    {
        return self::$_controlador;
    }

    public static function getMetodo()
    {
        return self::$_metodo;
    }

    public static function getParametros($parametro = null)
    {
        $_post_params = array();

        if(isset($_POST)){
            foreach($_POST as $request_key => $request_value){
               $_post_params[$request_key] = $request_value;
               //unset($_POST[$request_key]);
            }
        }

        if($parametro){
            return $_post_params[$parametro];
        }
        return $_post_params;
    }

    public static function _getParameters()
    {
        return self::$_parametros;
    }

    public static function getFiles($parametro = null)
    {
        if(isset($_FILES)){
            if($parametro){
                return $_FILES[$parametro];
            }else{
                return $_FILES;
            }
        }
        return null;
    }

    public function __toString()
    {
        var_dump($this);
    }

    /**
     * Valida si una petición es realizada por AJAX
     *
     * @return bool TRUE si la petición se ha hecho median AJAX
     */
    public function isAjax()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return false;
    }


}


?>