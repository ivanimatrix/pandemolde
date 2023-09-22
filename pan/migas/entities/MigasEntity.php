<?php

namespace pan\Migas\Entities;

use Pan\Db\DBInfo;
use Pan\Migas\Migas;

require 'app/app_database.php';
require 'pan/db/DbConexion.php';
require 'pan/db/DbQueryBuilder.php';
require 'pan/db/DbStore.php';
require 'pan/db/DBInfo.php';
require 'pan/db/pgsql/DBPgsql.php';
require 'pan/db/mysql/DBMysql.php';
require 'pan/db/mssql/DBMssql.php';
require 'pan/utils/ValidatePan.php';
require 'pan/utils/ErrorPan.php';
require 'pan/kore/App.php';
require 'pan/kore/Entity.php';

class MigasEntity extends Migas
{

    public function __construct()
    {
    }


    public function make($parametros = null)
    {

        $_parametros = explode("::", $parametros[1]);
        
        if (isset($_parametros[1])) {

            $entity = $_parametros[1];

            if (isset($entity) and !empty($entity)) {

                // make-all : crear entitities de todas las tablas de la base de datos
                if (mb_strtolower($entity) === 'make-all') {
                    $prefix = null;
                    if (isset($parametros[2])) {
                        $second = explode('::', $parametros[2]);
                        if (isset($second[1])) {
                            $prefix = mb_strtolower($second[1]);
                        }
                    }
                    $this->makeAll($prefix);
                }
                die;

                //$entity_module = $entity[0];
                $entity_class = $entity;

                $table = "";
                if (isset($parametros[2]) and !is_null($parametros[2])) {
                    $s = explode('::', $parametros[2]);
                    if (isset($s[1]) and !empty($s[1]))
                        $table = "\tprotected \$table = '" . $s[1] . "';\n\n";
                }

                $primary_key = "";
                if (isset($parametros[3]) and !is_null($parametros[3])) {
                    $t = explode('::', $parametros[3]);
                    if (isset($t[1]) and !empty($t[1])) {
                        $t_pk = explode(',', $t[1]);
                        if (count($t_pk) > 1) {
                            $arr_pk = "array(";
                            foreach ($t_pk as $pk) {
                                $arr_pk .= "'" . $pk . "',";
                            }
                            $arr_pk = trim($arr_pk, ',') . ")";

                            $primary_key = "\tprotected \$primary_key = " . $arr_pk . ";\n\n";
                        } else {
                            $primary_key = "\tprotected \$primary_key = '" . $t_pk[0] . "';\n\n";
                        }
                    }
                }

                $module_namespace = "\n" . "namespace Entities;\n";

                $content_entity = "<?php\n" . $module_namespace . "\nclass " . $entity_class . " extends \\pan\\Kore\\Entity{\n\n " . $table . $primary_key . "\n";
                $content_entity .= "}";

                $path_entity = 'entities' . DIRECTORY_SEPARATOR . $entity_class . '.php';
                $f = fopen($path_entity, 'w');
                fwrite($f, $content_entity);
                fclose($f);
                if (is_file($path_entity)) {
                    print "* Entidad " . $entity_class . " creada\n";
                } else {
                    print "* Entidad " . $entity_class . " no ha sido creada\n";
                }
            } else {
                print "* El formato para crear entidad es Entidad\n";
            }
        }
    }


    private function makeAll($prefix = null)
    {
        $_DBInfo = new \Pan\Db\DBInfo();
        $tables = $_DBInfo->getTables();
        
        if ($tables) {
            $dir_entities = 'entities' . DIRECTORY_SEPARATOR;
            foreach ($tables as $table) {
                $name_table = $table;
                if (!is_null($prefix)) {
                    $table = str_replace($prefix, '', $table);
                }

                $name_entity = ucwords(str_replace('_', ' ', $table));
                $name_entity = str_replace(' ', '', $name_entity);

                // get primary keys
                $primary_key = '';
                $primary = $_DBInfo->getTablePK($name_table);
                
                if ($primary and count($primary) > 0) {
                    if (count($primary) == 1) {
                        $p = $primary[0];
                        $primary_key = "\tprotected \$primary_key = '" . $p . "';\n\n";
                    } else {
                        $arr_pk = "array(";
                        foreach ($primary as $pk) {
                            $arr_pk .= "'" . $pk . "',";
                        }
                        $arr_pk = trim($arr_pk, ',') . ")";

                        $primary_key = "\tprotected \$primary_key = " . $arr_pk . ";\n\n";
                    }
                }

                if (!is_file($dir_entities . $name_entity . '.php')) {
                    $table_def = "\tprotected \$table = '" . $name_table . "';\n\n";
                    $module_namespace = "\n" . "namespace Entities;\n";

                    $content_entity = "<?php\n" . $module_namespace . "\nclass " . $name_entity . " extends \\pan\\Kore\\Entity{\n\n " . $table_def . $primary_key . "\n";
                    $content_entity .= "}";

                    $path_entity = 'entities' . DIRECTORY_SEPARATOR . $name_entity . '.php';
                    $f = fopen($path_entity, 'w');
                    fwrite($f, $content_entity);
                    fclose($f);
                    if (is_file($dir_entities . $name_entity . '.php')) {
                        print "* Entidad " . $name_entity . " creada\n";
                    } else {
                        print "* Entidad " . $name_entity . " no ha sido creada\n";
                    }
                } 
            }
        }
    }
}
