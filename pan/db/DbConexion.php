<?php

namespace Pan\Db;

class DbConexion  {

    private $conn;
    private $conn_string = '';
    private $conn_options;
    private $query;
    private $params;

    private $db_type;
    private $db_host;
    private $db_port;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_charset;

    public function __construct($db_type=DB_TYPE, $db_host=DB_HOST, $db_port=DB_PORT, $db_name=DB_NAME, $db_user=DB_USER,$db_pass=DB_PASS,$db_charset=DB_CHARSET) {

        $this->db_type = $db_type;
        $this->db_host = $db_host;
        $this->db_port = $db_port;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_charset = $db_charset;

    }

    public function prepareQuery($query) {
        return $this->conn->prepare($query);
    }

    public function lastInsertId(){
        return $this->conn->lastInsertId();
    }


    public function initConn()
    {
        $this->conn_string = mb_strtolower($this->db_type) . ':host=' . $this->db_host . ';port=' . $this->db_port . ';dbname=' . $this->db_name;
        $this->conn_options = array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        );

        if (mb_strtolower($this->db_type) === 'mysql')
            $this->conn_options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES '" . $this->db_charset . "'";

        try {
            $this->conn = new \PDO($this->conn_string, $this->db_user, $this->db_pass, $this->conn_options);

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