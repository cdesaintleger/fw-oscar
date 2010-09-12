<?php
/*
 * Interface du controller frontale 
 */
interface Controller_Interface{

    /*
     * Singleton du controller Frontale
     */
    public static function getInstance();

    /*
     * Autoloader d'oscar
     */
    public static function _oscar_autoload( $className = null );


    /*
     * Singleton d'une instance Smarty.
     * Celle de la vue principale
     */
    public function SmartyGetInstance();

    /*
     * Définition des chemins des controllers
     * Suppression des anciens s'il en existe
     */
    public function set_controller_directory( $path = null );

    /*
     * Ajoute un chemin vers des controllers
     */
    public function add_controller_directory( $path = null );

    /*
     * Supprime un chemin vers les controllers
     */
    public function remove_controller_directory( $path = null );

    /*
     * Ajoute un service de démarrage au Front_controller
     */
    public function add_service( $service=array() );


    public function get_param( $name = null, $silent=TRUE );

    /*
     * Retourne TRUE ou FALSE si l'attribut existe
     */
    public function isRegistered( $name=null );


    public function set_param ( $name , $value, $silent=TRUE );

    /*
     * Permet de récupérer le nom du controller ( defini par un forward )
     */
    public function get_controller();

    /*
    * Permet de récupérer le nom de l'action ( defini par un forward )
    */
    public function get_action();

    /*
     * Permet de récupérer le nom du controller ( defini par l'url )
     */
    public function get_url_controller();

    /*
     * Permet de récupérer le nom de l'action ( definie par l'url )
     */
    public function get_url_action();

    /*
     * Envoie le contenu du buffer sur la sortie standard
     * Vide le buffer , mais ne le coupe pas
     */
    public function _flush( $varname=null , $clearbuffer=FALSE);

   /*
    * Permet de lancer un autre duo controller / action
    * Sans terminer le couple courant
    */
    public function _skipTo( $destination=NULL );

    /*
     * Permet de lancer un autre duo controller / action
     */
    public function _forward( array $destination );

    /*
     * Lancement du controller
     * Analyses
     * Dispatch
     * Retour
     *
     */
    public function run();

    /*
     * Méthode qui active le layout ,
     * et paramétre celui-ci
     * Bind des variables
     */
    public function set_layout( array $params );

    /**
     * Arrete l'affichage du layout pour
     * l'action courante
     */
    public function stop_layout();

    /*
     * Permet d'annuler l'affichage des differents contenus récupérés
     * protected $_pre_content 	= 	null;
     * protected $_init_content	=	null;
     * protected $_content		=	null;
     * protected $_post_content	=	null;
     */
    public function _no_display( $display_list );

}
?>
