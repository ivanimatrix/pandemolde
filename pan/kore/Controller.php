<?php
namespace Pan\Kore;

use Pan\Utils\SessionPan as SessionPan;
use Pan\Kore\Request as Request;
use Pan\Kore\Response as Response;
use Pan\Kore\View as View;

abstract class Controller {
	

	protected $view;
    protected $request;
    protected $session;
    protected $response;




	public function __construct(){
        /*if(App::getSessionApp()){
            $this->session =  new panSession();
        }*/
        $this->session = new SessionPan();
        $this->request = new Request();
        $this->response = new Response();

        $this->view = new View();

        /*if(App::getTemplate() != ""){
            $template = App::getTemplate();
            $template = new $template();
            $this->view = $template->getTemplate();
        }*/

        $this->load = new Loader();

	}
}