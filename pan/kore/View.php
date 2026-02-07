<?php

namespace Pan\Kore;

class View
{

    static private $data = array();
    static private $dir_views = 'views';
    static private $js_code = '';
    static private $css_code = '';


    /**
     * Renderiza una vista(template)
     * @param null $template string Nombre del fichero de la vista. Si no se especifica, se toma fichero declarado por defecto en App::setDefaultTemplate()
     * @param null $arr_data array Arreglo de datos que seran procesados para ser utilizados en la vista a renderizar
     * @throws \Exception
     */
    public static function render($template = null, $arr_data = null, $full_path = false)
    {
        // Definir el directorio base permitido de forma absoluta
        $base_app_dir = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app');
        if ($base_app_dir === false) {
            throw new \Exception('Directorio base de la aplicación no encontrado');
        }

        if (!empty($template) || !is_null($template)) {
            // Sanitizar el template: eliminar caracteres peligrosos
            $template = self::sanitizeTemplateName($template);

            if (!preg_match('/\.php$/i', $template)) {
                $template .= '.php';
            }

            if ($full_path) {
                // NUNCA permitir full_path desde entrada del usuario
                // Solo usar internamente con rutas pre-validadas
                $_template = $template;
            } else {
                // Sanitizar los componentes del path
                $_internal_module = self::sanitizePathComponent(basename(Request::getModulo()));
                $_template = 'app' . DIRECTORY_SEPARATOR . $_internal_module . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;

                if (!empty(Request::getGrupo())) {
                    $_interal_group = self::sanitizePathComponent(basename(Request::getGrupo()));
                    $_template = 'app' . DIRECTORY_SEPARATOR . $_interal_group . DIRECTORY_SEPARATOR . $_internal_module . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
                }
            }

            // Resolver la ruta absoluta y validar que esté dentro del directorio permitido
            $absolute_template_path = realpath($_template);

            // Verificar que el archivo existe
            if (!$absolute_template_path || !is_file($absolute_template_path)) {
                throw new \Exception('Vista no encontrada');
            }

            // VALIDACIÓN CRÍTICA: Verificar que está dentro del directorio base permitido
            if (!self::isPathWithinBaseDirectory($absolute_template_path, $base_app_dir)) {
                throw new \Exception('Acceso denegado: ruta no permitida');
            }

            // Validar que el archivo está en la whitelist de extensiones permitidas
            if (!self::isAllowedFileExtension($absolute_template_path)) {
                throw new \Exception('Tipo de archivo no permitido');
            }

            $_template = $absolute_template_path;

        } else {
            if (!empty(App::getDefaultTemplate())) {
                $default_template = App::getDefaultTemplate();
                // Aplicar las mismas validaciones al template por defecto
                $absolute_template_path = realpath($default_template);

                if (!$absolute_template_path || !is_file($absolute_template_path)) {
                    throw new \Exception('Template por defecto no encontrado');
                }

                if (!self::isPathWithinBaseDirectory($absolute_template_path, $base_app_dir)) {
                    throw new \Exception('Template por defecto en ubicación no permitida');
                }

                $_template = $absolute_template_path;
            } else {
                throw new \Exception('No se ha definido un template por defecto');
            }
        }

        // Procesar datos
        if (!is_null($arr_data) && is_array($arr_data)) {
            if (count(self::$data) == 0) {
                self::$data = $arr_data;
            } else {
                foreach ($arr_data as $k => $v) {
                    self::set($k, $v);
                }
            }
        }

        // Extraer variables de forma segura
        if (is_array(self::$data) && count(self::$data) > 0) {
            extract(self::$data, EXTR_SKIP); // EXTR_SKIP previene sobrescribir variables existentes
        }

        // Incluir el template validado
        require $_template;
        echo self::$js_code;
    }

    /**
     * Sanitiza el nombre del template eliminando caracteres peligrosos
     */
    private static function sanitizeTemplateName($template)
    {
        // Eliminar null bytes y caracteres de control
        $template = str_replace(["\0", "\r", "\n"], '', $template);

        // Eliminar path traversal
        $template = str_replace(['../', '..\\', '../', '..\\'], '', $template);

        // Eliminar caracteres peligrosos adicionales
        $template = preg_replace('/[^a-zA-Z0-9_\-\/\\.]/', '', $template);

        return $template;
    }

