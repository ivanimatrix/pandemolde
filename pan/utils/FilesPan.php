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


    /**
     * guardar archivo en cache
     * @param $file archivo a guardar
     * @param $path ruta en donde se va a guardar el cache del archivo
     * @return string se retorna un hash asociado al cache guardado del archivo
     */
    public static function cacheIt($file, $path = 'tmp/cache')
    {

        $hash = '';
        return $hash;
    }


}