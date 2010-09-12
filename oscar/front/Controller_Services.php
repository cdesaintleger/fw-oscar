<?php
require_once 'Oscar_Exception.php';

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
                    throw new Oscar_Exception('$service doit être un tableau non vide !');
                }
            }catch(Oscar_Exception $e){
                echo $e->getMessage();
            }catch(Oscar_Exception $e){
                echo $e->getMessage();
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
