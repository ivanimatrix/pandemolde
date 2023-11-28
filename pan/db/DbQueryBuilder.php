<?php

namespace Pan\Db;


class DbQueryBuilder
{

    protected $db;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $dbname;

    protected $table;

    private $string_query;

    private $select;

    private $from;

    private $orderBy;

    private $groupBy;

    private $order;

    private $where;

    private $limit;

    private $join;

    private $result;

    private $num_rows;

    private $query;

    private $params;

    private $conn;

    public function __construct($connection = null)
    {

        require 'app' . DIRECTORY_SEPARATOR . 'app_database.php';

        if (is_null($connection) or !isset($app_database[$connection])) {
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE: No se ha definido conexión');
        }

        $this->conn = $connection;

        $file_connection = 'connections' . DIRECTORY_SEPARATOR . 'DB' .  ucfirst($connection) . '.php';
        if (!is_file($file_connection))
            \Pan\Utils\ErrorPan::_showErrorAndDie('ERROR DATABASE : Archivo de conexion para ' . $connection . ' no encontrado');


        require_once $file_connection;
        
        $dbconnection = '\\connections\\DB' . ucfirst($connection);
        $db_class = new $dbconnection($app_database[$connection]);
        $this->db = call_user_func(array($db_class, 'initConn'), array($app_database[$connection]));
    }


    public function fields($fields = null)
    {
        if (is_null($fields)) {
            \Pan\Utils\ErrorPan::_showErrorAndDie('Se deben ingresar los campos que se desean seleccionar: ' . $this->query);
        }

        if ($this->query === "")
            $this->query = "select ";

        if (\Pan\Utils\ValidatePan::isArray($fields)) {
            $fields_name = '';
            foreach ($fields as $field) {
                $fields_name .= $field . ', ';
            }
            $fields = trim($fields_name, ', ');
        }
        $this->query = str_replace('*', $fields, $this->query);
        return $this->getQuery($this->query, $this->params);
    }


    public function select($fields = null)
    {

        if ($this->select == "")
            $this->select = "SELECT ";


        if (\Pan\Utils\ValidatePan::isArray($fields)) {
            $fields_name = '';
            foreach ($fields as $field) {
                $fields_name .= $field . ', ';
            }
            $fields = trim($fields_name, ', ');
            $this->select .= $fields . ' ';
        } elseif (is_string($fields)) {
            $this->select .= $fields . ' ';
        } else {
            $this->select .= ' * ';
        }
    }


    public function conditions($conditions = null)
    {
        if (!is_null($conditions)) {
            $this->query .= ' where ';
            if (is_array($conditions)) {
                $parameters = array();
                foreach ($conditions as $field => $value) {
                    if (is_array($value)) {
                        $this->query .= $field . ' ' . $value[1] . ' ? and ';
                        $parameters[] = $value[0];
                    } else {
                        $this->query .= $field . ' = ? and ';
                        $parameters[] = $value;
                    }
                }
                $this->query = trim($this->query, 'and ');
                $this->params = $parameters;
            } elseif (is_string($conditions)) {
                //$this->query .= ' where ' . $conditions;
                $this->query .= $conditions;
            } elseif (is_numeric($conditions)) {
                $this->query .= $this->primary_key . ' = ?';
            }
        }
        return $this->getQuery($this->query, $this->params);
    }

    public function from($from, $alias = null)
    {
        $this->from = ' FROM ' . $from;
        if (!is_null($alias))
            $this->from .= ' ' . $alias . ' ';
    }


    public function join($table, $conditions, $position = 'LEFT')
    {
        $this->join .= ' ' . $position . ' JOIN ' . $table . ' on ' . $conditions;
    }


