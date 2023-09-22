<?php

namespace Pan\Kore;


class Response {


    /**
     * Imprime un json al REQUEST realizado
     * @param $json
     */
    static function toJson($json = null)
    {
        header('Content-type: application/json; charset=UTF-8');
        echo \pan\Utils\JsonPan::enc_json($json);
    }


    /**
     * Imprime el contenido HTML pasado como parametro
     * @param $html
     */
    static function toHtml($html)
    {
        header('Content-type: text/html; charset=UTF-8');
        echo $html;
    }


}