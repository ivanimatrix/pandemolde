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
                            $DBConnectionContent = "<?php\nnamespace Connections;\n\n";
                            $DBConnectionContent .= "require_once 'pan/db/connection/DBConnection.php';\n\n";
                            $DBConnectionContent .= "class " . 'DB' . ucfirst($conn) . " extends \Pan\db\connection\DBConnection\n{\n\n";
                            $DBConnectionContent .= "\t" . 'public function __construct($arr_connection)' . "\n\t{\n";
                            $DBConnectionContent .= "\t\t" . 'parent::__construct($arr_connection);' . "\n\t}\n\n";
                            
                            if (mb_strtolower($conn) === 'audit') {
                                $DBConnectionContent .= "\t" . 'public function register($time, $query){' . "\n\t\t// code ...\n\t}"; 
                            }
                                
                            $DBConnectionContent .= "\n\n}";
                            
                            /* $DBConnection = file_get_contents('pan/db/DbConexion.php');
                            $DBConnection = str_replace(array('Pan\Db', 'DbConexion'), array('Connections', 'DB' . ucfirst($conn)), $DBConnection); */
                            $nameFile = 'DB' . ucfirst($conn) . '.php';
                            $pathFile = 'connections' . DIRECTORY_SEPARATOR . $nameFile;
                            if (!is_file($pathFile)) {
                                file_put_contents($pathFile, $DBConnectionContent);
                                if (is_file($pathFile)) {
                                    print " * Conexion " . $conn . " creada\n";
                                }
                            }
                        }
                    }
                } /* elseif ($param === 'audit') {
                    $DBConnection = file_get_contents('pan/db/DbConexion.php');
                    $DBConnection = str_replace(array('Pan\Db', 'DbConexion', 'closeConn()', '$this->conn = null;'), array('Connections', 'DBAudit', 'register($time, $query)','// code for audit'), $DBConnection);
                    $nameFile = 'DBAudit.php';
                    $pathFile = 'connections' . DIRECTORY_SEPARATOR . $nameFile;
                    if (!is_file($pathFile)) {
                        file_put_contents($pathFile, $DBConnection);
                        if (is_file($pathFile)) {
                            $_app_database = file_get_contents('pan/app_database_audit.php.example');
                            $a = file_put_contents('app/app_database_audit.php',$_app_database);
                            print " * Conexion AUDITORIA creada\n";
                        }
                    }
                } */
            } else {
                print "* Problemas para crear conexiones\n";
            }
        }
    }
}
