<?php
require_once 'Smarty/Smarty.class.php';
require_once 'Smarty_Factory.php';

class Oscar_Front_Controller{
	/*
	 * Instance du Controller Frontal , qui DOIT être unique
	 */
	protected static $_instance	=	null;
	
	/*
	 * Instance du gestionaire de vue principale Smarty
	 */
	protected static $_smarty_instance	=	null;
	
	/*
	 * Mode debug 
	 * Affiche ou non les erreur eventuelles de controller et actions
	 * Par defaut , à Off
	 */
	public static $_debug	=	FALSE;
	
	/*
	 * Liste des services qui seront toujours lancé avant chaque controller
	 */
	private static $_Oscar_services	=	array();
	
	/*
	 * Liste des chemins (repertoires) 
	 * qui contiennent les controllers
	 */
	protected static $controllersDirectory	=	array();
	
	/*
	 * Controller par defaut , si celui-ci 
	 * n'est pas spécifie dans l'url
	 */
	protected static $_controller	=	"Index";
	protected static $_url_controller	=	"Index";
	
	
	/*
	 * Action executé par defaut , 
	 * si celle-ci n'est pas spécifié dans 
	 * l'url
	 */
	protected static $_action		=	"defaut";
	protected static $_url_action		=	"defaut";
	
	/*
	 * Instance du controller courant , 
	 * qui lui aussi doit être unique
	 * 
	 */
	protected static $_instance_controller	=	null;
	
	/*
	 * Jeton , pour l'execution des méthodes du controller,
	 *  permet donc de lancer plusieurs controllers
	 * et actions differente , avant même qu'un seul affiche ne soit effectué
	 * sur le navigateur
	 */
	protected static $_token_init_object	=	0;
	
	
	/*
	 * Permet de savoir si le controller que l'on utilise
	 * provient d'un forward ou de l'url
	 */
	protected static $_from_url	=	TRUE;
	
	
	/*
	 * Jeton , de skip
	 * S'il vaut 1 , l'action dans laquelle il a ete appelé sera la derniére , toutes les autres 
	 * seront sautées 
	 * S'il vaut 0 , fonctionneent pas defaut
	 */
	protected static $_skipto_token	=	0;
	
	/*
	 * Variable contenant l'affichage de la méthode 
	 * pre_action() si celle ci est déclaré dans le controller
	 */
	protected static $_pre_content 	= 	null;
	
	/*
	 * Variable contenant l'affichage de la méthode init() du controller
	 * si celle si celle-ci est declarée
	 */
	protected static $_init_content	=	null;
	
	/*
	 * Variable contenant l'affichage de l'action demandée
	 */
	protected static $_content			=	null;
	
	/*
	 * Variable contenant l'affichage de la methode post_action()
	 * si celle-ci est declaré dans le controller
	 */
	protected static $_post_content	=	null;

	/*
	 * Par defaut aucun layout n'est definie 
	 * Il n'y aura donc pas d'utilisation 
	 * de smarty par defaut
	 */
	protected static $template_token	=	0;
	
	/*
	 * Nom du template à utiliser comme layout
	 */
	protected static $template	=	null;
	
	/*
	 * Liaison nom variable template Smarty
	 */
	protected static $bindSmarty	=	array();
	
	
	/*
	 * Tableau qui contient les types de contenu à ne pas afficher
	 * Permet d'annuler l'affichage automatique d'un ou plusieurs
	 * contenus pour certaines actions 
	 */
	private static $_TnoDisplay	=	array();
	
	
	/*
	 * Attributs public qui sont souvents utiles
	 */
	
	/*
	 * initialisé à l'appel de la méthode run()
	 * Permet de donner le realpath ( pour des inclusions parfaites )
	 */
	protected static $oscar_base	=	null;
	
	/*
	 * Constructeur mis en "protected" afin de générer
	 * une erreur si il y a une tentative 
	 * de creer une nouvelle instance
	 * sans utiliser le SINGLETON
	 */
	protected function __construct(){ }
		
	/*
	 * Vérouille la possibilité de cloner 
	 * l'objet
	 */
	private function __clone(){ }
		
	
		
