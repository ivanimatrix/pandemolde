<?php
namespace Pan\Db;


class DbStore {


	private $num_rows;

	private $rows = null;

	private $query_string;


	public function getRows($index=null){
		
		if(is_numeric($index) and $index>=0){
		    if (isset($this->rows[$index])) {
		        return $this->rows[$index];
            } else {
		        return null;
            }
        }
		return $this->rows;
	}


	public function setRows($rows){
		$this->rows = $rows;
	}


	public function getNumRows(){
		return $this->num_rows;
	}


	public function setNumRows($num_rows){
		$this->num_rows = $num_rows;
	}


	public function setQueryString($query_string){
		$this->query_string = $query_string;
	}


	public function getQueryString(){
		return $this->query_string;
	}


	/**
	 * Retorna la primera fila encontrada en la consulta
	 *
	 * @return Object Primer registro de query
	 */
	public function getFirst()
	{
		return isset($this->rows[0]) ? $this->rows[0] : null;
	}


	/**
	 * Retorna la última fila encontrada en la consulta
	 *
	 * @return Object Ultimo registro de query
	 */
	public function getLast()
	{
		return isset($this->rows[($this->num_rows - 1)]) ? $this->rows[($this->num_rows - 1)] : null;
	}

}