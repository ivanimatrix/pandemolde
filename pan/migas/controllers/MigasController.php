<?php

namespace pan\Migas\Controllers;


use Pan\Migas\Migas;

class MigasController extends Migas {


    protected $_controller_name;


    public function __construct($arguments)
    {
        parent::__construct($arguments);
    }


    public function make($parametros = null) {
        $_parametros = explode("::", $parametros[1]);
        
        if(isset($_parametros[1])){

            $controller = explode('/',$_parametros[1]);
            
            if(!isset($controller[1])){
                print "* El formato para crear controlador es Modulo[@Grupo]\\Controlador\n";
                die;
            }

            $group = '';
            $module = $controller[0];
            $new_controller = $controller[1];
            if (strpos($controller[0], '@') !== false) {
                $explode_module = explode('@', $controller[0]);
                $module = $explode_module[0];
                $group = $explode_module[1];
            } 

            $path_module = 'app' . DIRECTORY_SEPARATOR . $group;
            
            if(!is_dir( $path_module . '/'. $module)){
                print "* El modulo no existe\n";
            }else{

                /* app/GROUP/MODULE */
                $path_module = $path_module . '/'. $module;
                
                if(isset($controller[0]) and isset($controller[1])){
                    $controller_module = ucwords($module);
                    
                    /* if(!is_dir('app/'.$controller_module)){
                        print "* El modulo no existe\n";
                        die;
                    } */

                    $controller_class = ucwords($controller[1]);

                    $methods = "";
                    if(isset($parametros[2]) and !is_null($parametros[2])){
                        $s = explode('::', $parametros[2]);
                        if(isset($s[0]) and !empty($s[0]) and $s[0] == 'actions'){
                            $m = explode(',', $s[1]);
                            foreach($m as $n){
                                if(!empty(trim($n)) or !is_null(trim($n)))
                                    $methods .= "\tpublic function ".$n."(){\n\t\t/** code **/\n\t}\n\n";
                            }

                        }
                    }

                    $module_namespace = "\n" . "namespace App\\"  . $controller_module . ";\n";
                    if (!empty($group)) {
                        $module_namespace = "\n" . "namespace App\\"  . ucwords($group) . "\\" . ucwords($controller_module) . ";\n";
                    }

                    $content_controller = "<?php\n" . $module_namespace . "\n\n\nclass " . $controller_class . " extends \\pan\\Kore\\Controller{\n\n\tpublic function __construct(){\n\t\tparent::__construct();\n\t}\n\n".$methods."}";


                    $path_controller = $path_module  . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller_class . '.php';
                    $f = fopen($path_controller,'w');
                    fwrite($f,$content_controller);
                    fclose($f);
                    if(is_file($path_controller)){
                        print "* controller ". $controller_class . " created it\n";
                    }else{
                        print "* controller ". $controller_class . " did not create it\n";
                    }
                }else{
                    print "* El formato para crear controlador es Modulo\\Controlador\n";
                }
            }

        }

    }

}