    public function limit($num_limit, $total = null)
    {
        require __DIR__ . '/../../app/app_database.php';
        $total_limit = '';
        if (!is_null($total))
            if (strtolower($app_database[$this->conn]['DB_TYPE']) === 'mysql')
                $total_limit = ',' . $total;
            elseif (strtolower($app_database[$this->conn]['DB_TYPE']) === 'pgsql')
                $total_limit = ' offset ' . $total;

        $this->query .= ' limit ' . $num_limit . $total_limit;
        return $this->getQuery($this->query, $this->params);
    }


    /**
     * Clausula WHERE
     * @param  string $con_logic Conector logico para clausula WHERE: AND|OR
     * @param  array $params    Arreglo con el campo involucrado en clausula WHERE, formato ['campo' => 'valor']
     * @param  string $condition Condicion para la clausula. Por defecto es '='
     */
    public function where($con_logic, $params, $condition = '=')
    {

        if (empty($this->where)) {
            $this->where = ' WHERE ';
        } else {
            $this->where .= ' ' . strtoupper($con_logic);
        }

        if (is_array($params)) {
            $parameters = array();
            foreach ($params as $field => $value) {
                $this->where .= ' ' . $field . ' ' . $condition . ' ? ';
                //$parameters[] = $value;
                $this->params[] = $value;
            }
        }
    }


    public function order($by, $order = 'ASC')
    {
        if (\Pan\Utils\ValidatePan::isArray($by)) {
            $order_query = ' order by ';
            foreach ($by as $key => $value) {
                $order_query .= $key . ' ' . $value . ', ';
            }
            $order_query = trim($order_query, ', ');
            $this->query .= ' ' . $order_query;
        } else {
            $this->query .= ' order by ' . $by . ' ' . $order;
        }

        return $this->getQuery($this->query, $this->params);
    }


    /**
     * ejecutar una sentencia SELECT
     * @param  [string] $query      [setencia SQL a ejecutar]
     * @param  [mixed] $parameters [(opcional) puede ser un array de parametros o un solo parametro]
     * @return [object]             [retorna los resultados de la sentencia como objetos]
     */
    public function getQuery($query, $parameters = null)
    {

        $this->query = $query;
        $this->params = $parameters;

        return $this;
        /*try {
            $stmt = $this->db->prepareQuery($query);
            if (!is_null($parameters)) {
                if (is_array($parameters)) {
                    $stmt->execute($parameters);
                } else {
                    $stmt->execute(array($parameters));
                }
            } else {
                $stmt->execute();
            }
            $this->num_rows = $stmt->rowCount();
            $this->result = $stmt->fetchAll();
            //return $stmt->fetchAll();
            return $this;
        } catch (\PDOException $e) {
            errorPan::_showErrorAndDie($e->getMessage());
        } catch (\Exception $e) {
            errorPan::_showErrorAndDie($e->getMessage());
        }*/
    }

