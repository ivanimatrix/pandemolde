<?php

namespace Pan\Utils;


class panSession
{

    private static $session_id;

    public static $instance;

    public function __construct()
    {
        session_start();

        self::$session_id = str_replace(' ', '', mb_strtolower(App::getName()));
        self::$instance = $this;
    }


    private static function is_session_started()
    {
        if ( php_sapi_name() !== 'cli' ) {
            if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }


    public static function get()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function start()
    {
        if ( self::is_session_started() === FALSE ) session_start();

        $session_id = str_replace(' ', '', mb_strtolower(App::getName()));
        $_SESSION[$session_id] = array();
    }


    public static function sessionKill($param=null)
    {
        $session_id = str_replace(' ', '', mb_strtolower(App::getName()));
        if($param){
            unset($_SESSION[$session_id][$param]);
        }else{
            session_destroy();
        }
        
    }


    public static function setSession($param, $value)
    {
        $session_id = str_replace(' ', '', mb_strtolower(App::getName()));
        if ( self::is_session_started() === TRUE ){
            self::get();
        }

        $_SESSION[$session_id][$param] = $value;
    }


    public static function getSession($param)
    {
        $session_id = str_replace(' ', '', mb_strtolower(App::getName()));
        if (isset($_SESSION[$session_id][$param])) {
            return $_SESSION[$session_id][$param];
        } else {
            return null;
        }
    }


    public static function isValidate()
    {
        $session_id = str_replace(' ', '', mb_strtolower(App::getName()));
        if (!isset($_SESSION[$session_id])) {
            header('Location: ' . Uri::getHost());
            die();
        }

    }

}