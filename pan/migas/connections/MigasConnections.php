<?php

namespace pan\Migas\Connections;


use Pan\Migas\Migas;

class MigasConnections extends Migas
{

    public function __construct()
    {
    }


    public function make($parametros = null)
    {

        $_parametros = explode("::", $parametros[1]);

        if (isset($_parametros[1])) {

            $param = $_parametros[1];

            if (isset($param) and !empty($param)) {

                if ($param === 'create') {

                    require_once 'app' . DIRECTORY_SEPARATOR . 'app_database.php';

                    if (isset($app_database)) {
                        foreach ($app_database as $conn => $data) {

                            if ($conn !== 'main' and $conn !== 'audit') {
                                $DBConnection = file_get_contents('pan/db/DbConexion.php');
                                $DBConnection = str_replace(array('Pan\Db', 'DbConexion'), array('Connections', 'DB' . ucfirst($conn)), $DBConnection);
                                $nameFile = 'DB' . ucfirst($conn) . '.php';
                                $pathFile = 'connections' . DIRECTORY_SEPARATOR . $nameFile;
                                if (!is_file($pathFile)) {
                                    file_put_contents($pathFile, $DBConnection);
                                    if (is_file($pathFile)) {
                                        print " * Conexion " . $conn . " creada\n";
                                    }
                                }
                                
                            }
                        }
                    }
                }

                /* $path_job = 'jobs' . DIRECTORY_SEPARATOR . $job_class . '.php';
                $f = fopen($path_job,'w');
                fwrite($f,$content_job);
                fclose($f);
                if(is_file($path_job)){
                    print "* Job ". $job_class . " creada\n";
                }else{
                    print "* Job ". $job_class . " no ha sido creada\n";
                } */
            } else {
                print "* El formato para crear job es Entidad\n";
            }
        }
    }
}
