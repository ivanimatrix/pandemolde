<?php

namespace Connections;



class DBFormacion
{

    private $conn = null;
    private $conn_string = '';
    private $conn_options;
    private $query;
    private $params;

    private static $instance = null;

    private $db_type;
    private $db_host;
    private $db_port;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_charset;

    public function __construct()
    {
    }

    public function prepareQuery($query)
    {
        return self::$instance->prepare($query);
        //return $this->conn->prepare($query);
    }

    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }


    public static function initConn($arr_conexion)
    {
        
        if (!is_array($arr_conexion) or is_null($arr_conexion)) {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: No se ha indicado el arreglo de conexion');
        }
        /* $this->db_type = DB_TYPE;
        $this->db_host = DB_HOST;
        $this->db_port = DB_PORT;
        $this->db_name = DB_NAME;
        $this->db_user = DB_USER;
        $this->db_pass = DB_PASS;
        $this->db_charset = DB_CHARSET; */


        if (!isset($arr_conexion['DB_TYPE']) or $arr_conexion['DB_TYPE'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_TYPE no definido');
        }
        if (!isset($arr_conexion['DB_HOST']) or $arr_conexion['DB_HOST'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_HOST no definido');
        }
        if (!isset($arr_conexion['DB_PORT']) or $arr_conexion['DB_PORT'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_PORT no definido');
        }
        if (!isset($arr_conexion['DB_NAME']) or $arr_conexion['DB_NAME'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_NAME no definido');
        }
        if (!isset($arr_conexion['DB_USER']) or $arr_conexion['DB_USER'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_USER no definido');
        }

        $db_type = $arr_conexion['DB_TYPE'];
        $db_host = $arr_conexion['DB_HOST'];
        $db_port = $arr_conexion['DB_PORT'];
        $db_name = $arr_conexion['DB_NAME'];
        $db_user = $arr_conexion['DB_USER'];
        $db_pass = $arr_conexion['DB_PASS'];
        $db_charset = $arr_conexion['DB_CHARSET'];

        $conn_string = mb_strtolower($db_type) . ':host=' . $db_host . ';port=' . $db_port . ';dbname=' . $db_name;
        $conn_options = array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        );

        if (mb_strtolower($db_type) === 'mysql')
            $conn_options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES '" . $db_charset . "'";

        try {
            
            if (!self::$instance) {
                self::$instance =  new \PDO($conn_string, $db_user, $db_pass, $conn_options);
            }

            return self::$instance;

        } catch (\PDOException $e) {
            \Pan\Utils\ErrorPan::_showErrorAndDie(__CLASS__ . ' : ' . $e->getMessage());
        } catch (\Exception $e) {
            \Pan\Utils\ErrorPan::_showErrorAndDie(__CLASS__ . ' : ' . $e->getMessage());
        }
    }


    public function closeConn()
    {
        $this->conn = null;
    }
}
