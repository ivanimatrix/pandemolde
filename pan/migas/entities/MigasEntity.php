<?php

namespace pan\Migas\Entities;


use Pan\Migas\Migas;

class MigasEntity extends Migas {


    public function make($parametros = null) {

        $_parametros = explode("::", $parametros[1]);

        if(isset($_parametros[1])){

            $entity = explode('/',$_parametros[1]);
            if(isset($entity[0]) and isset($entity[1])){
                $entity_module = $entity[0];
                $entity_class = $entity[1];

                $table = "";
                if(isset($parametros[2]) and !is_null($parametros[2])){
                    $s = explode('::',$parametros[2]);
                    if(isset($s[1]) and !empty($s[1]))
                        $table = "\tprotected \$table = '".$s[1]."';\n\n";
                }

                $primary_key = "";
                if(isset($parametros[3]) and !is_null($parametros[3])){
                    $t = explode('::', $parametros[3]);
                    if(isset($t[1]) and !empty($t[1])){
                        $t_pk = explode(',', $t[1]);
                        if(count($t_pk) > 1){
                            $arr_pk = "array(";
                            foreach($t_pk as $pk){
                                $arr_pk .= "'".$pk."',";
                            }
                            $arr_pk = trim($arr_pk,',') . ")";

                            $primary_key = "\tprotected \$primary_key = ".$arr_pk.";\n\n";
                        }else{
                            $primary_key = "\tprotected \$primary_key = '".$t_pk[0]."';\n\n";
                        }

                    }
                }

                $module_namespace = "\n" . "namespace App\\"  . ucwords($entity_module) . "\\Entity;\n";

                $content_entity = "<?php\n" .$module_namespace . "\nclass " . $entity_class . " extends \\pan\\Kore\\Entity{\n\n " . $table . $primary_key . "}";

                $path_entity = 'app' . DIRECTORY_SEPARATOR . ucwords($entity_module) . DIRECTORY_SEPARATOR . 'entities' . DIRECTORY_SEPARATOR . $entity_class . '.php';
                $f = fopen($path_entity,'w');
                fwrite($f,$content_entity);
                fclose($f);
                if(is_file($path_entity)){
                    print "* Entidad ". $entity_class . " creada\n";
                }else{
                    print "* Entidad ". $entity_class . " no ha sido creada\n";
                }
            }else{
                print "* El formato para crear entidad es Modulo/Entidad\n";
            }

        }

    }

}