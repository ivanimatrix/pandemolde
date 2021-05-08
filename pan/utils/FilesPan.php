<?php

namespace Pan\Utils;

class FilesPan {


    /**
     * obtener extension de un archivo
     * @param $filename nombre del fichero
     * @return string extension del archivo
     */
    public static function getExtension($filename){
        $tmp_name = explode('.', $filename);
        return strtolower(end($tmp_name));
    }





}