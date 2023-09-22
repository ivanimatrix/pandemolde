<?php

namespace Pan\Utils;


class DebuggingPan {


    /**
     * imprimir en pantalla variable $str
     * @param  mixed $str
     * @param  boolean $die true si se desea terminar la ejecucion del script despues de mostrar la variable
     * @return [type]      [description]
     */
    public static function printThis($str,$die=null){
        echo "<pre>";
        if(is_array($str) or is_object($str))
            print_r($str);
        else
            echo $str;

        echo "</pre>";
        if($die){
            die();
        }
    }


    public static function dumpThis($str,$die=null){
        echo "<pre>";
        var_dump($str);
        echo "</pre>";
        if($die){
            die();
        }
    }


}