	/*
	 * Singleton du controller frontal
	 */
	public static function getInstance(){

		if(is_null(self::$_instance)){
			self::$_instance	=	new self;
			
			//Initialisation du tableau d'affichage annulés
			self::$_instance->_no_display( array('none') );
		}
				
		return self::$_instance;
		
	}
	
	
	
	/*
	 * singleton de smarty du controller Frontal
	 */
	public static function SmartyGetInstance(){

		if(is_null(self::$_smarty_instance)){
			self::$_smarty_instance	=	new Smarty_factory();
		}
		return self::$_smarty_instance;
		
	}
	
	
	/**************************************************
	 * 												  *
	 *		Gestion des Chemins des Controllers		  *
	 * 												  *
	 **************************************************/
	
	
	
	/*
	 * Définition des chemins des controllers
	 * Suppression des anciens s'il en existe
	 */
	public function set_controller_directory( $path ){
		
		try{
		
			self::$controllersDirectory	=	array();
			
			if(!is_array($path)){
				
				if( is_dir($path)){
					
					self::$controllersDirectory[]	=	$path;
					
				}else{
					
					throw new Exception("Le chemin $path n'est pas valide ! ");
				}
			}else{
				foreach ( $path AS &$chemin ){
					
					if( is_dir($chemin)){
						
						self::$controllersDirectory[]	=	$chemin;
						
					}else{
						
						throw new Exception("Le chemin $chemin n'est pas valide ! ");
					}
				}
			}
			
		}catch (Exception $e){
			
			if(self::$_debug){
				print_r($e->getMessage());
				exit(1);
			}
		}
		
	}
	
	
	/*
	 * Ajoute un chemin vers des controllers
	 */
	public function add_controller_directory( $path ){
		
		if(!is_array($path)){
			if( is_dir($path)){
				self::$controllersDirectory[]	=	$path;
			}
		}		
	}
	
	
	
	/*
	 * Supprime un chemin vers les controllers
	 */
	public function remove_controller_directory( $path ){
		
		if(in_array( $path , self::$controllersDirectory )){
			self::$controllersDirectory	=	array_diff( self::$controllersDirectory , array($path) );
		}
	}
	
	
	/**************************************************
	 * 												  *
	 *		Gestion des services					  *
	 * 												  *
	 **************************************************/
	
	/*
	 * Ajoute un service de démarrage au Front_controller
	 */
	public function add_service( $service=array() ){
		
		try{
			if(is_array($service) && !empty($service) ){
				array_push(self::$_Oscar_services, $service);
			}else{
				
				throw new Exception('Erreur: Ajout d\'un service non valide !');
					
				return FALSE;
			}
		}catch(Exception $e){
			
			//Si le mode silencieux est inactif affichage de l'erreur
			if(self::$_debug){
				print_r($e->getMessage());
			}
			
		}
		
	}
	
	
	
	/**************************************************
	 * 												  *
	 *		Gestion des paramétres d'url			  *
	 * 												  *
	 **************************************************/
	
	
	public function get_param( $name = null, $silent=TRUE){
		
		try{
			if(  $this->isRegistered($name)){
				return self::$_instance->$name;
			}else{
				throw new Exception('Erreur: demande d\'acces à une variable qui n\'héxiste pas !');
				
				return FALSE;
			}
		}catch (Exception $e){
			//Si le mode silencieux est inactif affichage de l'erreur et blocage
			if(self::$_debug){
				if(!$silent){
					print_r($e->getMessage());
					exit(1);
				}
			}
			
		}
		
	}
	
	
	/*
	 * Retourne TRUE ou FALSE si l'attribut existe
	 */
	public function isRegistered( $name=null ){
	
		$reponse	=	FALSE;
		
		$Tab_obj_var	=	get_object_vars( self::$_instance );
				
		if(array_key_exists($name,$Tab_obj_var)){
			$reponse	=	TRUE;
		}
		
		return $reponse;

	}
	
	
	
