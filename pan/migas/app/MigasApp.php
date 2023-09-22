<?php

namespace Pan\Migas\App;

use Pan\Migas\Migas;

class MigasApp extends Migas {

    /**
     * @var array Listado de directorios que forman la estructura de PANDEMOLDE
     */
    protected static $_dirs = array(
        'app',
        'entities',
        'libs',
        'store',
        'pub' => array('js','img','css','others'),
        'sql',
        'tmp' => array('logs','cache')
    );

    protected static $_folders_htaccess = array('app','entities','libs','store','sql');

    protected static $_htaccess_content = 'Deny from all';

    /* protected static $_arguments = array(
        'create',
        'restore'
    ); */


    public function __construct()
    {
        /* self::$_arguments = array(
            'create',
            'restore'
        ); */
    }

    public function make($parametros = null)
    {

        foreach(self::$_dirs as $d => $e){
            if(is_array($e)){
                mkdir($d,0755);
                if(in_array($d,self::$_folders_htaccess)){
                    $h = fopen($d . DIRECTORY_SEPARATOR . '.htaccess','w');
                    fwrite($h,self::$_htaccess_content);
                    fclose($h);
                }
                foreach($e as $f){
                    mkdir($d. DIRECTORY_SEPARATOR . $f ,0755);
                }
            }else{
                if (!is_dir($e)) {
                    mkdir($e,0755);
                }

                if(in_array($e,self::$_folders_htaccess)){
                    $h = fopen($e . DIRECTORY_SEPARATOR . '.htaccess','w');
                    fwrite($h,self::$_htaccess_content);
                    fclose($h);
                }
            }

        }

        if(is_dir('app')){
            $_app_config = file_get_contents('pan/app_config.php.example');
            $_app_database = file_get_contents('pan/app_database.php.example');

            $a = file_put_contents('app/app_config.php',$_app_config);
            $a = file_put_contents('app/app_database.php',$_app_database);
        }

        print "* Estructura creada  \n";

    }

}