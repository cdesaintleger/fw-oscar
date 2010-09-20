<?php
/*
 * Gestion des services d'oscar
 */
class Oscar_Front_Controller_Services{


    private static $_Oscar_services =   array();

    /*
     * Ajoute un service de démarrage au Front_controller
     */
    public function add_service( $service ){

        try{
                if(is_array($service) && !empty($service) ){
                        array_push(self::$_Oscar_services, $service);
                }else{
                    throw new Exception('$service doit être un tableau non vide !',700);
                }
            }catch(Oscar_Exception $e){
                Oscar_Exception::getInstance()
                    ->error($e->getCode(),$e->getMessage(),null,null);
            }

    }

    /*
     * Retourne simplement le tableau des servies enregistrés
     */
    public function liste_Services(){

        return self::$_Oscar_services;

    }

}
?>
