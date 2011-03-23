<?php
/*
 * Gestion des instanciations et executions de controller/action
 */
class Oscar_Front_Controller_Execute extends Oscar_Front_controller{



    private $_instance_controller   =   null;


    /*
     * Vérifie si le controller existe ,
     * s'il existe , il est inclue et instancié
     * la ressource du controller est retourné au front
     */
    public function load_controller( $controllerName , $_controller_Directory , $controller_proc = null ){

        
        try{

            //initialisation dir layout du controller
            $this->set_param("Oscar_dir_layouts", false);

            $tokenFind	=	0;
            //test l'existance d'apc
            $apc_on =   false;
            if( function_exists("apc_fetch")){
                $apc_on =   true;
            }

            //Récupération dans le cache s'il existe
            if( $apc_on ){

                $patharequire   =   null;
                $patharequire   =   apc_fetch("APC_".$this->get_signature()."_$controllerName");
                
                if( $patharequire   !=  false ){
                    
                    include_once $patharequire.$controllerName.'.php';

                    /*
                     * Définition du répertoire par défaut des vues
                     */
                    $default_layout_dir =   dirname($patharequire)."/layouts/";
                                        
                    $tokenFind	=	1;
                }

            }

            //si l'on a rien trouvé dans la cache
            if($tokenFind != 1){

                foreach($_controller_Directory->controllersDirectory AS &$path){

                    //recherche du controller dans les directory configurés
                    if( file_exists($path.$controllerName.'.php' )){
                            
                            require_once $path.$controllerName.'.php';

                            /*
                             * Définition du répertoire par défaut des vues
                             */
                            $default_layout_dir =    dirname($path)."/layouts/";

                            //si apc activé
                            if( $apc_on ){
                                //mise en cache pendant 30 minutes
                                apc_store  ( "APC_".$this->get_signature()."_$controllerName"  , $path  , 36000 );
                            }

                            $tokenFind	=	1;
                            continue 1;
                    }
                }

            }

            //vérifie que le controller a ete trouvé
            if($tokenFind == 0){
                
                    //Changement de l'etat du cycle
                    $controller_proc->stopCycle();
                    
                    throw new Exception("Client : ".$_SERVER['REMOTE_ADDR']." -  Le controller ".htmlentities($controllerName)." , n'a pas été trouvé ! ",100);
                    
            }else{

                if(class_exists($controllerName)){

                    /*
                     * Définition du répertoire par défaut contenant les vue du controller actif
                     */
                      if( is_dir($default_layout_dir) ){
                          $this->set_param("Oscar_dir_layouts", $default_layout_dir);
                      }

                    //instanciation du controller
                    $this->_instance_controller  =   new $controllerName();

                }else{
                    throw new Exception("Client : ".$_SERVER['REMOTE_ADDR']." - Le nom de la classe ".htmlentities($controllerName)." n'existe pas dans le fichier controller demandé ",101);
                }


            }

                                

        }catch(Exception $e){
            //echo $e->getMessage();

            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);
        }

