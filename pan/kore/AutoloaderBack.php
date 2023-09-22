<?php
namespace Pan\Kore;

const DS = DIRECTORY_SEPARATOR;

require_once __DIR__ . '/../../app/app_database.php';
require_once __DIR__ . '/App.php';
require_once __DIR__ . '/../utils/ErrorPan.php';
require_once __DIR__ . '/../uri/Uri.php';
require_once __DIR__ . '/../db/DbStore.php';
require_once __DIR__ . '/../db/DbConexion.php';
require_once __DIR__ . '/../db/DbConexionAudit.php';
require_once __DIR__ . '/../db/DbQueryBuilder.php';
require_once __DIR__ . '/../utils/panFunc.php';
require_once __DIR__ . '/../utils/ValidatePan.php';
require_once __DIR__ . '/../utils/HashPan.php';
require_once __DIR__ . '/../utils/FilesPan.php';
require_once __DIR__ . '/../utils/JsonPan.php';
require_once __DIR__ . '/../utils/panMinify.php';
require_once __DIR__ . '/../utils/LoggerPan.php';
require_once __DIR__ . '/Entity.php';
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/Bootstrap.php';

class AutoloaderBack
{

    private static $directories = array(
        'entities',
        'libraries'
    );


    static public function loader($className)
    {
        $class = explode('\\',$className);
        $className = end($class);
        
        if (is_dir('../entities') and is_file('../entities' . DS . $className . '.php')) {
            require_once '../entities' . DS . $className . '.php';
        }

        if (is_dir('../libs') and is_file('../libs' . DS . $className . '.php')) {
            require_once '../libs' . DS . $className . '.php';
        }
    }
}

spl_autoload_register('\Pan\Kore\AutoloaderBack::loader');






