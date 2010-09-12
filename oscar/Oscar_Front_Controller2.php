<?php

require_once 'front/Controller_Interface.php';

class Oscar_Front_Controller implements Controller_Interface{


     /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     */
    protected static $_instance = null;


    protected $_instance_controller_directory   =   null;


    protected $_instance_controller_services    =   null;


    protected $_instance_controller_params    =   null;


    protected $_instance_controller_url         =   null;


    protected $_instance_controller_execute     =   null;


    protected $_instance_controller_buffer      =   null;


    protected $_instance_controller_proc        =   null;


    protected $_instance_controller_layout      =   null;

    /*
     * Tableau contenant le nom d'un controller
     * et la fonction à executer , suite à un forward ou skipto
     */
    private static $_Tcycle    =   array();


    










    protected function __construct()
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
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /**
     * Retourne une ressource du controller
     * de Directory d'oscar
     */
    private function get_instance_controller_directory(){
        
        if( !$this->_instance_controller_directory instanceof  Oscar_Front_Controller_Directory ){
            require_once 'front/Controller_Directory.php';
            $this->_instance_controller_directory   =   new Oscar_Front_Controller_Directory();
        }

        return $this->_instance_controller_directory;
    }




    /**
     * Retourne une ressource du controller
     * de Services d'oscar
     */
    private function get_instance_controller_services(){

        if( !$this->_instance_controller_services instanceof  Oscar_Front_Controller_Services ){
            require_once 'front/Controller_Services.php';
            $this->_instance_controller_services   =   new Oscar_Front_Controller_Services();
        }

        return $this->_instance_controller_services;
    }



    /**
     * Retourne une ressource du controller
     * de paramétres et IO d'oscar
     */
    private function get_instance_controller_params(){

        if( !$this->_instance_controller_params instanceof  Oscar_Front_Controller_Params ){
            require_once 'front/Controller_Params.php';
            $this->_instance_controller_params   =   new Oscar_Front_Controller_Params();
        }

        return $this->_instance_controller_params;
    }






    /**
     * Retourne une ressource du controller
     * d'url pour en deduire Controller & Action
     */
    private function get_instance_controller_url(){

        if( !$this->_instance_controller_url instanceof  Oscar_Front_Controller_Url ){
            require_once 'front/Controller_Url.php';
            $this->_instance_controller_url   =   new Oscar_Front_Controller_Url();
        }

        return $this->_instance_controller_url;
    }








    /**
     * Retourne une ressource du controller
     * d'url pour en deduire Controller & Action
     */
    private function get_instance_controller_execute(){

        if( !$this->_instance_controller_execute instanceof  Oscar_Front_Controller_Execute ){
            require_once 'front/Controller_Execute.php';
            $this->_instance_controller_execute   =   new Oscar_Front_Controller_Execute();
        }

        return $this->_instance_controller_execute;
    }





    /**
     * Retourne une ressource du controller
     * d'url pour en deduire Controller & Action
     */
    private function get_instance_controller_buffer(){

        if( !$this->_instance_controller_buffer instanceof  Oscar_Front_Controller_Buffer ){
            require_once 'front/Controller_Buffer.php';
            $this->_instance_controller_buffer   =   new Oscar_Front_Controller_Buffer();
        }

        return $this->_instance_controller_buffer;
    }




    /*
     * Instance du controller de process
     */
    private function get_instance_controller_proc(){

        if( !$this->_instance_controller_proc instanceof  Oscar_Front_Controller_Proc ){
            require_once 'front/Controller_Proc.php';
            $this->_instance_controller_proc   =   new Oscar_Front_Controller_Proc();
           
        }

        return $this->_instance_controller_proc;

    }





    /*
     * Instance du controller de vues
     */
    private function get_instance_controller_layout(){

        if( !$this->_instance_controller_layout instanceof  Oscar_Front_Controller_Layout ){
            require_once 'front/Controller_Layout.php';
            $this->_instance_controller_layout   =   new Oscar_Front_Controller_Layout();
        }

        return $this->_instance_controller_layout;

    }



    /*
     * Définition des chemins des controllers
     * Suppression des anciens s'il en existe
     */
    public function set_controller_directory( $path = null ){

        if( $path != null){
            $this->get_instance_controller_directory()->set_controller_directory( $path );
        }

    }


    /*
     * Ajoute un chemin vers des controllers
     */
    public function add_controller_directory( $path = null ){

         $this->get_instance_controller_directory()->add_controller_directory( $path );
        
    }


    /*
     * Supprime un chemin vers les controllers
     */
    public function remove_controller_directory( $path = null ){

         $this->get_instance_controller_directory()->remove_controller_directory( $path );

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
    public function set_param( $name , $value, $silent=TRUE ){

        $this->get_instance_controller_params()->set_param( $name , $value, $silent );

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
    public function _skipTo( $destination = null ){

        //met le flag skipto à 1
	$this->get_instance_controller_proc()->StartSkipTo();


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
    public function _forward( array $destination ){

        try {

            if(is_array($destination)){

                if(count($destination)<2){
                        throw new Oscar_Exception("Erreur : les paramétres de la méthode _forward doivent être au minimum de deux ! ");
                }else{

                    self::$_Tcycle["controller"]	=	$destination[0];
                    self::$_Tcycle["action"]		=	$destination[1];

                    if(!is_null($destination[2]) && is_array($destination[2])){

                            foreach ( $destination[2] AS $key=>&$value ){
                                    $this->set_param( $key , $value );
                            }

                    }
                }


                //réinitialise le jeton pour un nouveau traitement
                $this->get_instance_controller_proc()->initCycle();


            }else{
                    throw new Oscar_Exception("Erreur : Le methode _forward a besoin d'un tableau en paramétre ! ");
            }



        }catch (Oscar_Exception $e){

            $this->get_instance_controller_proc()->stopCycle();
            echo $e->getMessage();
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
        $this->get_instance_controller_proc()->statusInitDisplay();
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

        //Exection des services au démarrage
        $this->get_instance_controller_execute()->executeServices(
            $this->get_instance_controller_services()->liste_Services() ,
            $this->get_instance_controller_buffer(),
            $this->get_instance_controller_proc(),
            $this->get_instance_controller_params(),
            $this->get_instance_controller_directory()
        );




        //analyse du chemin demandé par le client
        $this->_analyse_url($_SERVER['REQUEST_URI']);
        
        //chargement et instanciantion du controller
        $this->get_instance_controller_execute()->load_controller(
               $this->get_instance_controller_url()->get_url_controller() ,
               $this->get_instance_controller_directory()
        );
               
        //Execution des action post init action et pre
        $this->get_instance_controller_execute()->run_actions_user(
            $this->get_instance_controller_url()->get_url_action() ,
            $this->get_instance_controller_buffer(),
            $this->get_instance_controller_proc()
        );



        //Vérification de l'etat du cycle en cas de relance ou de forward
        $limit_number_cycle =   0;
        while( !$this->get_instance_controller_proc()->cycleFinished() ){

            //relance d'un cyle , lecture du nouveau controller et action / paramétres
            //chargement et instanciantion du controller
            $this->get_instance_controller_execute()->load_controller(
                   self::$_Tcycle['controller'] ,
                   $this->get_instance_controller_directory()
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