	public function set_param ( $name , $value, $silent=TRUE ){
		
		try{
			if(empty( $name )){
				throw new Exception('Erreur: paramétre manquant $name fonction set_param !');
			}
			
			self::$_instance->$name	=	$value;
			
			return TRUE;
			
		}catch (Exception $e){

			if(self::$_debug){
				//Si le mode silencieux est inactif affichage de l'erreur et blocage
				if(!$silent){
					print_r($e->getMessage());
					exit(1);
				}
			}
			
			return FALSE;
		}
		
	}
	
	
	/*
	 * Permet de récupérer le nom du controller ( defini par un forward )
	 */
	public function get_controller(){
		
		return self::$_controller;
		
	}
	
	/*
	 * Permet de récupérer le nom de l'action ( defini par un forward )
	 */
	public function get_action(){
		
		return self::$_action;
		
	}
	
	
	/*
	 * Permet de récupérer le nom du controller ( defini par l'url )
	 */
	public function get_url_controller(){
		
		return self::$_url_controller;
		
	}
	
	/*
	 * Permet de récupérer le nom de l'action ( definie par l'url )
	 */
	public function get_url_action(){
		
		return self::$_url_action;
		
	}
	
	
	
	/*
	 * Méthodes majique brute sans vérification 
	 * de déclaration
	 */
	public function __get( $name ){
		
		return self::$_instance->$name;
		
	}
	
	
	public function __set( $name, $value ){
		
		self::$_instance->$name	=	$value;
		
	}
	
	
	
	
	
	
	/**************************************************
	 * 												  *
	 *				Analyse de l'url			      * 
	 * 												  *
	 **************************************************/
	
	
	
	
	private static function analyse(){

		//On récupére la requete de la demande
		$T_url	=	array();
		$T_url	=	explode('/',rawurldecode($_SERVER['REQUEST_URI']));
		
		
		$nb_elements	=	0;
		$nb_elements	=	count($T_url);
		
		switch($nb_elements){
			
			case 2:
				if(!empty($T_url[1])){
					self::$_url_controller	=	htmlentities(strip_tags($T_url[1]));
				}
				
			break;
			
			case 3:
				
				if(!empty($T_url[1])){
					self::$_url_controller	=	htmlentities(strip_tags($T_url[1]));
				}
				
				if(!empty($T_url[2])){
					self::$_url_action		=	htmlentities(strip_tags($T_url[2]));
				}
				
			break;
			
			case ($nb_elements>3):
				
				if(!empty($T_url[1])){
					self::$_url_controller	=	htmlentities(strip_tags($T_url[1]));
				}
				if(!empty($T_url[2])){
					self::$_url_action		=	htmlentities(strip_tags($T_url[2]));
				}
				
				for($i=3;$i<=$nb_elements;){
					
					if(!empty($T_url[$i]) ){
						self::$_instance->set_param(htmlentities(strip_tags($T_url[$i])) , htmlentities(strip_tags($T_url[$i+1])) );
					}
					
					$i	+= 2;
				}
				
			break;
		}
		
		
		/*
		 * Gestion des paramétres en $_POST et Get
		 */
		if(!empty($_POST)){
			
			foreach( $_POST AS $key	=>	&$value ){
				self::$_instance->set_param(htmlentities(strip_tags($key)) , htmlentities(strip_tags($value)));
			}
		}
		
		if(!empty($_GET)){
			
			foreach( $_GET As $key	=>	&$value ){
				self::$_instance->set_param(htmlentities(strip_tags($key)) , htmlentities(strip_tags($value)));
			}
		}
		
		
		//Copie dans controller et action
		self::$_controller	=	self::$_url_controller;
		self::$_action		=	self::$_url_action;
	}
	
	
	
	
	/**************************************************
	 * 												  *
	 *				Include Controller			      * 
	 * 												  *
	 **************************************************/
	
	
	
	
	/*
	 * Recherche et inclue le fichier du controller
	 */
	private static function _require_controller($from_url	=	TRUE){
		
		
		if( $from_url == TRUE ){
			
			$controller	=	self::$_url_controller;
			$action		=	self::$_url_action;
			
		}else{
			
			$controller	=	self::$_controller;
			$action		=	self::$_action;
			
		}
			
	//récupération du controller et recherche dans les fichiers
		try{
			
			if(!empty(self::$controllersDirectory)){
				
				$token	=	0;
				foreach(self::$controllersDirectory AS &$path){

					if(file_exists($path.$controller.'.php')){
						
						require_once $path.$controller.'.php';
						
						$token	=	1;
						continue 1;
					}
					
				}
				
				//vérifie que le controller a ete trouvé
				if($token == 0){
					throw new Exception("Erreur : Le controller ".htmlentities($controller)." , n'a pas été trouvé ! ");
				}
				
				
			}else{
				throw new Exception("Erreur : Pas de chemin de controllers déclaré ! ");
			}
			
		}catch (Exception $e){
			
			if(self::$_debug){
				print_r($e->getMessage());
			}
			
		}
		
	}
	
	
	
	
	/**************************************************
	 * 												  *
	 *				Execute Controller			      * 
	 * 												  *
	 **************************************************/
	
	
	
