<?php

namespace Pan\Kore;


class App {

    public static $environment;

    public static $version;

    public static $name;

    public static $default_module;

    public static $default_controller;

    public static $default_action;

    public static $default_template;

    public static $is_production;

    public static $charset = 'UTF-8';

    public static $salt = '';

    public static $view_debug = false;

    private static $http_protocol;

    private static $db_audit;

    private static $_app_config;

    

    public function __construct($app_config)
    {

        self::$environment = $app_config['app_environment'];
        self::$name = $app_config['app_name'];
        self::$version = $app_config['app_version'];
        self::$default_module = $app_config['app_default_module'];
        self::$default_controller = $app_config['app_default_controller'];
        self::$default_action = $app_config['app_default_action'];
        self::$is_production = $app_config['app_is_production'];
        self::$default_template = $app_config['app_default_template'];
        self::$salt = $app_config['app_salt'];
        self::$view_debug = $app_config['app_view_debug'];
        self::$http_protocol = isset($app_config['app_http_protocol']) ? $app_config['app_http_protocol'] : 'http';
        self::$db_audit = $app_config['app_db_audit'];
        self::$_app_config = $app_config;
    }


    /**
     * @throws \Exception
     * @return mixed
     */
    public function init()
    {
        try {
            return \Pan\Kore\Bootstrap::run();
        } catch (\Exception $e) {
            if (self::$view_debug == true) {
                \Pan\Utils\ErrorPan::_showErrorAndDie($e->getMessage());
            }

            if (is_dir('tmp/logs/') and is_writable('tmp/logs/')) {
                error_log("\n" . date('Y-m-d H:i:s') . " " . $e->getMessage(), 3, 'tmp/logs/error_log_' . date('Ymd') . '.log');
            } else {
                error_log(\Pan\Kore\App::getName() . " " . $e->getMessage());
            }

            //error_log($e->getMessage(), 3, 'tmp/logs/error_log_' . date('Ymd') . '.log');
            die;
        }
    }


    public static function get($item)
    {
        if (isset(self::$_app_config[$item]))
            return self::$_app_config[$item];

        return null;
    }

    public static function getName()
    {
        return self::$name;
    }

    public static function getDefaultModule()
    {
        return self::$default_module;
    }


    public static function getDefaultController()
    {
        return self::$default_controller;
    }


    public static function getDefaultAction()
    {
        return self::$default_action;
    }


    public static function getCharset()
    {
        return self::$charset;
    }


    public static function getDefaultTemplate()
    {
        return self::$default_template;
    }

    public static function getSalt()
    {
        return self::$salt;
    }

    public static function getDebugView()
    {
        return self::$view_debug;
    }


    public static function getHttpProtocol()
    {
        return self::$http_protocol;
    }


    public static function getEnvironment()
    {
        return self::$environment;
    }


    public static function getDbAudit()
    {
        return self::$db_audit;
    }

}