    /**
     * Sanitiza componentes individuales del path
     */
    private static function sanitizePathComponent($component)
    {
        // Solo permitir caracteres alfanuméricos, guiones y guiones bajos
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $component);
    }

    /**
     * Verifica que una ruta esté dentro del directorio base permitido
     */
    private static function isPathWithinBaseDirectory($path, $baseDir)
    {
        // Normalizar ambas rutas
        $path = realpath($path);
        $baseDir = realpath($baseDir);

        if ($path === false || $baseDir === false) {
            return false;
        }

        // Verificar que la ruta comience con el directorio base
        return strpos($path, $baseDir) === 0;
    }

    /**
     * Verifica que el archivo tenga una extensión permitida
     */
    private static function isAllowedFileExtension($filePath)
    {
        $allowedExtensions = ['.php', '.phtml']; // Solo extensiones de templates permitidas
        $extension = strtolower(substr($filePath, strrpos($filePath, '.')));

        return in_array($extension, $allowedExtensions);
    }

    /**
     * Método público para usar cuando necesites full_path de forma segura
     * Solo para uso interno de la aplicación, nunca con entrada del usuario
     */
    public static function renderWithValidatedPath($validated_template_path, $arr_data = null)
    {
        // Este método solo debe usarse internamente con rutas ya validadas
        return self::render($validated_template_path, $arr_data, true);
    }

    /**
     * Procesar una vista para obtener su contenido
     * @param $template
     * @param null $arr_data
     * @param null $other_module
     * @return string
     */
    public static function fetchIt($template, $arr_data = null, $other_module = null)
    {
        // Definir el directorio base permitido de forma absoluta
        $base_app_dir = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app');
        if ($base_app_dir === false) {
            throw new \Exception('Directorio base de la aplicación no encontrado');
        }

        // Sanitizar el template
        $template = self::sanitizeTemplateName($template);

        if (!preg_match('/\.php$/i', $template)) {
            $template .= '.php';
        }

        // Construir la ruta del template de forma segura
        if (!is_null($other_module)) {
            // Sanitizar el módulo externo
            $other_module = self::sanitizePathComponent($other_module);

            // Validar que el módulo esté en la whitelist de módulos permitidos
            /*if (!self::isAllowedModule($other_module)) {
                throw new \Exception('Módulo no permitido: ' . htmlspecialchars($other_module));
            }*/

            $_template = 'app' . DIRECTORY_SEPARATOR . $other_module . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
        } else {
            // Sanitizar módulo y grupo del request
            $_internal_module_fechIt = self::sanitizePathComponent(basename(Request::getModulo()));
            $_template = 'app' . DIRECTORY_SEPARATOR . $_internal_module_fechIt . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;

            if (!empty(Request::getGrupo())) {
                $_internal_group_fechIt = self::sanitizePathComponent(basename(Request::getGrupo()));
                $_template = 'app' . DIRECTORY_SEPARATOR . $_internal_group_fechIt . DIRECTORY_SEPARATOR . $_internal_module_fechIt . DIRECTORY_SEPARATOR . self::$dir_views . DIRECTORY_SEPARATOR . $template;
            }
        }

        // Resolver la ruta absoluta y validar
        $absolute_template_path = realpath($_template);

        // Verificar que el archivo existe
        if (!$absolute_template_path || !is_file($absolute_template_path)) {
            throw new \Exception('Template no encontrado: ' . htmlspecialchars($template));
        }

        // VALIDACIÓN CRÍTICA: Verificar que está dentro del directorio base permitido
        if (!self::isPathWithinBaseDirectory($absolute_template_path, $base_app_dir)) {
            throw new \Exception('Acceso denegado: ruta no permitida');
        }

        // Validar que el archivo está en la whitelist de extensiones permitidas
        if (!self::isAllowedFileExtension($absolute_template_path)) {
            throw new \Exception('Tipo de archivo no permitido');
        }

        // Procesar datos de forma segura
        if (!is_null($arr_data) && is_array($arr_data)) {
            if (count(self::$data) == 0) {
                self::$data = $arr_data;
            } else {
                foreach ($arr_data as $k => $v) {
                    self::set($k, $v);
                }
            }
        }


        // Temporal para debug - eliminar después
        if (is_array(self::$data) && count(self::$data) > 0) {
            extract(self::$data, EXTR_SKIP);
        }

        // Iniciar captura de output
        ob_start();

        try {

            // Incluir el template validado
            require $absolute_template_path;

            $content = ob_get_contents();
            ob_end_clean();

            return $content;

        } catch (\Exception $e) {
            // Limpiar buffer en caso de error
            ob_end_clean();
            throw $e;
        }
    }


    /**
     * Asignar un valor a una variable para la vista
     * @param $name string Nombre de la variable en la vista
     * @param $val mixed Valor que $name debe tener en la vista
     */
    public static function set($name, $val)
    {
        //$this->data[$name] = $val;
        self::$data[$name] = $val;
    }


    /**
     * Agrega codigo CSS a la vista
     * @param $css
     */
    public static function addCSS($css)
    {
        if (!filter_var($css, FILTER_VALIDATE_URL) === false) {
            echo '<link type="text/css" href="' . $css . '" rel="stylesheet"/>';
        } else {
            $css = basename($css);
            $modulo = basename(Request::getModulo());
            $path = "app/" . $modulo . "/assets/css/" . $css;
            if (!empty(Request::getGrupo())) {
                $grupo = basename(Request::getGrupo());
                $path = "app/" . $grupo . "/" . $modulo . "/assets/css/" . $css;
            }
            if (is_file($path) and is_readable($path)) {
                $css_content = file_get_contents($path);
                if (defined('ENVIRONMENT') and ENVIRONMENT == 'PROD') {
                    $css = str_replace('.css', '.min.css', $css);
                }

                $css_content = preg_replace("/[\n|\r|\n\r]/i", "", $css_content);
                //$ccs_content = \pan\panMinify::minCSS($css_content);
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
    public static function addJS($javascript, $dir = null)
    {

        if (strpos($javascript, '<script') !== false) {
            self::$js_code .= $javascript;
        } else {
            if (!filter_var($javascript, FILTER_VALIDATE_URL) === false) {
                self::$js_code .= '<script type="text/javascript" src="' . $javascript . '" charset="' . App::getCharset() . '"></script>';
            } else {
                if (!is_null($dir)) {
                    $dir = trim($dir, DS);
                    if (is_file($dir . DS . $javascript) and is_readable($dir . DS . $javascript)) {
                        $js_content = file_get_contents($dir . DS . $javascript);

                        self::$js_code .= "\n" . '<script type="text/javascript" charset="' . App::getCharset() . '">' . "\n" . $js_content . "\n" . '</script>' . "\n";
                    } else {
                        self::$js_code .= "\n" . '<script type="text/javascript" charset="' . App::getCharset() . '">' . "\n" . 'console.error("Archivo ' . $javascript . ' no se ha cargado");' . "\n" . '</script>' . "\n";
                    }
                } else {
                    $modulo = basename(Request::getModulo());
                    $path_js = "app/" . $modulo . "/assets/js/" . $javascript;

                    if (!empty(Request::getGrupo())) {
                        $grupo = basename(Request::getGrupo());
                        $path_js = "app/" . $grupo . "/" . $modulo . "/assets/js/" . $javascript;
                    }

                    if (is_file($path_js) and is_readable($path_js)) {
                        $js_content = file_get_contents($path_js);

                        self::$js_code .= "\n" . '<script type="text/javascript" charset="' . App::getCharset() . '">' . "\n" . $js_content . "\n" . '</script>' . "\n";
                    } else {
                        self::$js_code .= "\n" . '<script type="text/javascript" charset="' . App::getCharset() . '">' . $javascript . ';</script>' . "\n";
                    }
                }
            }
        }
    }
}
