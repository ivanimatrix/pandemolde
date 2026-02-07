<?php
namespace Pan\Kore;


class BootstrapOld
{
    public static function run(\Pan\Kore\RequestOld $_request)
    {
        $controller = $_request->getControlador();

        $pathController = 'app' . DS . 'controllers' . DS . basename($controller) . '.php';
        
        $method = $_request->getMetodo();
        $parameters = $_request->getParametros();
        if(is_file($pathController)){
            
            require_once $pathController;
            $_controller = '\\' . $controller;
            $controller = new $_controller;

            if(is_callable(array($controller, $method))){
                $method = $_request->getMetodo();
            } else {
                //$method = 'index';
                throw new \Exception('<h2>'. \Pan\Kore\App::getName().'</h2><h3>Error!!! Peticion no encontrada</h3>');
            }

            if(!empty($parameters)){
                call_user_func_array(array($controller, $method), $parameters);
            } else {
                call_user_func(array($controller, $method));
            }
        } else {
            throw new \Exception('<h2>'. \Pan\Kore\App::getName().'</h2><h3>Error!!! Peticion no encontrada</h3>');
        }
    }
}