        //retourne l'instance du controller
        return $this->_instance_controller;


    }





    /*
     * Lance les actions utilisateur
     */
    public function run_actions_user( $action_name = null , $controller_buffer = null , $controller_proc = null ){

        try{

            //Changement de l'etat du cycle
            $controller_proc->stopCycle();
            
            if( $this->_instance_controller != null){

                if( $action_name != null){

                    if($controller_buffer != null ){

                        if($controller_proc != null){

                            //commençons

                            //Vérifie si preaction existe , si oui alors on l'execute
                            if(method_exists($this->_instance_controller,"pre_action")  && !$controller_proc->skipToActivated() ){

                                    //demarre le buffer
                                    $controller_buffer->start();
                                            $controller_buffer->putInMemory(
                                                $this->_instance_controller->pre_action(),
                                                "pre_content"
                                            );
                                    //detruit le buffer , et arrete la tempo de sortie
                                    $controller_buffer->stop();
                            }


                            //Vérifie si init existe , si oui alors on l'execute
                            if(method_exists($this->_instance_controller, "init")  && !$controller_proc->skipToActivated() ){

                                    //demarre le buffer
                                    $controller_buffer->start();
                                            $controller_buffer->putInMemory(
                                                $this->_instance_controller->init(),
                                                "init_content"
                                            );
                                    //detruit le buffer , et arrete la tempo de sortie
                                    $controller_buffer->stop();

                            }




                            //Vérifie si l'action existe , si oui alors on l'execute
                            if(method_exists($this->_instance_controller, $action_name) && !$controller_proc->skipToActivated() ){

                                    //demarre le buffer
                                    $controller_buffer->start();
                                            $controller_buffer->putInMemory(
                                                $this->_instance_controller->$action_name(),
                                                "content"
                                            );
                                    //detruit le buffer , et arrete la tempo de sortie
                                    $controller_buffer->stop();

                            }elseif( !$controller_proc->skipToActivated() ){

                                    throw new Exception("Client : ".$_SERVER['REMOTE_ADDR']." - L'action demandée - " .htmlentities($action_name). " - n'est pas disponible ! ",102);

                            }




                            //Vérifie si postaction existe et que le jeton skipto_token soit à 0 , si oui alors on l'execute
                            if(method_exists($this->_instance_controller, "post_action") && !$controller_proc->skipToActivated() ){


                                    //demarre le buffer
                                    $controller_buffer->start();
                                            $controller_buffer->putInMemory(
                                                $this->_instance_controller->post_action(),
                                                "post_content"
                                            );
                                    //detruit le buffer , et arrete la tempo de sortie
                                    $controller_buffer->stop();

                            }

                            //supprime l'instance , le controller étant maintenant inutile
                            unset($this->_instance_controller);

                            //initialisation du skip to pour une nouvelle aventure s'il y en a une prochaine
                            $controller_proc->initialiseSkipTo();


                        }else{
                            throw new Exception("Ressource Proc non valide",103);
                        }

                    }else{
                        throw new Exception("Ressource Buffer non valide",104);
                    }

                }else{
                    throw new Exception("Aucune action recue !",105);
                }

        }else{
            throw new Exception("Client : ".$_SERVER['REMOTE_ADDR']." - Controller non valide !",106);
        }





        }catch(Exception $e){
            //echo $e->getMessage();

            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);

        }


    }



    /*
     * Permet aux controllers de s'appeller entre eux !
     */
    public function call_action_user( $action_name = null , $controller_proc = null , $controller_buffer = null ){

        try{

            if( $this->_instance_controller != null){

                if( $action_name != null){

                        if($controller_proc != null){

                            //commençons

                            //Vérifie si l'action existe , si oui alors on l'execute
                            if(method_exists($this->_instance_controller, $action_name) ){

                                    $controller_buffer->start();
                                            $controller_buffer->putInMemory(
                                                $this->_instance_controller->$action_name(),
                                                "_OscarFW_sortie_call"
                                            );
                                    //detruit le buffer , et arrete la tempo de sortie
                                    $controller_buffer->stop();

                            }else{

                                    throw new Exception("Client : ".$_SERVER['REMOTE_ADDR']." - L'action demandée - " .htmlentities($action_name). " - n'est pas disponible ! ",107);
                            }

                            //supprime l'instance , le controller étant maintenant inutile
                            unset($this->_instance_controller);

                        }else{
                            throw new Exception("Ressource Proc non valide",108);
                        }

                }else{
                    throw new Exception("Aucune action recue !",109);
                }

        }else{
            throw new Exception("Client : ".$_SERVER['REMOTE_ADDR']." - Controller non valide !",110);
        }





        }catch(Exception $e){
            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);
        }

    }



    /*
     * Recupére les services à lancer sous forme brut
     * les lance
     */
    public function executeServices( $Tservices , $controller_buffer , $controller_proc , $controller_params , $controller_directory ){

        if(is_array($Tservices)){

            foreach($Tservices AS &$serv ){

                //nb elem à prendre en compte ( url artificielle )
                $nbItem =   count($serv);

                if( $nbItem >=2){

                    $controller	=	$serv[0];
                    $action		=	$serv[1];

                    if($nbItem>2){

                        if(!is_null($serv[2]) && is_array($serv[2])){

                            foreach ( $serv[2] AS $key=>&$value ){
                                    $controller_params->set_param( $key , $value );
                            }

                        }
                    }

                    //lancement du service
                    $this->load_controller( $controller , $controller_directory );
                    $this->run_actions_user( $action , $controller_buffer, $controller_proc );

                    if($nbItem>2){

                        //Suppression des paramétres passés
                        if(!is_null($serv[2]) && is_array($serv[2])){

                            foreach ( $serv[2] AS $key=>&$value ){
                                    $controller_params->unset_param( $key , $value );
                            }

                        }
                    }

                }
            }//foreach
            
        }
    }
    
}
?>
