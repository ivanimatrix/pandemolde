<?php

namespace pan\db\connection;

class DBConnection
{

    protected $dbtype;

    protected $dbhost;

    protected $dbport;

    protected $dbname;

    protected $dbuser;

    protected $dbpass;

    protected $dbcharset;

    protected $dbprefix;

    private $dbconn;

    private static $instance = array();

    private $arr_conn;

    protected function __construct($arr_conexion)
    {
        
        if (!is_array($arr_conexion) or is_null($arr_conexion)) {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: No se ha indicado el arreglo de conexion');
        }

        if (!isset($arr_conexion['DB_TYPE']) or $arr_conexion['DB_TYPE'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_TYPE no definido para conexion');
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

        $this->dbtype = $arr_conexion['DB_TYPE'];
        $this->dbhost = $arr_conexion['DB_HOST'];
        $this->dbport = $arr_conexion['DB_PORT'];
        $this->dbname = $arr_conexion['DB_NAME'];
        $this->dbuser = $arr_conexion['DB_USER'];
        $this->dbpass = $arr_conexion['DB_PASS'];
        $this->dbcharset = $arr_conexion['DB_CHARSET'];

        $this->arr_conn = $arr_conexion;
    }


    public function initConn()
    {
        $conn_string = mb_strtolower($this->dbtype) . ':host=' . $this->dbhost . ';port=' . $this->dbport . ';dbname=' . $this->dbname;
        $conn_options = array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        );

        if (mb_strtolower($this->dbtype) === 'mysql')
            $conn_options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES '" . $this->dbcharset . "'";

        try {

            $returnValue = null;
            $class = get_called_class();

            if (!isset(self::$instance[$class])) {
                self::$instance[$class] = new \PDO($conn_string, $this->dbuser, $this->dbpass, $conn_options);
                $returnValue = self::$instance[$class];
                echo 'nueva instancia ' . $class;
            } else {
                $returnValue = self::$instance[$class];
                echo ' misma instancia ' . $class;
            }
            $this->dbconn = $returnValue;
            
            return $returnValue;

        } catch (\PDOException $e) {
            \Pan\Utils\ErrorPan::_showErrorAndDie(__CLASS__ . ' : ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            \Pan\Utils\ErrorPan::_showErrorAndDie(__CLASS__ . ' : ' . $e->getMessage());
            return null;
        }
    }

    
}
