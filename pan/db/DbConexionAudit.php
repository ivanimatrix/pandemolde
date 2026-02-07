<?php

namespace Pan\Db;

class DbConexionAudit  {

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

    private function __construct() {

    }

    public function prepareQuery($query) {
        return self::$instance->prepare($query);
        //return $this->conn->prepare($query);
    }

    public function lastInsertId(){
        return $this->conn->lastInsertId();
    }


    public static function initConn()
    {
        /* $this->db_type = DB_TYPE;
        $this->db_host = DB_HOST;
        $this->db_port = DB_PORT;
        $this->db_name = DB_NAME;
        $this->db_user = DB_USER;
        $this->db_pass = DB_PASS;
        $this->db_charset = DB_CHARSET; */

        $conn_string = mb_strtolower(DB_TYPE_AUDIT) . ':host=' . DB_HOST_AUDIT . ';port=' . DB_PORT_AUDIT . ';dbname=' . DB_NAME_AUDIT;
        $conn_options = array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        );

        if (mb_strtolower(DB_TYPE_AUDIT) === 'mysql')
            $conn_options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES '" . DB_CHARSET_AUDIT . "'";

        try {
            
            
            if (is_null(self::$instance)) {
                self::$instance =  new \PDO($conn_string, DB_USER_AUDIT, DB_PASS_AUDIT, $conn_options);
                //error_log('nueva instancia DB');
            } else {
                //error_log('se ocupa la misma instancia');
            }
            
            return self::$instance;
            
            //$this->conn = new \PDO($this->conn_string, $this->db_user, $this->db_pass, $this->conn_options);

            /* error_log('nueva instancia DB');
            return new \PDO($conn_string, DB_USER, DB_PASS, $conn_options); */

        } catch (\PDOException $e) {
            \Pan\Utils\ErrorPan::_showErrorAndDie($e->getMessage());
        } catch (\Exception $e) {
            \Pan\Utils\ErrorPan::_showErrorAndDie($e->getMessage());
        }
    }


    public function closeConn()
    {
        $this->conn = null;
    }

}