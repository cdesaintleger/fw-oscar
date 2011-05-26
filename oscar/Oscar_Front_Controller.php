<?php
/**
 * Oscar Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Oscar Open Accessibility.
 * Copyright (c) 2007, 2008 Christophe DE SAINT LEGER. All rights reserved.
 *
 * Oscar Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Oscar Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Oscar Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Oscar_Front_Controller
 * @subpackage  Controller_Interface
 *
 */


/*
 * Oscar Controller Interface
 */
require_once 'front/Controller_Interface.php';
require_once 'front/Oscar_Exception.php';


/**
 * Class Oscar_Front_Controller.
 *
 * Implement the Controller_Interface.
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.2
 * @package     Oscar_Front_Controller
 * @subpackage  Controller_Interface
 */
class Oscar_Front_Controller implements Controller_Interface{


     /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     */
    private static $_instance = null;


    private static $_instance_controller_directory   =   null;


    private static $_instance_controller_services    =   null;


    private static $_instance_controller_params    =   null;


    private static $_instance_controller_url         =   null;


    private static $_instance_controller_execute     =   null;


    private static $_instance_controller_buffer      =   null;


    private static $_instance_controller_proc        =   null;


    private static $_instance_controller_layout      =   null;

    /*
     * Tableau contenant le nom d'un controller
     * et la fonction à executer , suite à un forward ou skipto
     */
    private static $_Tcycle    =   array();

    /*
     * Signature de l'application
     * qui evite le chevauchement du cache APC
     */
    private static $_signature  =   null;


    

    /*
     * Destination du log si choix du type = 1 || 3
     */
    protected static $_log_destination  =   null;

    /*
     * Entetes suplémentaire pour l'envoi de log par mail
     */
    protected static $_log_headers  =   null;


    










    public function __construct()
    {
    }

    /**
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton instance
     *
     */
    public static function getInstance()
    {
        if ( !isset( self::$_instance) ) {

            $c = __CLASS__;
            self::$_instance = new $c;

            /*
             * Constantes pour le type de log possible
             */
            define('OSCAR_LOG_HIST',0);
            define('OSCAR_LOG_MAIL',1);
            define('OSCAR_LOG_FILE',3);
            define('OSCAR_LOG_SAPI',4);

            /*
             * Constantes pour la gestion du type d'url utilisé
             */
            define('OSCAR_URL_ASSOC',"ASSOC");
            define('OSCAR_URL_NUM',"NUM");

            /*
             * Constantes pour le type de resultat retourné
             * par les fonction ORM
             */
            define( 'OSCAR_ORM_RES_ASSOC', PDO::FETCH_ASSOC );
            define( 'OSCAR_ORM_RES_NUM', PDO::FETCH_NUM );
            define( 'OSCAR_ORM_RES_BOTH', PDO::FETCH_BOTH );
            define( 'OSCAR_ORM_RES_OBJ', PDO::FETCH_OBJ );
            define( 'OSCAR_ORM_RES_LAZY', PDO::FETCH_LAZY );


            /*
             * Gestion par defaut des exceptions en standard
             *
             */
            Oscar_Exception::getInstance()
                ->attach(Oscar_Exception::factory('Std'));
        }

        return self::$_instance;
    }


    /**
     * Retourne une ressource du controller
     * de Directory d'oscar
     */
    private function get_instance_controller_directory(){
        
        if( !self::$_instance_controller_directory instanceof  Oscar_Front_Controller_Directory ){
            require_once 'front/Controller_Directory.php';
            self::$_instance_controller_directory   =   new Oscar_Front_Controller_Directory();
        }

        return self::$_instance_controller_directory;
    }




    /**
     * Retourne une ressource du controller
     * de Services d'oscar
     */
    private function get_instance_controller_services(){

        if( !self::$_instance_controller_services instanceof  Oscar_Front_Controller_Services ){
            require_once 'front/Controller_Services.php';
            self::$_instance_controller_services   =   new Oscar_Front_Controller_Services();
        }

        return self::$_instance_controller_services;
    }



