<?php
namespace Pan\Kore;

use Pan\Kore\Request as Request;
use Pan\Utils\ErrorPan as ErrorPan;

class Bootstrap
{

    public static function run()
    {
        // Definir el directorio base permitido de forma absoluta
        $base_app_dir = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app');

        if ($base_app_dir === false) {
            throw new \Exception('Directorio base de la aplicación no encontrado');
        }

        try {
            $_request = new Request;

            if ($_request->getModulo() === 'Login') {
                self::LoginAntiguo($_request);
                exit;
            } 

            // Sanitizar y validar módulo
            $_internal_bootstrap_module = self::sanitizePathComponent(basename($_request->getModulo()));
            if (empty($_internal_bootstrap_module)) {
                self::handleNotFound('Módulo no válido');
                return;
            }

            // Procesar grupo si existe
            if (!empty($_request->getGrupo())) {
                $sanitized_grupo = self::sanitizePathComponent(basename($_request->getGrupo()));

                $group_path = $base_app_dir . DIRECTORY_SEPARATOR . $sanitized_grupo;

                // Validar que el directorio del grupo existe y está dentro del base_app_dir
                if (!self::isValidDirectory($group_path, $base_app_dir)) {
                    self::handleNotFound('Grupo ' . htmlspecialchars($sanitized_grupo) . ' no encontrado');
                    return;
                }

                $_internal_bootstrap_module = $sanitized_grupo . DIRECTORY_SEPARATOR . $_internal_bootstrap_module;
            }

            // Validar directorio del módulo
            $module_path = $base_app_dir . DIRECTORY_SEPARATOR . $_internal_bootstrap_module;
            if (!self::isValidDirectory($module_path, $base_app_dir)) {
                $msg_notFound = 'Módulo ' . htmlspecialchars($_request->getModulo()) . ' no encontrado';
                if (!empty($_request->getGrupo())) {
                    $msg_notFound = 'Módulo ' . htmlspecialchars($_request->getModulo()) . ' no encontrado en Grupo ' . htmlspecialchars($_request->getGrupo());
                }
                self::handleNotFound($msg_notFound);
                return;
            }

            // Sanitizar y validar controlador
            $_internal_bootstrap_controller = self::sanitizePathComponent(basename($_request->getControlador()));
            if (empty($_internal_bootstrap_controller)) {
                self::handleNotFound('Controlador no válido');
                return;
            }

            // Construir ruta del controlador de forma segura
            $pathController = $base_app_dir . DIRECTORY_SEPARATOR . $_internal_bootstrap_module . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $_internal_bootstrap_controller . '.php';

            // Validar que la ruta del controlador está dentro del directorio permitido
            $absolute_controller_path = realpath($pathController);
            if (!$absolute_controller_path || !self::isPathWithinBaseDirectory($absolute_controller_path, $base_app_dir)) {
                self::handleNotFound('Controlador ' . htmlspecialchars($_internal_bootstrap_controller) . ' no encontrado en módulo ' . htmlspecialchars($_request->getModulo()));
                return;
            }

            // Sanitizar método
            $internal_bootstrap_method = self::sanitizeMethodName($_request->getMetodo());
            if (empty($internal_bootstrap_method)) {
                self::handleNotFound('Método no válido');
                return;
            }

            $parameters = $_request->_getParameters();

            if (is_file($absolute_controller_path)) {

                require $absolute_controller_path;

                // Construir namespace de forma segura - usando valores ya sanitizados
                $_class = '\\app\\' . $_internal_bootstrap_module . '\\' . $_internal_bootstrap_controller;
                if (!empty($_request->getGrupo())) {
                    $sanitized_grupo_ns = self::sanitizePathComponent(basename($_request->getGrupo()));
                    $sanitized_modulo_ns = self::sanitizePathComponent(basename($_request->getModulo()));
                    $_class = '\\app\\' . $sanitized_grupo_ns . '\\' . $sanitized_modulo_ns . '\\' . $_internal_bootstrap_controller;
                } else {
                    $_class = '\\app\\' . $_internal_bootstrap_module . '\\' . $_internal_bootstrap_controller;
                }

                // Validar que la clase existe
                if (!class_exists($_class)) {
                    self::handleNotFound('Clase ' . htmlspecialchars($_class) . ' no encontrada');
                    return;
                }

                $_internal_bootstrap_controller_instance = new $_class;

                // Validar que el método es callable y está en la whitelist
                if (!is_callable(array($_internal_bootstrap_controller_instance, $internal_bootstrap_method))) {
                    self::handleNotFound('Acción ' . htmlspecialchars($internal_bootstrap_method) . ' no encontrada');
                    return;
                }

                // Ejecutar el método de forma segura
                if (!empty($parameters)) {
                    // Validar parámetros antes de pasarlos
                    $safe_parameters = self::sanitizeParameters($parameters);
                    call_user_func_array(array($_internal_bootstrap_controller_instance, $internal_bootstrap_method), $safe_parameters);
                } else {
                    call_user_func(array($_internal_bootstrap_controller_instance, $internal_bootstrap_method));
                }

            } else {
                self::handleNotFound('Controlador ' . htmlspecialchars($_internal_bootstrap_controller) . ' no encontrado en módulo ' . htmlspecialchars($_request->getModulo()));
            }

        } catch (\Exception $e) {
            // Log del error para debugging (sin exponer información sensible)
            error_log('Bootstrap error: ' . $e->getMessage());
            self::handleNotFound('Error interno del sistema');
        }
    }