	/*
	 * Instancie et lance les méthodes :
	 *  
	 * pre_action
	 * init
	 * action 
	 * post_action
	 */
	private static function _initialise_object($from_url	=	TRUE){
		
		self::$_token_init_object	=	1;
						
		if($from_url == TRUE ){
		
			$controller	=	self::$_url_controller;
			$action		=	self::$_url_action;
			
		}else {
			
			$controller	=	self::$_controller;
			$action		=	self::$_action;
			
		}
		
		
		try {
			
			if(class_exists($controller)){
				
				//instancie le controller
				self::$_instance_controller	=	new $controller;
				
				//Vérifie si preaction existe , si oui alors on l'execute
				if(method_exists($controller,"pre_action")  && self::$_skipto_token==0 ){
					
					//demarre le buffer
					ob_start();
						//recupere le retour de pre_action()
						self::$_pre_content	=	self::$_instance_controller->pre_action();
						//recupere l'affichage de preaction ( si echo ou erreur )
						self::$_pre_content	.=	ob_get_contents();
					//detruit le buffer , et arrete la tempo de sortie
					ob_end_clean();
				}
				
				
				
				
				//Vérifie si init existe , si oui alors on l'execute
				if(method_exists($controller, "init")  && self::$_skipto_token==0 ){
					
					//demarre le buffer
					ob_start();
						//recupere le retour de init()
						self::$_init_content	=	self::$_instance_controller->init();
						//recupere l'affichage de init ( si echo ou erreur )
						self::$_init_content	.=	ob_get_contents();
					//detruit le buffer , et arrete la tempo de sortie
					ob_end_clean();
				}
				
				
				
				
				//Vérifie si l'action existe , si oui alors on l'execute
				if(method_exists($controller, $action) && self::$_skipto_token==0 ){
					
					//demarre le buffer
					ob_start();
						//recupere le retour de l'action
						self::$_content	=	self::$_instance_controller->$action();
						
						//recupere l'affichage de init ( si echo ou erreur )
						self::$_content	.=	ob_get_contents();
					//detruit le buffer , et arrete la tempo de sortie
					ob_end_clean();
				}elseif(self::$_skipto_token==0){
					
					throw new Exception("Erreur : L'action demandée n'est pas disponible ! ");
					
				}
				
				
				//Vérifie si postaction existe et que le jeton skipto_token soit à 0 , si oui alors on l'execute
				if(method_exists($controller, "post_action") && self::$_skipto_token==0 ){
					
					//demarre le buffer
					ob_start();
						//recupere le retour de post_action()
						self::$_post_content	=	self::$_instance_controller->post_action();
						//recupere l'affichage de postaction ( si echo ou erreur )
						self::$_post_content	.=	ob_get_contents();
					//detruit le buffer , et arrete la tempo de sortie
					ob_end_clean();
				}
				
				
			}else{
				throw new Exception("Erreur : La classe ".htmlentities($controller)." n'existe pas ! ");
			}
			
		}catch (Exception $e){
			
			if(self::$_debug){
				print_r($e->getMessage());
			}
						
		}
		
	}
	
	
	/*
	 * Envoie le contenu du buffer sur la sortie standard
	 * Vide le buffer , mais ne le coupe pas
	 */
	protected function _flush( $varname=null , $clearbuffer=FALSE){
		
		//Si l'option d'effacement de buffer avant affichage est activé
		if($clearbuffer){
			ob_clean();
		}
		
		if(!empty($varname)){	
			echo $this->$varname;
		}
		//Et on balance le tt
		ob_flush();
		
	}
	
	
	
	
	
