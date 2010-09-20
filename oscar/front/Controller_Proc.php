<?php
/*
 * Gestion des "process" flag des executions de services
 * actions et vues
 */
class Oscar_Front_Controller_Proc{


    /*
     * Jeton de saut ,
     * permet d'arreter la progression du process actuel 
     */
    private static $_skipTo    =   0;


     /*
     * Jeton permet aux service de stopper toute execution suivante
     * 0 , le cycle n'est pas encore terminé ,
     * 1 , le cycles est abordé
     */
    private static $_break_by_service   =   0;


    /*
     * Jeton d'etat de l'execution du processactuel
     * 0 , le cycle n'est pas encore terminé ,
     * 1 , le cycles est terminé , l'on peut passer
     * à  l'affichage
     */
    private static $_action_executed   =   0;



    /*
     * Template smarty par defaut desactive
     */
    private static $_sarty_template_activated   =   0;



    /*
     * Tableau de gestion des affichages ou non
     */
    private static $_TdisplayZone   =   array();



    /*
     * Vérifie l'etat du jeton skipTo
     */
    public function skipToActivated(){

        return self::$_skipTo;

    }

    /*
     * Reinitialise le jeton skip to
     */
    public function initialiseSkipTo(){

         self::$_skipTo  =   0;

    }

    /*
     * Change d'etat le jeton skipto
     */
    public function StartSkipTo(){

         self::$_skipTo  =   1;

    }


    /*
     * Retourne le jeton
     */
    public function breakedByService(){

        return self::$_break_by_service;

    }


    /*
     * Change d'etat le jeton pour stopper l'appli par les services
     */
    public function breakByService(){

        self::$_break_by_service    =   1;

    }


    /*
     * Retourne l'etat du cyle de fonction utilisateur
     */
    public function cycleFinished(){

        return  self::$_action_executed;

    }


    /*
     * Passage à l'etat 1 du cycle principale
     */
    public function stopCycle(){

         self::$_action_executed =   1;

    }

    /*
     * Reinitialise le jeton du cycle
     */
    public function initCycle(){

         self::$_action_executed =   0;

    }



    /*
     * Template smarty activé ou non 
     */
    public function activeSmartyTemplate(){

        self::$_sarty_template_activated  =   1;

    }


    public function disableSmartyTemplate(){

        self::$_sarty_template_activated  =   0;

    }

    /*
     * Retourne l'etat du jeton 
     */
    public function statusSmartyTemplate(){

        return self::$_sarty_template_activated;

    }




    public function statusInitDisplay(  ){

        self::$_TdisplayZone['content'] =   TRUE;
        self::$_TdisplayZone['pre_content'] =   TRUE;
        self::$_TdisplayZone['init_content'] =   TRUE;
        self::$_TdisplayZone['post_content'] =   TRUE;

    }

    public function statusDisableAllDisplay( ){

        self::$_TdisplayZone['content'] =   FALSE;
        self::$_TdisplayZone['pre_content'] =   FALSE;
        self::$_TdisplayZone['init_content'] =   FALSE;
        self::$_TdisplayZone['post_content'] =   FALSE;

    }

    /*
     * Désactive l'affichage d'une des principales donnes
     * content
     * init
     * ..
     * ..
     */
    public function statusDisableDisplay( $zone = null ){

        if( !empty( $zone ) ){

            if( in_array($zone, self::$_TdisplayZone) ){
                self::$_TdisplayZone[$zone] =   FALSE;
            }else{
                throw new Exception("Changement d'etat d'un affichage qui n'existe pas !",600);
            }

        }
        
    }

    public function statusEnableDisplay( $zone = null ){

        if( !empty( $zone ) ){

            if( in_array($zone, self::$_TdisplayZone) ){
                self::$_TdisplayZone[$zone] =   TRUE;
            }else{
                throw new Exception("Changement d'etat d'un affichage qui n'existe pas !",601);
            }

        }
    }


    public function getStatusDisplay( $zone = null ){

        if( !empty( $zone ) ){

            if( in_array($zone, self::$_TdisplayZone) ){
                return self::$_TdisplayZone[$zone];
            }else{
                return FALSE;
            }

        }

    }

}
?>