    /**
     * Maneja errores 404 de forma segura
     */
    private static function handleNotFound($message)
    {
        $path404 = \Pan\kore\App::getPath404();

        if (!empty($path404) && self::isValidPath404($path404)) {
            require_once $path404;
        } else {
            ErrorPan::_showErrorAndDie(htmlspecialchars($message));
        }
        die;
    }

    /**
     * Valida que el path 404 sea seguro
     */
    private static function isValidPath404($path404)
    {
        $absolute_404_path = realpath($path404);

        if (!$absolute_404_path || !is_file($absolute_404_path)) {
            return false;
        }

        // Definir directorio base para archivos 404 (ajusta según tu estructura)
        $base_dir = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . '..');

        return self::isPathWithinBaseDirectory($absolute_404_path, $base_dir);
    }

    /**
     * Valida que un directorio existe y está dentro del directorio base
     */
    private static function isValidDirectory($directory_path, $base_dir)
    {
        $absolute_dir = realpath($directory_path);

        if (!$absolute_dir || !is_dir($absolute_dir)) {
            return false;
        }

        return self::isPathWithinBaseDirectory($absolute_dir, $base_dir);
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
     * Sanitiza nombres de métodos
     */
    private static function sanitizeMethodName($method)
    {
        // Solo permitir caracteres alfanuméricos y guiones bajos
        return preg_replace('/[^a-zA-Z0-9_]/', '', $method);
    }

    /**
     * Verifica que una ruta esté dentro del directorio base permitido
     */
    private static function isPathWithinBaseDirectory($path, $baseDir)
    {
        $path = realpath($path);
        $baseDir = realpath($baseDir);

        if ($path === false || $baseDir === false) {
            return false;
        }

        return strpos($path, $baseDir) === 0;
    }


    /**
     * Sanitiza parámetros antes de pasarlos al controlador
     */
    private static function sanitizeParameters($parameters)
    {
        if (!is_array($parameters)) {
            return [];
        }

        $safe_parameters = [];
        foreach ($parameters as $param) {
            if (is_string($param)) {
                // Eliminar null bytes y caracteres de control
                $param = str_replace(["\0", "\r"], '', $param);
            }
            $safe_parameters[] = $param;
        }

        return $safe_parameters;
    }



    private static function LoginAntiguo($request)
    {
        print_r($request->getModulo);die;
    }

}


