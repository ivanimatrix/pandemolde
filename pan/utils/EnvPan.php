<?php

namespace Pan\Utils;



class EnvPan
{


    private $path_env = "";


    public function __construct($path_env = null)
    {
        // Solo si el archivo existe se crean las variables en $_ENV
        if (!is_null($path_env)) {
            if (file_exists($path_env)) {
                $lines = file($path_env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    // Ignorar las líneas que comienzan con #
                    if (strpos(trim($line), '#') === 0) {
                        continue;
                    }
                    // Separar la línea en clave y valor
                    list($key, $value) = explode('=', $line, 2);
                    // Asignar la variable de entorno si la línea es válida
                    if (isset($key) && isset($value)) {
                        $_ENV[trim($key)] = trim($value);
                    }
                }
                
            } else {
                \Pan\Utils\ErrorPan::_showErrorAndDie('No se encuentra el archivo .env en la ruta especificada');
            }
        }
        
    }


    /**
     * Cargar archivo .env
     * @param $path_env string ruta del archivo .env
     */
    public static function load($path_env = null)
    {
        
        // Solo si el archivo existe se crean las variables en $_ENV
        if (!is_null($path_env)) {
            if (file_exists($path_env)) {
                $lines = file($path_env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    // Ignorar las líneas que comienzan con #
                    if (strpos(trim($line), '#') === 0) {
                        continue;
                    }
                    // Separar la línea en clave y valor
                    list($key, $value) = explode('=', $line, 2);
                    // Asignar la variable de entorno si la línea es válida
                    if (isset($key) && isset($value)) {
                        $_ENV[trim($key)] = trim($value);
                    }
                }
                
            } else {
                \Pan\Utils\ErrorPan::_showErrorAndDie('No se encuentra el archivo .env en la ruta especificada');
            }
        }
    }


    /**
     * Obtener una variable de entorno
     * @param $var string nombre de la variable de entorno
     * @return string valor de la variable de entorno solicitada
     */
    public static function get($var)
    {
        return $_ENV[$var];
    }


}