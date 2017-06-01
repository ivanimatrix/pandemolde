<?php
namespace pan;

class View
{

    static private $data = array();
    static private $dir_views = 'views';
    static private $js_code = '';
    static private $css_code = '';



    public static function render($template = null, $arr_data = null)
    {
        if(!empty($template) or !is_null($template)){
            if(!preg_match('/\.php/i',$template)){
                $template .= '.php';
            }

            $_template = 'app' . DIRECTORY_SEPARATOR . \pan\Request::getModulo() . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
            if (!is_file($_template))
                throw new \Exception('vista no encontrada');

            if (!is_null($arr_data) and is_array($arr_data)) {
                if (count(self::$data) == 0) {
                    self::$data = $arr_data;
                } else {
                    foreach ($arr_data as $k => $v) {
                        self::set($k, $v);
                    }
                }
            }    
        }else{
            if(!empty(\pan\App::getDefaultTemplate())){
                $_template = \pan\App::getDefaultTemplate();
            }else{
                throw new \Exception('No se ha definido un template por defecto. ');                
            }
        }
        

        if (is_array(self::$data) and count(self::$data) > 0)
            extract(self::$data);

        require_once $_template;

        echo self::$js_code;
    }


    public static function process($template, $arr_data = null)
    {
        ob_start();

        if (!is_null($arr_data) and is_array($arr_data)) {
            if (count(self::$data) == 0) {
                self::$data = $arr_data;
            } else {
                foreach ($arr_data as $k => $v) {
                    self::set($k, $v);
                }
            }
        }

        if (is_array(self::$data) and count(self::$data) > 0)
            extract(self::$data);

        if(!preg_match('/\.php/i',$template)){
            $template .= '.php';
        }

        $_template = 'app' . DIRECTORY_SEPARATOR . \pan\Request::getModulo() . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;

        require_once $_template;

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }


    public static function set($name, $val)
    {
        //$this->data[$name] = $val;
        self::$data[$name] = $val;
    }


    public static function loadCss($css, $dir=null)
    {
        $loader = new \pan\Loader();
        $loader->loadCss($css, $dir);
        /*
        if (is_file($css) and is_readable($css)) {
            if (defined('ENVIRONMENT') and ENVIRONMENT == 'PROD') {
                $css = str_replace('.js', '.min.js', $css);
            } else {

            }
            echo '<link href="' . \pan\Uri::getHost() . $css . '?'.sha1($css.uniqid()).'" type="text/css" rel="stylesheets" />';
        } else {
            echo '<style type="text/css">' . $css . '</style>';
        }*/
    }


    public static function loadJs($javascript,$dir = null)
    {
        $loader = new \pan\Loader();
        $loader->loadJs($javascript, $dir);
        /*
        if (is_file($javascript) and is_readable($javascript)) {
            if (defined('ENVIRONMENT') and ENVIRONMENT == 'PROD') {
                $javascript = str_replace('.js', '.min.js', $javascript);
            } else {

            }
            echo '<script src="' . \pan\Uri::getHost() . $javascript . '?'.sha1($javascript.uniqid()).'" type="text/javascript" charset="' . \pan\App::getCharset() . '"></script>';
        } else {
            echo '<script type="text/javascript" charset="' . \pan\App::getCharset() . '">' . $javascript . '</script>';
        }*/
    }

    /**
     * Agrega codigo CSS a la vista
     * @param $css
     */
    public static function addCSS($css)
    {
        if (!filter_var($css, FILTER_VALIDATE_URL) === false) {
            echo '<link type="text/css" href="'.$css.'" rel="stylesheet"/>';
        }else{
            if (is_file("app/".\pan\Request::getModulo()."/assets/css/".$css) and is_readable("app/".\pan\Request::getModulo()."/assets/css/".$css)) {
                $css_content = file_get_contents("app/".\pan\Request::getModulo()."/assets/css/".$css);
                if (defined('ENVIRONMENT') and ENVIRONMENT == 'PROD') {
                    $css = str_replace('.js', '.min.js', $css);
                }

                $css_content = preg_replace("/[\n|\r|\n\r]/i","",$css_content);
                $ccs_content = self::minify_css($css_content);
                //echo '<link href="' . \pan\Uri::getHost() . $css . '?'.sha1($css.uniqid()).'" type="text/css" rel="stylesheets" />';
            } else {
                $css_content = $css;
            }
            echo '<style type="text/css">' . $css_content . '</style>';
        }

    }