    /**
     * Retourne une ressource du controller
     * de paramétres et IO d'oscar
     */
    private function get_instance_controller_params(){

        if( !self::$_instance_controller_params instanceof  Oscar_Front_Controller_Params ){
            require_once 'front/Controller_Params.php';
            self::$_instance_controller_params   =   new Oscar_Front_Controller_Params();
        }

        return self::$_instance_controller_params;
    }






    /**
     * Retourne une ressource du controller
     * d'url pour en deduire Controller & Action
     */
    private function get_instance_controller_url(){

        if( !self::$_instance_controller_url instanceof  Oscar_Front_Controller_Url ){
            require_once 'front/Controller_Url.php';
            self::$_instance_controller_url   =   new Oscar_Front_Controller_Url();
        }

        return self::$_instance_controller_url;
    }








    /**
     * Retourne une ressource du controller
     * d'url pour en deduire Controller & Action
     */
    private function get_instance_controller_execute(){

        if( !self::$_instance_controller_execute instanceof  Oscar_Front_Controller_Execute ){
            require_once 'front/Controller_Execute.php';
            self::$_instance_controller_execute   =   new Oscar_Front_Controller_Execute();
        }

        return self::$_instance_controller_execute;
    }





    /**
     * Retourne une ressource du controller
     * d'url pour en deduire Controller & Action
     */
    private function get_instance_controller_buffer(){

        if( !self::$_instance_controller_buffer instanceof  Oscar_Front_Controller_Buffer ){
            require_once 'front/Controller_Buffer.php';
            self::$_instance_controller_buffer   =   new Oscar_Front_Controller_Buffer();
        }

        return self::$_instance_controller_buffer;
    }




    /*
     * Instance du controller de process
     */
    private function get_instance_controller_proc(){

        if( !self::$_instance_controller_proc instanceof  Oscar_Front_Controller_Proc ){
            require_once 'front/Controller_Proc.php';
            self::$_instance_controller_proc   =   new Oscar_Front_Controller_Proc();
           
        }

        return self::$_instance_controller_proc;

    }





    /*
     * Instance du controller de vues
     */
    private function get_instance_controller_layout(){

        if( !self::$_instance_controller_layout instanceof  Oscar_Front_Controller_Layout ){
            require_once 'front/Controller_Layout.php';
            self::$_instance_controller_layout   =   new Oscar_Front_Controller_Layout();
        }

        return self::$_instance_controller_layout;

    }



    /*
     * Définition des chemins des controllers
     * Suppression des anciens s'il en existe
     */
    public function set_controller_directory( $path = null, $autoload = false, $pathauto = null ){

        if( $path != null){
            $this->get_instance_controller_directory()->set_controller_directory( $path, $autoload, $pathauto );
        }

    }


    /*
     * Ajoute un chemin vers des controllers
     */
    public function add_controller_directory( $path = null, $autoload = false, $pathauto = null ){

         $this->get_instance_controller_directory()->add_controller_directory( $path, $autoload, $pathauto );
        
    }


    /*
     * Supprime un chemin vers les controllers
     */
    public function remove_controller_directory( $path = null ){

         $this->get_instance_controller_directory()->remove_controller_directory( $path );

    }





    /*
     * Méthode autoload
     * Simplifier le chargement des models et autre ...
     * 
     */
    public static function _oscar_autoload( $className = null )
    {
        //Compatibilité Smarty V3
        $_class = strtolower($className);

        if( $className != null && (substr($_class, 0, 16) != 'smarty_internal_' && $_class != 'smarty_security') ){
            include_once $className.'.php';
        }
    }



    /*
     * Méthode pour compatibilité avec Vercion 1.x et 0.x
     */
    public function ZendEnable($modules){

        if( is_array($modules)){

            //si utilisation de zendRegistry
            if( $modules["registry"] === TRUE ){
                self::get_instance_controller_params()->enableZendRegistry();
            }

        }

    }






