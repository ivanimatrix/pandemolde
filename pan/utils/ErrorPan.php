<?php

namespace Pan\Utils;

require "vendor/autoload.php";



class ErrorPan
{


    public static function _showErrorAndDie($message)
    {

        if (is_dir('tmp/logs/') and is_writable('tmp/logs/')) {
            //error_log("\n" . date('Y-m-d H:i:s') . " " . $message, 3, 'tmp/logs/error_log_' . date('Ymd') . '.log');
            file_put_contents('tmp/logs/error_log_' . date('Ymd') . '.log', PHP_EOL . "\n" . date('Y-m-d H:i:s') . " " . $message . PHP_EOL, FILE_APPEND);
        } else {
            //error_log(\Pan\Kore\App::getName() . " " . $message);
            file_put_contents('php://stderr', PHP_EOL . \Pan\Kore\App::getName() . " " . $message . PHP_EOL, FILE_APPEND);
            //file_put_contents('php://stderr', PHP_EOL . print_r($this->db->last_query(), TRUE). PHP_EOL, FILE_APPEND);
        }

        $e = new \Exception();
        $trace = self::callStack($e->getTrace());
        $html_error = file_get_contents('pan/utils/error-view.html');
        $html_error = str_replace('__appname__', \Pan\Kore\App::getName(), $html_error);
        $html_error = str_replace('__message__', $message, $html_error);
        $html_error = str_replace('__debugtrace__', $trace, $html_error);
        echo $html_error;
        die;

        
    }

    private static function callStack($trace)
    {
        $error = array();
        $j = 1;

        for ($i = count($trace) - 1; $i > 0; $i--) {

            if($i!=0){
                $file = "";
                if(isset($trace[$i]['file']))
                    $file = $trace[$i]['file'];

                $line = "";
                if (isset($trace[$i]['line']))
                    $line = "(linea " . $trace[$i]['line'] . ")"; 

                if (isset($trace[$i]['class']) and isset($trace[$i]['function'])) {
                    $error[] = "$j. " . $file . " en " . $trace[$i]['class'] . "::" . $trace[$i]['function'] . $line;
                    $j++;
                }

            }
        }

        return implode("<br/>",$error);

    }


    private static function debugTrace(){

        return self::callStack(debug_backtrace());
        // Remove first item from backtrace as it's this function which 
        // is redundant. 
        //$trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

        // Renumber backtrace items. 
        //$trace = preg_replace ('/^#(\d+)/m', '<br/> ($1)', $trace);

        //return $trace;
    }


    public static function _showError($errno, $errstr, $errfile, $errline)
    {

        $arr_error_no = array(
            E_ERROR => 'PHP Error',
            E_WARNING => 'PHP Warning',
            E_PARSE => 'PHP Parse Error',
            E_NOTICE => 'PHP Notice',
            E_USER_ERROR => 'User Fatal Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_USER_DEPRECATED => 'User Deprecated'
        );

        //$whoops = new \Whoops\Run;
        //$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        //$whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler());
        //$whoops->register();

        $message = $arr_error_no[$errno] . ': ' . $errstr . ' en fichero ' . $errfile . ' linea ' . $errline;

        if (is_dir('tmp/logs/') and is_writable('tmp/logs/')) {
            //error_log("\n" . date('Y-m-d H:i:s') . " " . $message, 3, 'tmp/logs/error_log_' . date('Ymd') . '.log');
            file_put_contents('tmp/logs/error_log_' . date('Ymd') . '.log', PHP_EOL . "\n" . date('Y-m-d H:i:s') . " " . $message . PHP_EOL, FILE_APPEND);
        } else {
            //error_log(\Pan\Kore\App::getName() . " " . $message);
            file_put_contents('php://stderr', PHP_EOL . \Pan\Kore\App::getName() . " " . $message . PHP_EOL, FILE_APPEND);
            //file_put_contents('php://stderr', PHP_EOL . print_r($this->db->last_query(), TRUE). PHP_EOL, FILE_APPEND);
        }

        $e = new \Exception();
        $trace = self::callStack($e->getTrace());

        $content_file_error = '';
        $fp = fopen($errfile, 'r');
        $line = 1;
        $start_line = $errline - 5;
        if ($start_line < 1) {
            $start_line = 1;
        }
        $end_line = $errline + 5;
        while ($read_linea = fgets($fp)) {
            if ($line >= $start_line and $line <= $end_line) {
                if ($line == $errline) {
                    $content_file_error .= '<span style="color:#821a1a;font-weight: bold;">'.$line . '</span>. ' . $read_linea;
                } else {
                    $content_file_error .= $line . '. ' . $read_linea;
                }

            }
            $line++;
        }
        fclose($fp);

        if (\Pan\Kore\App::getDebugView()) {
            error_log(__DIR__);
            $html_error = file_get_contents(__DIR__ .'/error-view.html');
            $html_error = str_replace('__appname__', \Pan\Kore\App::getName(), $html_error);
            $html_error = str_replace('__message__', $message, $html_error);
            $html_error = str_replace('__debugtrace__', '<p>File ' . $errfile .':</p><pre style="font-size:10px;line-height: 16px;">' . $content_file_error . '</pre>', $html_error);
            /* $html_error = '<html>
                        <head>
                            <style>
                                html{
                                    background-color : #111;
                                    color : #fff;
                                    font-family: Arial, Helvetica, sans-serif;
                                }
                                .container{margin:0 auto; width:75%}
                                .container-title{font-size:13px;margin:15px 0; }
                                .container-message{font-size:12px; line-height: 26px; background-color: #888; border-radius: 5px;padding: 10px;}
                                .container-debug-trace, .container-code{font-family: Monaco, Inconsolata;font-size:11px; line-height: 26px;margin:15px 0; background-color: #888; border-radius: 5px;padding: 10px;}
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <hr/>
                                <div class="container-title">Oops! Yo que tu reviso lo siguiente en ' . \Pan\Kore\App::getName() . '</div>
                                <hr/>
                                <div class="container-message">' . $message . '</div>
                                
                                <div class="container-code"><p>File ' . $errfile .':</p><pre style="font-size:10px;line-height: 16px;">' . $content_file_error . '</pre></div>
                            </div>
                        </body>
                        </html>'; */

        } else {
            $html_error = '<html>
                        <head>
                            <style>
                                html{
                                    background-color : #111;
                                    color : #fff;
                                    font-family: Arial, Helvetica, sans-serif;
                                }
                                .container{margin:0 auto; width:75%}
                                .container-title{font-size:13px;margin:15px 0; }
                                .container-message{font-size:12px; line-height: 26px; background-color: #888; border-radius: 5px;padding: 10px;}
                                .container-debug-trace{font-family: Monaco, Inconsolata;font-size:11px; line-height: 26px;margin:15px 0; background-color: #888; border-radius: 5px;padding: 10px;}
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <hr/>
                                <div class="container-title">Oops! Hubo un error en ' . \Pan\Kore\App::getName() . '</div>
                                <hr/>
                                <div class="container-message">Por favor, contactarse con el Administrador del sitio</div>
                            </div>
                        </body>
                        </html>';

        }

        echo $html_error;
        die;
        //throw new \Exception( $message);


    }


    public static function _showShutDown()
    {

        define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR |
            E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
        $error = error_get_last();
        if($error && ($error['type'] & E_FATAL)){
            \Pan\Utils\ErrorPan::_showError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }


}

register_shutdown_function('\Pan\Utils\ErrorPan::_showShutDown');

set_error_handler('\Pan\Utils\ErrorPan::_showError');

set_exception_handler('\Pan\Utils\ErrorPan::_showErrorAndDie');