	/**************************************************
	 * 												  *
	 *				SkipTo Controller			      * 
	 * 												  *
	 **************************************************/
	
	
	/*
	 * Permet de lancer un autre duo controller / action 
	 */
	protected function _skipTo( $destination=NULL ){
	
		//met le flag skipto à 1
		self::$_skipto_token	=	1;
		
	
		//si une nouvelle destination est presente
		if( !empty($destination) ){
		
			if(is_array($destination)){
						
				//Programme la nouvelle action
				$this->_forward($destination);
			}
		
		}
	
	}	
	
	
	
	
	
	
	/**************************************************
	 * 												  *
	 *				Forward Controller			      * 
	 * 												  *
	 **************************************************/
	
	
	
	
	
	/*
	 * Permet de lancer un autre duo controller / action 
	 */
	protected function _forward( array $destination ){
		
		try {
		
			if(is_array($destination)){
				
				if(count($destination)<2){
					throw new Exception("Erreur : les paramétres de la méthode _forward doivent être au minimum de deux ! ");
				}else{
					
					self::$_controller	=	$destination[0];
					self::$_action		=	$destination[1];
										
					if(!is_null($destination[2]) && is_array($destination[2])){
						
						foreach ( $destination[2] AS $key=>&$value ){
							
							$this->set_param(htmlentities(strip_tags($key)) , htmlentities(strip_tags($value)) );
							
						}
						
					}
				}
				
				
			}else{
				throw new Exception("Erreur : Le methode _forward a besoin d'un tableau en paramétre ! ");
			}
			
			//réinitialise le jeton pour un nouveau traitement 
			self::$_token_init_object	=	0;
			
			//Prévient que c'est un forward
			self::$_from_url	=	FALSE;
		
		}catch (Exception $e){
			
			self::$_token_init_object	=	1;
			
			if(self::$_debug){
				print_r($e->getMessage());
			}
			
		}
		
		
		
	}
	
	
	
	
	
	
	
	
	/**************************************************
	 * 												  *
	 *					RUN							  *
	 * 												  *
	 **************************************************/
	
