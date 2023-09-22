<?php
session_start();

//error_reporting(E_ALL); ini_set('display_errors', 1);

/**
 * cargar el autoload para librerias en vendor
 */
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/pan/kore/Autoloader.php';

$app = require_once __DIR__.'/pan/bootstrap/app.php';

$app->init();



