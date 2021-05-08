<?php
namespace Pan\Utils;


class LoggerPan
{


    private static $_log_file = null;


    const LOG_INFO = 1;
    const LOG_WARNING = 2;
    const LOG_ERROR = 3;

    public function  __construct($log_file = null)
    {
        if (!is_null($log_file)) {
            self::$_log_file = $log_file;
        }
    }


    /**
     * Establece la ruta y nombre del archivo log
     *
     * @param [type] $log_file  Ruta del archivo
     * @return void
     */
    public static function setLogFile ($log_file) {
        self::$_log_file = $log_file;
    }


    /**
     * Escribe $message en el archivo de log indicado
     *
     * @param [type] $message   Mensaje a escribir
     * @param [type] $type      Tipo de mensaje: INFO, ERROR o WARNING
     * @return void
     */
    public static function writeLog ($message = null, $type = self::LOG_INFO)
    {
        if (self::$_log_file) {
            if (is_null($message)) {
                $message = "No message";
            }
            $fp = fopen (self::$_log_file, 'a+');
            fwrite ($fp, date('Y-m-d H:i:s') . " [" . self::getLogNivel($type) . "] : " . $message ."\n");
            fclose ($fp);
        }
    }


    /**
     * Limpia contenido de archivo log indicado
     *
     * @return void
     */
    public static function clearLog () 
    {
        if (self::$_log_file) {
            unlink (self::$_log_file);
            $fp = fopen (self::$_log_file, 'a+');
            fclose ($fp);
        }
    }


    private static function getLogNivel($nivel) {
        
        if ($nivel == self::LOG_WARNING) {
            return 'WARNING';
        } elseif ($nivel == self::LOG_ERROR) {
            return 'ERROR';
        } else {
            return 'INFO';
        }
    }

}