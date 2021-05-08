<?php
namespace Pan\Kore;


class Entity{

	protected $db;

	protected $table;

	protected $primary_key;

    protected $_entity = null;

    protected $_fields = array();

    protected $_queryable = array();

    protected $_oneToOne = array();

    protected $_oneToMany = array();

    protected $_manyToMany = array();


	public function __construct($_entity=null){
        $this->db = new \Pan\Db\DbQueryBuilder();
        if(!is_null($_entity)){
            $this->setEntity($_entity);
        }
	}


	public function getPrimaryKey(){
	    return $this->primary_key;
    }

    public function getTable(){
	    return $this->table;
    }

    /**
     * crear una instancia de la entidad para un registro específico
     * @param [type] $_entity [description]
     */
    public function setEntity($_entity){
        $this->_entity = $_entity;
        $sql = 'select * from ' . $this->table . ' where ' . $this->primary_key . ' = ? ';
        $result = $this->db->getQuery($sql, $this->_entity)->runQuery();
        if ($result->getNumRows() == 1) {
            $res = $result->getRows(0);
            foreach($res as $field => $val) {
                $this->_fields[$field] = $val;
                $this->{$field} = $val;
            }
            
            
        } else {
            $this->_entity = null;
        }
        
    }


    public function getEntity()
    {
        return $this->_entity;
    }


    /**
     * obtener campos de la instancia creada de la entidad
     * @param  string $field nombre del campo que se desea obtener. Si se omite, el retorno correspondera a todos los campos asociados a la entidad instanciada
     * @return [type]        [description]
     */
    public function get($field = null){
        if(!is_null($field)){
            if (isset($this->_fields[$field])) {
                return $this->_fields[$field];
            } 
        }else{
            $sql = 'select * from ' . $this->table . ' where ' . $this->primary_key . ' = ? ';
            $result = $this->db->getQuery($sql, $this->_entity)->runQuery();
            if ($result->getNumRows() == 1) {
                return $result->getRows(0);
            }
        }
        return null;
    }


	public function create($parametros, $return_last_id = true){

        $insert = "insert into ".$this->table;
        $fields = "";
        $values = "";
        if(is_array($parametros)){
            foreach($parametros as $field => $value){
                $fields .= $field.",";
                $values .= "?,";
                $parameters[] = $value;
            }
            $fields = trim($fields,",");
            $values = trim($values,",");
            $insert .= "(".$fields.") values(".$values.")";
        }


        $return = $this->db->execQuery($insert,$parameters, $return_last_id);
        
        return $return;
	}


	public function update($parametros, $pk = null, $conditions=null){
        $parameters = array();
        $update = "update ".$this->table." set ";
        if(is_array($parametros)){
            foreach($parametros as $field => $value){
                $update .= $field ." = ?, ";
                $parameters[] = $value;
            }
            $update = trim($update,", ");
        }

        if(is_null($conditions)){
            $update .= ' where '.$this->primary_key.' = ?';
            $parameters[] = $pk;
        }else{
            if(is_array($conditions)) {
                $update .= " where ";
                foreach ($conditions as $c => $v) {
                    $update .= ' ' . $c . ' = ? and';
                    $parameters[] = $v;
                }
                $update = trim($update, 'and');
            } else {
                $update .= " where $conditions";
            }

		}

        return $this->db->execQuery($update,$parameters);
	}


	public function read($fields="*"){
        $query = "select ";
        if(empty($fields) or is_null($fields))
            $fields = '*';
        

        if(is_null($fields)){
            $fields = '*';
        }else{
            if(\pan\Utils\ValidatePan::isArray($fields)){
                foreach($fields as $field){
                    $query .= $field.', ';
                }
                $query = trim($query,', ');
            }elseif(\pan\Utils\ValidatePan::isLiteral($fields)){
                $query .= $fields.' ';
            }
        }

        $query .= ' from '. $this->table;
        return $this->db->getQuery($query);
	}


	public function delete($parametros, $conditions=null){
        $parameters = array();
        $delete = "delete from ".$this->table. " where ";
        if(is_array($parametros)){
            foreach($parametros as $field => $value){
                $delete .= $field ." = ? AND ";
                $parameters[] = $value;
            }
            $delete = trim($delete,"AND ");
        }

        /*if(is_null($conditions)){
            $update .= ' where '.$this->primary_key.' = ?';
            $parameters[] = $pk;
        }*/

        return $this->db->execQuery($delete,$parameters);
	}

    /**
     * @param $pk_other_table nombre de campo PK en tabla relacionada
     * @param $name_other_table nombre de tabla relacionada
     * @param $fk nombre de campo interno que se relaciona con otra tabla
     * @param $mandatoria valor booleano para indicar si la relacion es obligatoria(TRUE) o no
     * @return $this
     */
    public function hasOneToOne($pk_other_table,$name_other_table,$fk,$mandatoria=false){

        if(!is_null($this->_entity)){
            $sql = 'SELECT a.* from ' .$name_other_table. ' a ';
            if($mandatoria){
                $sql .= ' inner join ' . $this->table . ' b on a.' . $pk_other_table . ' = b.' . $fk;
            }else{
                $sql .= ' left join ' . $this->table . ' b on a.' . $pk_other_table . ' = b.' . $fk;
            }

            $sql .= ' where b.'. $this->primary_key .' = ? ';
            $params = array($this->_entity);

            $result = $this->db->getQuery($sql,$params)->runQuery();
        }else{
            $sql = 'select * from ' . $this->table . ' a ';
            
            if($mandatoria){
                $sql .= ' inner join ' . $name_other_table . ' b on a.' . $pk_other_table . ' = b.' . $fk;
            }else{
                $sql .= ' left join ' . $name_other_table . ' b on a.' . $pk_other_table . ' = b.' . $fk;
            }
            

            $result = $this->db->getQuery($sql)->runQuery();


        }

        if($result->getNumRows() > 1 ){
            return $result->getRows();
        }elseif($result->getNumRows() == 1){
            return $result->getRows(0);
        }else{
            return null;
        }
        
       
    }

