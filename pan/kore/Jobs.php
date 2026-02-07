<?php
namespace Pan\Kore;

/**
 * cargar el autoload para librerias en vendor
 */
require '../vendor/autoload.php';
require '../pan/kore/AutoloaderBack.php';

use Pan\Kore\Request as Request;
use Pan\Kore\Response as Response;

abstract class Jobs {
	

    protected $request;
    protected $response;


	public function __construct(){
        
        $this->request = new Request();
        $this->response = new Response();

	}
}