    /**
     * ejecutar una sentencia DELETE, UPDATE o INSERT
     * @param  string $query sentencia SQL a ejecutar
     * @param  mixed $parameters (opcional) puede ser un array de parametros, o un solo parametro
     * @return boolean             TRUE si se ejecuto correctamente la sentencia, o de lo contrario retorna NULL
     */
    public function execQuery($query, $parameters = null, $return_last_id = false)
    {
        $this->query = $query;
        $this->params = $parameters;

        try {

            $stmt = $this->db->prepare($query);
            $tiempo_inicial = microtime(true);
            if (!is_null($parameters)) {
                if (is_array($parameters)) {
                    $stmt->execute($parameters);
                } else {
                    $stmt->execute(array($parameters));
                }
            } else {
                $stmt->execute();
            }

            $total_time = ((microtime(true) - $tiempo_inicial));
            $this->logAuditoria($total_time);

            if ($stmt->rowCount() >= 0) {
                if ($return_last_id) {
                    $return = $this->db->lastInsertId();
                } else {
                    $return = true;
                }

                return $return;
            } else {
                return null;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            \Pan\Utils\ErrorPan::_showErrorAndDie($e->getMessage() . "<br><pre>" . $query . "</pre><br>");
        } catch (\Exception $e) {
            error_log($e->getMessage());
            \Pan\Utils\ErrorPan::_showErrorAndDie($e->getMessage() . "<br><pre>" . $query . "</pre><br>");
        }
    }

    /**
     * muestra la query en formato RAW, con parametros incluidos
     * @return [string] [retorna la query formada]
     */
    public function showQuery()
    {
        $keys = array();
        $values = array();

        if (\Pan\Utils\ValidatePan::isArray($this->params)) {
            foreach ($this->params as $key => $value) {
                if (is_string($key)) {
                    $keys[] = '/:' . $key . '/';
                } else {
                    $keys[] = '/[?]/';
                }

                if (is_numeric($value)) {
                    $values[] = (int)($value);
                } else {
                    $values[] = '"' . $value . '"';
                }
            }
        } else {
            if (is_string($this->params)) {
                $keys[] = '/:' . $this->params . '/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_numeric($this->params)) {
                $values[] = (int)($this->params);
            } else {
                $values[] = '"' . $this->params . '"';
            }
        }


        $query = preg_replace($keys, $values, $this->query, 1, $count);
        return $query;
    }


    public function runQuery()
    {
        try {
            $store = new \Pan\Db\DbStore();

            if (empty($this->query) or is_null($this->query))
                $this->query = $this->select . $this->from . $this->join . $this->where . $this->orderBy . $this->groupBy . $this->limit;

            $stmt = $this->db->prepare($this->query);
            $tiempo_inicial = microtime(true);
            if (!is_null($this->params)) {
                if (is_array($this->params)) {
                    $stmt->execute($this->params);
                } else {
                    $stmt->execute(array($this->params));
                }
            } else {
                $stmt->execute();
            }
            $total_time = ((microtime(true) - $tiempo_inicial));

            $this->logAuditoria($total_time);
            $this->result = $stmt->fetchAll();
            $store->setRows($this->result);
            $store->setNumRows($stmt->rowCount());
            $store->setQueryString($this->showQuery());

            $this->query = $this->select = $this->from = $this->join = $this->where = $this->orderBy = $this->groupBy = $this->limit = "";
            $this->params = null;

            return $store;
        } catch (\PDOException $e) {
            \Pan\Utils\ErrorPan::_showErrorAndDie($e->getMessage() . "<br><pre>" . $this->showQuery() . "</pre><br>");
            return null;
        } catch (\Exception $e) {
            \Pan\Utils\ErrorPan::_showErrorAndDie($e->getMessage() . "<br><pre>" . $this->showQuery() . "</pre><br>");
            return null;
        }
    }


    public function getLastId()
    {
        return $this->db->lastInsertId();
    }


    public function getNumRows()
    {
        return $this->num_rows;
    }


    protected function getTipoQuery($query)
    {

        $tipo = substr(trim($query), 0, 6);

        return $tipo;
    }


    /**
     * Ejecutar query directamente
     *
     * @param string $raw_query string de la query
     * @param array $parameters parametros de la query, si $raw_query va de manera parametrizada
     * @return void
     */
    public static function raw($raw_query = null, $parameters = null)
    {
        if (is_null($raw_query)) {
            return null;
        }

        return self::$db->getQuery($raw_query, $parameters)->runQuery()->getRows();
    }


    public function logAuditoria($total_time)
    {

        if (\Pan\Kore\App::getDbAudit() == true and $this->conn !== 'audit') {

            if (is_file('connections' . DIRECTORY_SEPARATOR . 'DBAudit.php') and is_file('app/app_database.php')) {
                error_log($this->conn);
                require_once 'connections' . DIRECTORY_SEPARATOR . 'DBAudit.php';
                require 'app/app_database.php';

                if (isset($app_database['audit'])) {
                    $audit_conn = new \Connections\DBAudit($app_database['audit']);
                    $audit_conn->register($total_time, $this->showQuery());
                }
            }
        }
    }
}
