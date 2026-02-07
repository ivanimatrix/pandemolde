<?php

namespace Pan\Db;



class DbConexion
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


    private $connection_start_time;
    private $connection_time;
    private $thresholds = [
        'CONNECTION' => 200,
        'PREPARE' => 50
    ];

    public function __construct()
    {
    }

    public function prepareQuery($query)
    {
        //return self::$instance->prepare($query);
        //return $this->conn->prepare($query);

        $start_time = microtime(true);
        
        try {
            $stmt = $this->conn->prepare($query);
            $execution_time = (microtime(true) - $start_time) * 1000;
            $this->logPerformance('PREPARE', $execution_time, $query);
            return $stmt;
        } catch (\Exception $e) {
            $execution_time = (microtime(true) - $start_time) * 1000;
            $this->logError('PREPARE_ERROR', $e->getMessage(), $execution_time);
            throw $e;
        }
    }

    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }


    public static function initConn($arr_conexion, $connection)
    {
        $connection_start_time = microtime(true);
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
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_TYPE no definido para ' . $connection);
        }
        if (!isset($arr_conexion['DB_HOST']) or $arr_conexion['DB_HOST'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_HOST no definido para ' . $connection);
        }
        if (!isset($arr_conexion['DB_PORT']) or $arr_conexion['DB_PORT'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_PORT no definido para ' . $connection);
        }
        if (!isset($arr_conexion['DB_NAME']) or $arr_conexion['DB_NAME'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_NAME no definido para ' . $connection);
        }
        if (!isset($arr_conexion['DB_USER']) or $arr_conexion['DB_USER'] === '') {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: DB_USER no definido para ' . $connection);
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
                //self::$instance =  new \PDO($conn_string, $db_user, $db_pass, $conn_options);
                $pdo = new \PDO($conn_string, $db_user, $db_pass, $conn_options);

                // Creamos instancia de DbConexion
                $obj = new self();
                $obj->conn = $pdo;

                self::$instance = $obj;
            }

            $connection_time = (microtime(true) - $connection_start_time) * 1000;
            self::$instance->logPerformance('CONNECTION', $connection_time, $db_host);

            return self::$instance;

        } catch (\PDOException $e) {
            $connection_time = (microtime(true) - $connection_start_time) * 1000;
            $tmp = new self();
            $tmp->logError('CONNECTION_ERROR', $e->getMessage(), $connection_time);

            \Pan\Utils\ErrorPan::_showErrorAndDie(__CLASS__ . ' : ' . $e->getMessage());
        } catch (\Exception $e) {
            $connection_time = (microtime(true) - $connection_start_time) * 1000;
            $tmp = new self();
            $tmp->logError('CONNECTION_ERROR', $e->getMessage(), $connection_time);

            \Pan\Utils\ErrorPan::_showErrorAndDie(__CLASS__ . ' : ' . $e->getMessage());
        }
    }


    public function closeConn()
    {
        $this->conn = null;
    }


    /**
     * Log simple de performance
     */
    private function logPerformance($type, $time_ms, $extra = '') {
        $threshold = $this->thresholds[$type] ?? 100; // si no existe el tipo, usa 100ms como fallback

        if ($time_ms >= $threshold) { // solo log de conexiones lentas
            $log_type = ($time_ms > 5000) ? "CRITICAL_{$type}" : $type;

            $log_data = date('Y-m-d H:i:s') . " | {$log_type} | " . round($time_ms, 2) . "ms";
            if ($extra) {
                $log_data .= " | " . substr(str_replace(["\r", "\n"], ' ', $extra), 0, 100);
            }
            $log_data .= " | " . ($_SERVER['REQUEST_URI'] ?? 'CLI') . " | " . ($_SERVER['REMOTE_ADDR'] ?? 'localhost');

            $log_file = dirname(__DIR__, 2) . '/tmp/logs/db_' . date('Y-m-d') . '.log';
            file_put_contents($log_file, $log_data . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Log de errores
     */
    private function logError($type, $message, $time_ms) {
        $log_data = date('Y-m-d H:i:s') . " | ERROR_{$type} | " . round($time_ms, 2) . "ms | " . $message;
        $log_data .= " | " . ($_SERVER['REQUEST_URI'] ?? 'CLI') . " | " . ($_SERVER['REMOTE_ADDR'] ?? 'localhost');
        
        $log_file = dirname(__DIR__, 2) . '/tmp/logs/db_' . date('Y-m-d') . '.log';
        file_put_contents($log_file, $log_data . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
