<?php

namespace pan\Migas\Modules;


use Pan\Migas\Migas;

class MigasModule extends Migas {

    private $_module_name;


    public function __construct($module_name = null)
    {
        if (!is_null($module_name))
            $this->_module_name = $module_name;
    }


    public function make($parametros = null)
    {
        if(isset($parametros[1])){
            $module = ucwords($parametros[1]);
            print "* Creando modulo $module \n";
            $directories = array(
                'assets' => array(
                    'css',
                    'js',
                    'img'
                ),
                'controllers',
                'models',
                'libraries',
                'views'
            );

            $group = '';

            $path = 'app' . DIRECTORY_SEPARATOR;
            if (strpos($module, '@') !== false) {
                $explode_module = explode('@', $module);
                $module = $explode_module[0];
                $group = $explode_module[1];
                $path = 'app' . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR;
            }


            $dir_module = $path . $module;
            /*if(!is_dir($dir_module)){
                mkdir($dir_module);
            }*/

            foreach($directories as $dir => $content){

                $make_dir =  $dir_module . DIRECTORY_SEPARATOR . $dir;
                if(is_numeric($dir)){
                    $make_dir =  $dir_module . DIRECTORY_SEPARATOR . $content;
                }
                if(is_array($content)){
                    foreach($content as $subdir){
                        mkdir($dir_module . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $subdir, 0755, true);
                    }
                }else{
                    mkdir($make_dir);
                }
            }
            print "* Modulo $module creado  \n";
        }else{
            print "Parece que falta indicar el nombre del modulo, no?\n";
        }
    }

}