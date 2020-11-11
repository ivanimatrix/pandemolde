<?php
namespace Pan\Kore;

use Pan\Kore\Request as Request;
use Pan\Utils\ErrorPan as ErrorPan;

class Bootstrap
{

    public static function run()
    {
        $_request = new Request;
        $module = $_request->getModulo();
        if (!empty($_request->getGrupo())) {
            if(!is_dir('app' . DIRECTORY_SEPARATOR . $_request->getGrupo())){
                throw new \Exception('Group '. $_request->getGrupo() .' not found');
            }
            $module = $_request->getGrupo() . DIRECTORY_SEPARATOR . $module;
        }
        if(empty($module) or !is_dir('app' . DIRECTORY_SEPARATOR . $module)){
            $msg_notFound = 'Module '.$_request->getModulo().' not found';
            if (!empty($_request->getGrupo())) {
                $msg_notFound = 'Module '.$_request->getModulo().' not found in Group ' . $_request->getGrupo();
            }
            throw new \Exception($msg_notFound);
        }

        $controller = $_request->getControlador();

        $pathController = 'app' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';

        $method = $_request->getMetodo();
        $parameters = $_request->_getParameters();

        if(is_file($pathController)){

            require $pathController;

            $_class = '\\app\\' . $_request->getModulo() . '\\' . $controller;
            if (!empty($_request->getGrupo())) {
                $_class = '\\app\\' . $_request->getGrupo() . '\\'. $_request->getModulo() . '\\' . $controller;
            }

            
            $controller = new $_class;

            if(is_callable(array($controller, $method))){
                $method = $_request->getMetodo();
            } else {
                ErrorPan::_showErrorAndDie('Action '.$method.' not found');
                /*if(is_file(App::getPath404())){
                    require_once App::getPath404();
                }else{
                    errorPan::_showErrorAndDie('Action '.$method.' not found');

                }*/
            }

            if(!empty($parameters)){
                call_user_func_array(array($controller, $method), $parameters);
            } else {
                call_user_func(array($controller, $method));
            }
        } else {
            ErrorPan::_showErrorAndDie('Controller '.$controller.' not found in module ' . $_request->getModulo());
            /*if(is_file(App::getPath404())){
                require_once App::getPath404();
            }else{
                \pan\errorPan::_showErrorAndDie('Controller '.$controller.' not found');
            }*/
        }
    }
}