	/*
	 * Lancement du controller
	 * Analyses
	 * Dispatch
	 * Retour
	 */
	public function run(){

		
		
		//Petit plus pour toujours avoir la base directory 
		$this->set_param("oscar_base",realpath('../'));
                /*
                 * Debugage php5.2->php5.3
                 *
                 */
		//$this->set_param("oscar_base",'../');
                
		//Analyse de l'url pour en ressortir controller / action / paramétres
		self::analyse();
		
		//Services qui doivent être lancés
		if(!empty(self::$_Oscar_services)){
						
			foreach( self::$_Oscar_services AS &$services ){
				
				//Lancement du service courant
				if(is_array($services)){
					
					self::_forward($services);
					
					self::_require_controller(self::$_from_url);
			
					self::_initialise_object(self::$_from_url);
					
					//reinitialise le jeton
					self::$_token_init_object	=	0;
				}
				
			}
			
			//on reinitialise les valeurs de $_controller et $_action
			//Copie dans controller et action des val de l'url
			self::$_controller	=	self::$_url_controller;
			self::$_action		=	self::$_url_action;
			
			//redefinie l'origine du controller
			self::$_from_url	=	TRUE;
		}
					
		//Execution des méthodes pre / init / action / post
		$debug	=	0;  
		while( self::$_token_init_object	==	0){
			
			self::_require_controller(self::$_from_url);
			
			self::_initialise_object(self::$_from_url);
			
			//remet le jeton skipto à 0 pour pouvoir faire la redirection s'il y en a une
			self::$_skipto_token = 0; 
			
			$debug++;
			$debug==50?self::$_token_init_object=1:null;
		}
		
		/*
		 * Il ne reste ici qu'à gérer l'affichage recupéré dans
		 * protected $_pre_content 	= 	null;
		 * protected $_init_content	=	null;
		 * protected $_content		=	null;
		 * protected $_post_content	=	null;
		 */ 
		
		//Layout actif
		if( self::$template_token != 0 ){
			
			//recupere l'instance de smarty
			$smarty = self::SmartyGetInstance();
			
			//definie les variables reservés
			$var_sys	=	array(
				"_pre_content",
				"_init_content",
				"_content",
				"_post_content"
			);
			
			//lie les nom aux variables
			foreach( self::$bindSmarty AS $key	=>	$name ){
				
				if(in_array($name,$var_sys)){

					//Verification si l'affichage de ce module n'est pas annulé
					if(self::$_TnoDisplay[$name]	==	FALSE ){
					
						$smarty->assign($key,self::$$name);
						
					}
					
				}else{
					
					$smarty->assign($key,self::get_param($name));
					
				}
				
				
				
			}
			//affiche le template ( à nous d'y mettre ce que l'on veut ou l'on veut )
			$smarty->display(self::$template);
			
		}else{
			//Affichage du tout :-)
			//Verification si l'affichage de ce module n'est pas annulé
			if(self::$_TnoDisplay['_pre_content']	==	FALSE ){
				echo self::$_pre_content;
			}
			
			if(self::$_TnoDisplay['_init_content']	==	FALSE ){
				echo self::$_init_content;
			}
			
			if(self::$_TnoDisplay['_content']	==	FALSE ){
				echo self::$_content;
			}
			
			if(self::$_TnoDisplay['_post_content']	==	FALSE ){
				echo self::$_post_content;
			}
		}		
					
	}
	
	
	
	
	
	
	/*
	 * Méthode qui active le layout , 
	 * et paramétre celui-ci 
	 * Bind des variables
	 */
	public function set_layout( array $params ){
		
		try{
			
			if(is_array($params) && !empty($params)){
				
				//récupére une instance de smarty
				$smarty	=	self::SmartyGetInstance();
				
				/*
				 * on recupere le template à utiliser qui se trouve par defaut dans 
				 * /html/smarty/templates
				 */ 
				if(!empty($params['dir_tpls']) && is_dir($params['dir_tpls'])){
					$smarty->template_dir	=	$params['dir_tpls'];
				}
				if(!empty($params['template'])){
					self::$template	=	$params['template'];
				}else{
					throw new Exception("Erreur : Un template doit être definie et lisible !");
				}
				if( !is_readable($smarty->template_dir.self::$template )){
					throw new Exception("Erreur : Un template doit être definie et lisible !");
				}
				//Liaison des nom->val
				if(is_array($params['binding']) && !empty($params['binding'])){
					
					self::$bindSmarty	=	$params['binding'];
					
				}
				
				//Active ou non le cache de smarty
				switch($params['cache']){
					case true:
						$smarty->caching = true;
					break;
					
					case false:
						$smarty->caching = false;
					break;
					
					default:
						$smarty->caching = false;
					break;
				}
				
				
				//Informe de l'activation du layout
				self::$template_token	=	1;
				
			}else{
				throw new Exception("Erreur : Set_Layout à besoin d'un tableau en paramétre !");
			}
			
		}catch (Exception $e){
			if(self::$_debug){
				print_r($e->getMessage());
				exit(1);
			}
		}
		
		
	}




        
	
	
	
	
	/**
	 * Arrete l'affichage du layout pour 
	 * l'action courante
	 */
	public function stop_layout(){
		
		self::$template_token	=	0;
		
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
						self::$_TnoDisplay['_init_content']	=	TRUE;
					break;
					
					case 'pre_action':
						self::$_TnoDisplay['_pre_content']	=	TRUE;
					break;
					
					case 'action':
						self::$_TnoDisplay['_content']	=	TRUE;
					break;
					
					case 'post_action':
						self::$_TnoDisplay['_post_content']	=	TRUE;
					break;
					
					case 'all':
						self::$_TnoDisplay['_post_content']	=	TRUE;
						self::$_TnoDisplay['_content']	=	TRUE;
						self::$_TnoDisplay['_pre_content']	=	TRUE;
						self::$_TnoDisplay['_init_content']	=	TRUE;
					break;
					
					case 'none':
						self::$_TnoDisplay['_post_content']	=	FALSE;
						self::$_TnoDisplay['_content']	=	FALSE;
						self::$_TnoDisplay['_pre_content']	=	FALSE;
						self::$_TnoDisplay['_init_content']	=	FALSE;
					break;
					
				}
			}
		}
		
	}
	
}
?>