    /**
     * @param $pk_other_table nombre de campo PK en tabla relacionada
     * @param $name_other_table nombre de tabla relacionada
     * @param $fk nombre de campo interno que se relaciona con otra tabla
     * @param $mandatoria valor booleano para indicar si la relacion es obligatoria(TRUE) o no
     * @return $this
     */
    public function hasOneToMany($pk_other_table,$name_other_table,$fk,$mandatoria=false){
        return $this->hasOneToOne($pk_other_table,$name_other_table,$fk,$mandatoria);
    }


    /**
     * @param $table_many tabla que se genera de la relacion muchos a muchos
     * @param $arr_entities arreglo con las entidades y campo que se relaciona en $table_many. Ej.: array('Entidad A'=>'campoA','Entidad B'=>'campoB')
     * @return $this
     */
    public function hasManyToMany($table_many,$arr_entities, $new_pk = null){
        $loader = new \pan\Loader();
        $inner = '';
        
        if(is_array($arr_entities)){
            foreach($arr_entities as $entity => $pk){
                $a = $loader->entity($entity);
                $inner .= ' inner join ' . $a->getTable() . ' b on b.' . $a->getPrimaryKey() . ' = a.' . $pk;

            }
        }
        $sql = 'select * from ' . $table_many . ' a '. $inner;

        if(!is_null($this->_entity)){
            $params = null;
            $_pk = $this->primary_key;
            if (!is_null($new_pk)) {
                $_pk = $new_pk;
            }
            $sql .= ' where a.' . $_pk.' = ? ';
            $params = array($this->_entity);
            return $this->db->getQuery($sql,$params)->runQuery()->getRows();
        }else{
            return $this->db->getQuery($sql);
        }

    }


    /**
     * verifica si existe un registro con un valor para un campo especifico
     * @param  array $arr_field Arreglo de la forma ['campo' => 'valor']
     * @return boolean           Retorna true si existe algun registro con el valor consultado, o false en caso contrario
     */
    public function unique($arr_field){
        if(!is_array($arr_field))
            return null;

        $query = "select count(".key($arr_field).") as total from ".$this->table. " where ".key($arr_field)." = ? ";
        $result = $this->db->getQuery($query,$arr_field[key($arr_field)])->runQuery()->getRows(0)->total;
        if($result == 1){
            return true;
        }else{
            return false;
        }
    }


    public function where($where = array(), $parameters = null)
    {

        $query = "select ";
        if (empty($fields) or is_null($fields))
            $fields = '*';


        if (is_null($fields)) {
            $fields = '*';
        } else {
            if (\pan\Utils\ValidatePan::isArray($fields)) {
                foreach ($fields as $field) {
                    $query .= $field . ', ';
                }
                $query = trim($query, ', ');
            } elseif (\pan\Utils\ValidatePan::isLiteral($fields)) {
                $query .= $fields . ' ';
            }
        }

        $query .= ' from ' . $this->table;

        $params = array();
        if (is_array($where) and count($where) > 0) { 
            $query .= ' where ';
            $last_conn_log = 'and';
            foreach ( $where as $field => $value) {
                if (is_array($value)) {
                    $val = $value[0];
                    $cond = $value[1];
                    $conn_log = 'and';
                    if(isset($value[2])){
                        $conn_log = $value[2];
                    }
                    /* $value = implode(',', $value);
                    $query .= ' ' . $field . ' in ('.$value.') and'; */
                    if(strtolower($cond) == "in" or strtolower($cond) == "not in"){
                        if(is_array($val)){
                            $val = implode(',', $val);
                        }
                        $query .= ' ' . $field . ' ' . $cond .' (' . $val. ') ' . $conn_log;
                    }else{
                        $query .= ' ' . $field . ' ' . $cond .' ? ' . $conn_log;
			            $params[] = $val;
                    }
                    
                    //$params[] = $val;
                    $last_conn_log = $conn_log;
                } else{
                    $query .= ' ' . $field . ' = ? and';
                    $params[] = $value;
                    $last_conn_log = 'and';
                }
               
                
            }
            $query = trim($query, $last_conn_log);
            
        } elseif (is_string($where)) {
            if (!is_null($parameters)) {
                $query .= ' where ' . $where;
                $params = $parameters;
            } else {
                $query .= ' where ' . $where;
            }

        }

        return $this->db->getQuery($query, $params);
    }


    public function all($arguments = null)
    {
        $query = "select * from " . $this->table;

        if (!is_null($arguments)) {
            if (is_string($arguments)) {
                $query .= ' ' . $arguments;
            }
        }

        return $this->db->getQuery($query)->runQuery()->getRows();
    }


    public function getByPK($pk) {
        if (!$pk)
            return null;

        $query = 'select * from ' . $this->table . ' where ' . $this->primary_key . ' = ?  limit 1';
        return $this->db->getQuery($query, $pk)->runQuery()->getRows(0);

    }

    
    public function raw($raw_query = null, $parameters = null)
    {
        if (is_null($raw_query)) {
            return null;
        }

        return $this->db->getQuery($raw_query, $parameters)->runQuery()->getRows();
    }

}