    /*
     * Ajoute un service de démarrage au Front_controller
     */
    public function add_service( $service=array() ){

        $this->get_instance_controller_services()->add_service( $service );

    }


    /*
     * Définition d'un paramétre dan l'objet
     */
    public function set_param( $name , $value, $nosecure = FALSE ,$silent=TRUE ){

        $this->get_instance_controller_params()->set_param( $name , $value, $nosecure ,$silent );

    }


    /*
     * Recupére un paramétre de façon sécurisé
     */
    public function get_param( $name = null, $silent=TRUE ){

        return $this->get_instance_controller_params()->get_param( $name );

    }


    /*
     * Check si le paramétre existe ( vide ou pas )
     */
    public function isRegistered( $name=null ){

        return $this->get_instance_controller_params()->isRegistered( $name );

    }


    public function __get( $name ){
        return $this->get_instance_controller_params()->get_param( $name );
    }

    public function __set( $name, $value ){
        $this->get_instance_controller_params()->set_param( $name , $value, TRUE );
    }


    /*
     * Permet d'utiliser les fonction isset et empty dans les controller
     * avec les attributs des objets Oscar_front_controller
     */
    public function __isset($key){
        
        if (isset($this->get_instance_controller_params()->$key)) {
            return empty($this->get_instance_controller_params()->$key);
        } else {
            return null;
        }
    }

    public function asArray($name,$key,$value,$nosecure = FALSE ,$silent=TRUE){
        $this->get_instance_controller_params()->set_asArray_param( $name , $key , $value, TRUE );
    }


    /*
     * retourne l'action en cours de traitement
     * uniquement suite à un forward ou skip to
     */
    public function get_action(){
        if(!empty(self::$_Tcycle)){
            return self::$_Tcycle["action"];
        }
    }


    /*
     * retourne le controller en cours de traitement
     * uniquement suite à un forward ou skip to
     */
    public function get_controller(){
        if(!empty(self::$_Tcycle)){
            return self::$_Tcycle["controller"];
        }
    }



    /*
     * retourne l'action en cours de traitement
     * 
     */
    public function get_url_action(){
        return $this->get_instance_controller_url()->get_url_action();
    }


    /*
     * retourne le controller en cours de traitement
     * 
     */
    public function get_url_controller(){
        return $this->get_instance_controller_url()->get_url_controller();
    }


    /*
     * Définition du controller par defaut .
     * Ne peut être utilisé qu'une fois au bootstrap
     * utf-8 imposé
     */
    public function set_default_controller( $value ){

        //ne doit pas pouvoir être appelé d'un controller
        if( !is_subclass_of  (  $this  ,  "Oscar_Front_Controller"  ) ){

            if( !empty( $value ) ){
                $this->get_instance_controller_url()->set_default_url_controller( $value );
            }

        }

    }
    /*
     * Definition de l'action par defaut
     * Ne peut être utilisé qu'une fois au bootstrap
     * utf-8 imposé
     */
    public function set_default_action( $value ){

        //ne doit pas pouvoir être appelé d'un controller
        if( !is_subclass_of  (  $this  ,  "Oscar_Front_Controller"  ) ){

            if( !empty( $value ) ){
                $this->get_instance_controller_url()->set_default_url_action( $value );
            }
            
        }

    }


    /*
     * Permet de modifier le style d'url
     * concernant les paramétres
     * soit ASSOC par defaut , les paramétres sont en format clef/valeur
     * soit NUM , ce sera donc valeur/valeur , assigné dans un tableau numérique
     */
    public function set_url_style( $value ){

        if( !empty( $value ) ){
            $this->get_instance_controller_url()->set_url_style_params( $value );
        }
    }



    /*
     * Definition/récupération de la signature de l'application
     */
    public function get_signature(  ){

        return self::$_signature;
        
    }

    public function set_signature(  $value ){

        self::$_signature   =   $value;

    }


    /*
     * Définition de la destination des log edité
     * soit un fichier ,
     * soit un mail
     */
    public function set_log_destination( $value ){

        self::$_log_destination =   $value;

    }

