<?php


namespace Pan\Migas;

require "app/MigasApp.php";
require "modules/MigasModule.php";
require "controllers/MigasController.php";
require "entities/MigasEntity.php";

use Pan\Migas\App\MigasApp;
use Pan\Migas\Entities\MigasEntity;
use Pan\Migas\Modules\MigasModule;
use Pan\Migas\Controllers\MigasController;


class Migas {

    protected $_arguments;

    protected $_parametros;

    protected $_second_parameter;

    protected $_third_parameter;


    public function __construct($arguments)
    {
        $this->_arguments = $arguments;
        $this->_parametros = explode('::',$arguments[1]);
        $this->_second_parameter = null;
        if(isset($arguments[2])){
            $this->_second_parameter= $arguments[2];
        }

        $this->_third_parameter = null;
        if(isset($arguments[3])){
            $this->_third_parameter = $arguments[3];
        }

    }

    public function make($parametros = null)
    {

    }


    public function destroy()
    {

    }

	private function getHelp($option="me"){

		$msg_help = "Ayuda rapida para Migas::PANDEMOLDE\n\n";

		$msg_help .= "php migas [option::action]\n\n";

		/** app */
		if($option == "me" || $option == "app"){
			$msg_help .= "- app::create\t\t\t\t\t\t\tCrear estructura de la aplicacion\n";	
		}
		
		/** module */
		if($option == "me" || $option == "module"){
			$msg_help .= "- module::MODULE_NAME\t\t\t\t\t\tCrear modulo con el nombre pasado en MODULE_NAME\n";	
		}

		/** controller */
		if($option == "me" || $option == "controller"){
			$msg_help .= "- controller::MODULE_NAME/CONTROLLER_NAME\t\t\tCrear controlador CONTROLLER_NAME dentro del modulo MODULE_NAME\n";	
		}

		/** controller */
		if($option == "me" || $option == "entity"){
			$msg_help .= "- entity::MODULE_NAME/ENTITY_NAME\t\t\t\tCrear entidad ENTITY_NAME dentro del modulo MODULE_NAME\n";	
		}

		return $msg_help;

	}


	public function run()
    {

        switch (strtolower($this->_parametros[0])){

            case 'help':
                echo $this->getHelp(strtolower($this->_parametros[1]));
                break;

            case 'app':
                MigasApp::make();
                break;

            case 'module' :
                MigasModule::make($this->_parametros);

                break;

            case 'entity' :
                MigasEntity::make($this->_arguments);
                break;

            case 'controller' :
                MigasController::make($this->_arguments);
                break;

        }
    }


}