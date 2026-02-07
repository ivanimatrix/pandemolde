<?php

namespace Pan\Kore;

class RequestOld
{
    private $_controlador;
    private $_metodo;
    private $_parametros;

    public function __construct()
    {

        if(isset($_GET['url'])){
            $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            $url = array_filter($url);

            $this->_controlador = array_shift($url);
            $this->_metodo = array_shift($url);
            $this->_parametros = $url;
        }else{
            
            if(strpos($_SERVER['REQUEST_URI'], BASE_PATH) !== false){
                $url = explode(BASE_PATH,$_SERVER['REQUEST_URI']);
                $url = trim($url[count($url)-1],"/");
                $url = explode("/",$url);
                $this->_controlador = array_shift($url);
                $this->_metodo = array_shift($url);
                $this->_parametros = $url;
            }else{
                $url = trim($_SERVER['REQUEST_URI'],"/");
                $url = explode("/",$url);

                $url = array_filter($url);
                $base = array_shift($url);
                $this->_controlador = DEFAULT_CONTROLLER;
                $this->_metodo = 'index';
                //$this->_parametros = $url;
            }
           
            

        }
        if(!$this->_controlador){
            $this->_controlador = DEFAULT_CONTROLLER;
        }

        if(!$this->_metodo){
            $this->_metodo = 'index';
        }

        if(!isset($this->_parametros)){
            $this->_parametros = array();
        }
    }

    public function getControlador()
    {
        return $this->_controlador;
    }

    public function getMetodo()
    {
        return $this->_metodo;
    }

    public function getParametros($parametro=null)
    { 
        return $this->_parametros;
    }
    
    public function setParametro($nombre, $valor){
        $this->_parametros[$nombre] = $valor;
    }
}