    /**
     * Agrega codigo Javascript a la vista
     * @param $javascript
     * @param null $dir
     */
    public static function addJS($javascript,$dir = null)
    {

        if (!filter_var($javascript, FILTER_VALIDATE_URL) === false) {
            self::$js_code .= '<script type="text/javascript" src="'.$javascript.'" charset="' . \pan\App::getCharset() . '"></script>';
        } else {
            if($dir == 'pub'){
                if (is_file("pub/js/".$javascript) and is_readable("pub/js/".$javascript)) {
                    $js_content = file_get_contents("pub/js/".$javascript);
                    if (defined('ENVIRONMENT') and ENVIRONMENT == 'PROD') {
                        $javascript = str_replace('.js', '.min.js', $javascript);
                    }

                    //$js_content = preg_replace("/[\n|\r|\n\r]/i","",$js_content);
                    if(strpos($javascript,'min.js') === false)
                        $js_content = self::minify_js($js_content);
                    //echo '<script src="' . \pan\Uri::getHost() . $javascript . '?'.sha1($javascript.uniqid()).'" type="text/javascript" charset="' . \pan\App::getCharset() . '"></script>';
                    self::$js_code .= '<script type="text/javascript" charset="' . \pan\App::getCharset() . '">'.$js_content.'</script>';
                } else {
                    self::$js_code .= '<script type="text/javascript" charset="' . \pan\App::getCharset() . '">console.log("Archivo '.$javascript.' no se ha cargado");</script>';
                }
            }else{
                if (is_file("app/".\pan\Request::getModulo()."/assets/js/".$javascript) and is_readable("app/".\pan\Request::getModulo()."/assets/js/".$javascript)) {
                    $js_content = file_get_contents("app/".\pan\Request::getModulo()."/assets/js/".$javascript);
                    if (defined('ENVIRONMENT') and ENVIRONMENT == 'PROD') {
                        $javascript = str_replace('.js', '.min.js', $javascript);
                    } else {

                    }

                    //$js_content = preg_replace("/[\n|\r|\n\r]/i","",$js_content);
                    $js_content = self::minify_js($js_content);
                    //echo '<script src="' . \pan\Uri::getHost() . $javascript . '?'.sha1($javascript.uniqid()).'" type="text/javascript" charset="' . \pan\App::getCharset() . '"></script>';
                    self::$js_code .= '<script type="text/javascript" charset="' . \pan\App::getCharset() . '">'.$js_content.'</script>';
                } else {
                    self::$js_code .= '<script type="text/javascript" charset="' . \pan\App::getCharset() . '">' . $javascript . ';</script>';
                }
            }
        }

    }


    private static function minify_js($input) {
        if(trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_]\w*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([\w\)\]])\[([\'"])([a-z_]\w*)\2\]#i',
                // Replace `true` with `!0`
                '#(?<=return |[=:,\(\[])true\b#',
                // Replace `false` with `!1`
                '#(?<=return |[=:,\(\[])false\b#',
                // Clean up ...
                '#\s*(\/\*|\*\/)\s*#'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3',
                '!0',
                '!1',
                '$1'
            ),
            $input);
    }


    private function minify_css($input) {
        if(trim($input) === "") return $input;
        // Force white-space(s) in `calc()`
        if(strpos($input, 'calc(') !== false) {
            $input = preg_replace_callback('#(?<=[\s:])calc\(\s*(.*?)\s*\)#', function($matches) {
                return 'calc(' . preg_replace('#\s+#', "\x1A", $matches[1]) . ')';
            }, $input);
        }
        return preg_replace(
            array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by a white-space or `=`, `:`, `,`, `(`, `-`
                '#(?<=[\s=:,\(\-]|&\#32;)0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][-\w]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s=:,\(]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s',
                '#\x1A#'
            ),
            array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2',
                ' '
            ),
            $input);
    }

}