    /*
     * Definition des entêtes mail si le logs sont envoyé de cette façon
     */
    public function set_log_headers( $value ){

        self::$_log_headers =   $value;

    }






    /*
     * Decortique l'url passé pour en déduire
     * Controller action
     * et paramétres
     */
    private function _analyse_url($url = null ){

        $this->get_instance_controller_url()->analyse_url( $url,  $this->get_instance_controller_params() );

    }







    /*
     * Permet de lancer un autre cycle sans finir le cycle courant
     */
    public function _skipTo( $destination = null, $byService = FALSE ){
        
        if( $byService ){
            //met le flag breakedByServices à 1
            $this->get_instance_controller_proc()->breakByService();
        }else{
            //met le flag skipto à 1
            $this->get_instance_controller_proc()->StartSkipTo();
        }


        //si une nouvelle destination est presente
        if( !empty($destination) ){

                if(is_array($destination)){

                        //Programme la nouvelle action
                        $this->_forward( $destination );
                }

        }

    }





    /*
     * Programme un nouveau cycle
     * 
     */
    public function _forward( array $destination, $restartServices  =   FALSE ){

        try {

            if(is_array($destination)){

                if(count($destination)<2){
                        throw new Exception("les paramétres de la méthode _forward doivent être au minimum de deux ! ",001);
                }else{

                    self::$_Tcycle["controller"]	=	$destination[0];
                    self::$_Tcycle["action"]		=	$destination[1];
                    self::$_Tcycle['restart_service']   =       $restartServices;

                    if(count($destination) == 3)
                    {
                        if(isset($destination[2])){

                            if( is_array($destination[2])){

                                foreach ( $destination[2] AS $key=>&$value ){
                                        $this->set_param( $key , $value );
                                }
                            }

                        }
                    }
                }


                //réinitialise le jeton pour un nouveau traitement
                $this->get_instance_controller_proc()->initCycle();


            }else{
                    throw new Exception("Le methode _forward a besoin d'un tableau en paramétre ! ",002);
            }



        }catch (Exception $e){

            $this->get_instance_controller_proc()->stopCycle();

            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);

        }
        

    }




    /*
     * Mise en place du principe HMVC
     * Permettant à un controller d'appeler un autre controller ect ..
     */
    public function _call($controller, $action, $param, &$sortie = 'inactive'){

            /*
             * Gestion des paramétres en $_POST et GET
             */
            if( !empty($param)){
                
                switch( strtoupper($param["type"]) ){
                    case "POST":
                        foreach( $param["param"] AS $key	=>	&$value ){
                            //enregistrement sécurisé
                            $this->get_instance_controller_params()->set_param( "CPOST_".$key , $value );
                            //enregistrement dans le tableau POST
                            $_POST[$key]    =   $value;
                        }
                    break;

                    case "GET":
                        foreach( $param["param"] AS $key	=>	&$value ){
                            //enregistrement sécurisé
                            $this->get_instance_controller_params()->set_param( "CGET_".$key , $value );
                            //enregistrement dans le tableau POST
                            $_GET[$key]    =   $value;
                        }
                    break;
                }
            }

            //chargement et instanciantion du controller
            $this->get_instance_controller_execute()->load_controller(
                   $controller ,
                   $this->get_instance_controller_directory(),
                   $this->get_instance_controller_proc()
            );
            


            //Execution des action post init action et pre
            $this->get_instance_controller_execute()->call_action_user(
                $action ,
                $this->get_instance_controller_proc(),
                $this->get_instance_controller_buffer()
            );

            //Si l'on ne souhaite pas afficher la sortie
            if( $sortie != 'inactive' ){

                $sortie = $this->get_instance_controller_buffer()->getInMemory("_OscarFW_sortie_call");
                
            }else{
                //display data sord in buffer and clear it
                echo $this->get_instance_controller_buffer()->getInMemory("_OscarFW_sortie_call");

            }
            
        
    }




    /*
     * Envoie le contenu mis en mémoire
     * sur la sortie standard
     * Vide le buffer mais ne le coupe pas
     */
    public function _flush( $varname=null, $clearbuffer=FALSE ){

        //Si l'option d'effacement de buffer avant affichage est activé
        if($clearbuffer){
                $this->get_instance_controller_buffer()->clean();
        }

        if(!empty($varname)){
                echo $this->get_param( $varname );
        }
        //Et on balance le tt
        $this->get_instance_controller_buffer()->flush();
    }



    /*
     * Méthode qui active le layout et paramétre celui-ci
     */
    public function set_layout( array $params ){
        //initialise les donnes qui doivent l'être
        $this->get_instance_controller_layout()->set_smarty_layout( $this->get_instance_controller_proc() , $params );

    }



    /**
     * Arrete l'affichage du layout pour
     * l'action courante
     */
    public function stop_layout(){

            $this->get_instance_controller_proc()->disableSmartyTemplate();

    }


    /*
     * Interpréte la vue et la retourne
     */
    public function fetch($layoutName, array $params=null, array $options = null ){

        //instanciation de smarty
        $smarty = new Smarty_factory();
        //définition du chemin des templates
        $smarty->set_template_dir( $this->Oscar_dir_layouts );
        //définition du chemin des plugins
        $smarty->plugins_dir[] = $this->get_param('oscar_base').'/html/smarty/plugins/';

        //liaison des paramétres
        if(!empty($params)){
            $smarty->bind_params( $params );
        }
        //si des options sont passées on les actives
        if(!empty($options)){
            $smarty->set_options($options);
        }

        return $smarty->fetch($layoutName);
        
    }

    /*
     * Interpréte la vue à l'affiche à l'écran
     */
    public function display($layoutName, array $params=null, array $options = null ){

        //instanciation de smarty
        $smarty = new Smarty_factory();
        //définition du chemin des templates
        $smarty->set_template_dir( $this->Oscar_dir_layouts );
        //définition du chemin des plugins
        $smarty->plugins_dir[] = $this->get_param('oscar_base').'/html/smarty/plugins/';
        
        //liaison des paramétres
        if(!empty($params)){
            $smarty->bind_params( $params );
        }
        //si des options sont passées on les actives
        if(!empty($options)){
            $smarty->set_options($options);
        }

        //affichage
        $smarty->display($layoutName);

        unset($smarty);

    }





    /*
     * permet d'ecrire des log de façon simple et rapide depuis toute l'application
     * en ne paramétrant qsu'une fois les attributs
     */
    public function _log( $msg , $type , $dest=null , $headers=null ){

        //type ou la destination doit être définie
        $TchoiceWdest   =   array(1,3);

        if( empty($dest) && in_array($type,$TchoiceWdest)){
            
            //récupére la destination paramétré de façon global
            if( !empty(self::$_log_destination) ){
                $dest   =   self::$_log_destination;
            }else{
                throw new Exception("Une destination doit être définie pour ce systéme de log ! ",003);
            }
        }
        if( !empty($msg)){

            if( empty ($headers) ){
                $headers    =   self::$_log_headers;
            }

            $ret    =   error_log  ( htmlspecialchars($msg).PHP_EOL, $type  , $dest, $headers);

            if(!$ret){
                throw new Exception("Erreur : un probléme est survenu à l'ecriture des logs ! ",004);
            }
        }




        
    }




    /*
	 * Permet d'annuler l'affichage des differents contenus récupérés
	 * protected $_pre_content 	= 	null;
	 * protected $_init_content	=	null;
	 * protected $_content		=	null;
	 * protected $_post_content	=	null;
	 */
	public function _no_display( $display_list ){


            if(is_array($display_list)){
                foreach ( $display_list AS &$display ){
                    switch ($display){

                        case 'init':
                                 $this->get_instance_controller_proc()->statusDisableDisplay('init_content');
                        break;

                        case 'pre_action':
                                $this->get_instance_controller_proc()->statusDisableDisplay('pre_content');
                        break;

                        case 'action':
                                $this->get_instance_controller_proc()->statusDisableDisplay('content');
                        break;

                        case 'post_action':
                                $this->get_instance_controller_proc()->statusDisableDisplay('post_content');
                        break;

                        case 'none':
                                $this->get_instance_controller_proc()->statusInitDisplay();
                        break;

                        case 'all':
                                $this->get_instance_controller_proc()->statusDisableAllDisplay();
                        break;

                    }
                }
            }

	}



        /*
         * Retourne l'instance smarty de la vue principale
         */
        public function SmartyGetInstance(){

            return $this->get_instance_controller_layout()->SmartyGetInstance();

        }





    /*
     * Lancement de Oscar une sorte de chef d'orchestre
     */
    public function run(){
        
        //Definition de l'autoloader objet
        //spl_autoload_register(array('Oscar_Front_Controller','_oscar_autoload'));
        spl_autoload_register('Oscar_Front_Controller::_oscar_autoload');

        //vérification de la signature
        if( self::$_signature == null ){
            self::$_signature = sha1(getcwd());
        }

        /*
         * Initialise les sorties qui doivent être affichees
         */
        $this->get_instance_controller_proc()->statusInitDisplay();

        
        //analyse du chemin demandé par le client
        $this->_analyse_url($_SERVER['REQUEST_URI']);

        //Exection des services au démarrage
        $this->get_instance_controller_execute()->executeServices(
            $this->get_instance_controller_services()->liste_Services() ,
            $this->get_instance_controller_buffer(),
            $this->get_instance_controller_proc(),
            $this->get_instance_controller_params(),
            $this->get_instance_controller_directory()
        );


         //verifie si un service n'a pas demander l'interuption de l'execution
        if( !$this->get_instance_controller_proc()->breakedByService()){

            //chargement et instanciantion du controller
            $this->get_instance_controller_execute()->load_controller(
                   $this->get_instance_controller_url()->get_url_controller() ,
                   $this->get_instance_controller_directory(),
                   $this->get_instance_controller_proc()
            );

            //Execution des action post init action et pre
            $this->get_instance_controller_execute()->run_actions_user(
                $this->get_instance_controller_url()->get_url_action() ,
                $this->get_instance_controller_buffer(),
                $this->get_instance_controller_proc()
            );
            
        }



        //Vérification de l'etat du cycle en cas de relance ou de forward
        $limit_number_cycle =   0;
        while( !$this->get_instance_controller_proc()->cycleFinished() ){

            //regarde si les services au démarrages doivent encore être lancés
            if( self::$_Tcycle['restart_service'] == TRUE ){

                //Exection des services au démarrage
                $this->get_instance_controller_execute()->executeServices(
                    $this->get_instance_controller_services()->liste_Services() ,
                    $this->get_instance_controller_buffer(),
                    $this->get_instance_controller_proc(),
                    $this->get_instance_controller_params(),
                    $this->get_instance_controller_directory()
                );
                
            }

            //relance d'un cyle , lecture du nouveau controller et action / paramétres
            //chargement et instanciantion du controller
            $this->get_instance_controller_execute()->load_controller(
                   self::$_Tcycle['controller'] ,
                   $this->get_instance_controller_directory(),
                   $this->get_instance_controller_proc()
            );

            
            //Execution des action post init action et pre
            $this->get_instance_controller_execute()->run_actions_user(
                self::$_Tcycle['action'] ,
                $this->get_instance_controller_buffer(),
                $this->get_instance_controller_proc()
            );

            /*
             * Anti boucle sans fin
             */
            $limit_number_cycle++;
            if( $limit_number_cycle==50 ){
                self::$this->get_instance_controller_proc()->stopCycle();
                echo "Boucle sans fin detecté ! ";
            }
        }


        /*
         * Gestion de l'affichage via Smarty
         */
         $this->get_instance_controller_layout()->smartyDisplayMainView(
            $this->get_instance_controller_buffer(),
            $this->get_instance_controller_params(),
            $this->get_instance_controller_proc()
         );


  


    }







}
?>
