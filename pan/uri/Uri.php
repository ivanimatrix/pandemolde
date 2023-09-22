<?php
namespace Pan\Uri;

class Uri
{

    private static $_port;




    public function __construct()
    {
        self::$_port = '';
        if ($_SERVER['SERVER_PORT'] != 80) {
            self::$_port = ':' . $_SERVER['SERVER_PORT'];
        }
    }


    public static function getHost()
    {
        self::$_port = '';
        if ($_SERVER['SERVER_PORT'] != 80) {
            self::$_port = ':' . $_SERVER['SERVER_PORT'];
        }
        $url	= $_SERVER['SERVER_NAME'] . self::$_port . $_SERVER['SCRIPT_NAME'];
        $url	= explode('/', $url);
        $tmp	= array_pop($url);
        $url	= implode("/", $url);

        return self::getUriProtocol() . $url . '/';
    }


    /**
     * Obtiene la base de la url de la aplicación
     *
     * @return string Base de url
     */
    public static function getBaseUri()
    {
        
        self::$_port = '';
        if ($_SERVER['SERVER_PORT'] != 80) {
            self::$_port = ':' . $_SERVER['SERVER_PORT'];
        }
        $url = $_SERVER['SERVER_NAME'] . self::$_port . $_SERVER['SCRIPT_NAME'];
        $url = explode('/', $url);
        $tmp = array_pop($url);
        $url = implode("/", $url);

        return self::getUriProtocol() . $url . '/';
        //return $_SERVER['SERVER_NAME'];

    }



    /**
     * Obtener protocolo HTTP o HTTPS según url
     *
     * @return string Protocolo 'https://' o 'http://';
     */
    private static function getUriProtocol() 
    {
        return \Pan\Kore\App::getHttpProtocol() . '://';
        //return  (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    }

}