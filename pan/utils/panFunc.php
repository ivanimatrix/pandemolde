<?php

namespace pan;


class panFunc {

    private static $tiempo_inicial;
    private static $tiempo_final;


    public static function startTime(){
        self::$tiempo_inicial = microtime(true);
    }


    public static function stopTime(){
        self::$tiempo_final = microtime(true) - self::$tiempo_inicial;
    }


    public static function getTimeExecute(){
        return self::$tiempo_final;
    }

}