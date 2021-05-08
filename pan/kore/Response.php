<?php

namespace Pan\Kore;


class Response {


    /**
     * Imprime un json al REQUEST realizado
     * @param $json
     */
    static function returnJson($json = null)
    {
        header('Content-type: application/json; charset=UTF-8');
        echo \pan\panJSON::enc_json($json);
    }


    /**
     * Imprime el contenido HTML pasado como parametro
     * @param $html
     */
    static function returnHtml($html)
    {
        header('Content-type: text/html; charset=UTF-8');
        echo $html;
    }


}