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
 * @category    Oscar library
 * @package     Oscar_Identification
 * @subpackage
 *
 */


 /**
 * Class Oscar_Identification.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_Identification
 * @subpackage
 */
class Oscar_Identification_2{
	
	private static $_instance	=	null;
	
	/*
	 * Paramétres changeables 
	 * Paramétre de la tables temporaire 
	 * de vérification de session
	 */
	
	/*
	 * Grain de sel , qui complique les données
	 * Mettre ce que l'on veut
	 */
	private static $_grain_de_sel	=	"indamix";
	
	
	/*
	 * Type d'authentification
	 * session
	 * cookie
	 * dual
	 */
	private static $_type_auth	=	"session";
	
	
	
	private static $_myCrypt	=	"sha1";
		
	
	/*
	 * Durrée de vie de la session
	 * Par defaut 0 ( jusqu'à la fermeture du navigateur )
	 */
	private static $_session_lifeTime	=	0;
	
	
	/*
	 * Durrée de vie du cookies
	 * Par defaut 0 ( jusqu'à la fermeture du navigateur )
	 */
	private static $_cookie_lifeTime	=	0;
	
	
	/*
	 * Champs à ajouter dans la session/cookie 
	 * ce tableau contiend 1 tableau par champ à ajouter 
	 * 
	 * array( <nom_champ_ddb> , <nom_ds_session> , <crypte> )
	 */
	private static $_personal_fields_in_session	=	array();
	
	/*
	 * Champs à récupérer et sauvegarder dans l'objet , 
	 * afin qu'ils soient accessible uniquement 
	 * via le code ( invisible dans le cookie et la session )
	 * 
	 * array( <nom_champ_ddb> , <nom_ds_session> , <crypte> )
	 */
	private static $_personal_fields_in_object =	array();
	
	/*
	 * Données sauvegardées dans l'objet
	 */
	private static $_private_data	=	array();

    /*
     * Activation ou non des log d'identification
     */
    private static $_active_log =   FALSE;

    /*
     * Fichier sans lequel il faut ecrire les logs
     */
    private static $_logfile = "/dev/null";

    /*
     * Log user data
     */
    private static $_user_log = "";


    /*
     * Vérification supplémentaire
     * Par l'ip du client
     */
    private static $_test_ip    =   TRUE;


    /*
     * Vérification supplémentaire
     * Par le navigateur
     */
    private static $_test_nav   =   TRUE;


    /*
     * Driver de la base de stockage
     *
     */
    private static $_driver =   null;
	
	
	
	/*
	 * Constructeur
	 * 
	 */
	private function __construct( $typeauth="session"){
				
		self::$_type_auth	=	$typeauth;
		
	}
	
	/* Liaison avec le driver d'acces aux données persistantes */
    public function set_driver( $driver=null ){

        if( $driver != null ){
            self::$_driver  =   $driver;
        }

    }

	/*
	 * Singleton 
	 */
	public static function getInstance(){

		if(is_null(self::$_instance)){
			self::$_instance	=	new self;
		}
				
		return self::$_instance;
		
	}
	
	/*
	 * Définition de la table contenant les login/mdp
	 */
	public function set_ident_table_name( $value ){
		
		(!empty($value))?self::$_ident_table_name	=	$value:null;
		
	}
	
	
	/*
	 * Définition de la table contenant les connexions et infos secrete
	 * Par defaut : Oscar_temp_ident ( 1 nom != par domaine )
	 */
	public function set_Oscar_temp_table_name( $value ){
		
		(!empty($value))?self::$_oscar_temp_table_name	=	$value:null;
		
	}
	
	/*
	 * Définition du champ contenant les logins
	 */
	public function set_login_field_name( $value ){
		
		(!empty($value))?self::$_login_field_name	=	$value:null;
		
	}
	
	/*
	 * Définition du champ contenant les mots de passe
	 */
	public function set_password_field_name( $value ){
		
		(!empty($value))?self::$_password_field_name	=	$value:null;
		
	}
	
	/*
	 * Définition du type de sauvegarde souhaité session/cookie
	 */
	public function set_type_auth( $value ){
		
		(!empty($value))?self::$_type_auth	=	$value:null;
		
	}
	
	/*
	 * Définition du mode de cryptage à utiliser
	 */
	public function set_type_crypt( $value ){
		
		(!empty($value))?self::$_myCrypt	=	$value:null;
		
	}
	
	
	/*
	 * Définition du grain de sel 
	 */
	public function set_grain_de_sel( $value ){
		
		(!empty($value))?self::$_grain_de_sel	=	$value:null;
		
	}
	
	
	
	/*
	 * Determine la durée de vie de la session
	 */
	public function set_session_lifeTime( $seconds ){
		
		(!empty($seconds))?self::$_session_lifeTime	=	intval($seconds):null;
		
	}
	
	/*
	 * Determine la durée de vie du cookie
	 */
	public function set_cookie_lifeTime( $seconds ){
		
		$time	=	time();
		
		(!empty($seconds))?self::$_cookie_lifeTime	=	intval($time+$seconds):null;
		
	}
	

        /*
	 * Active l'ecriture de log
	 */
	public function set_active_log( $value ){

		(!empty($value))?self::$_active_log	=	$value:null;

	}

        /*
	 * Definie le fichier d'ecriture de log
	 */
	public function set_logfile( $value ){

		(!empty($value))?self::$_logfile	=	$value:null;

        }


        /*
	 * Log de l'utilisateur à inserer dans les log par defaut
	 */
	public function set_log_user( $value ){

		(!empty($value))?self::$_user_log	=	$value:null;

        }



        /*
         * Active ou désactive les vérification client supplémentaires
         */
        public function set_test_supplementaire( $test , $onoff){

            if( is_bool($onoff)){

                switch($test){

                    case 'ip':
                        self::$_test_ip =   $onoff;
                    break;

                    case 'navigateur':
                        self::$_test_nav =   $onoff;
                    break;

                }

            }

        }

	
	/*
	 * Ajoute un champ à ajouter à la creation de/du la session/cookie
	 * accessible par session/cookie 
	 */
	public function add_in_session( $field , $name , $crypt=FALSE ){
		
		if(!empty($field) && !empty($name)){
			
			self::$_personal_fields_in_session[]	=	array(
				"field_name" => $field,
				"session_name"	=>	$name,
				"crypt"	=>	$crypt
			);
			
		}
		
	}
	
	
	
	
	public function add_in_object( $field , $name , $crypt=FALSE ){
		
		if(!empty($field) && !empty($name)){
			
			self::$_personal_fields_in_object[]	=	array(
				"field_name" => $field,
				"data_name"	=>	$name,
				"crypt"	=>	$crypt
			);
			
		}
		
	}
	
	
	
	/*
	 * Retourne une donnée privé 
	 */
	public function get_private_data( $name ){
		
		if(!empty($name)){
			
			if(key_exists($name,self::$_private_data)){
				
				return self::$_private_data[$name];
				
			}
			
		}
		
	}
	
	
	
	
	
	/*
	 * Retourne TRUE ou FALSE , en fonction du fait 
	 * que l'utilisateur soit identifié ou non
	 */
	public function isIdentified(){
		
		//par defaut , on est pas identifié
		$retour	=	FALSE;

                				
		/*
		 * 1 vérification de l'existance de la session
		 */
		if($this->_session_existe()){

                        switch(self::$_type_auth){

                                case "session":

                                        $loginconcerne	=	$_SESSION['Oscar_i']['login'];

                                break;

                                case "cookie":

                                        $loginconcerne	=	$_COOKIE['Oscar_i']['login'];

                                break;
                        }
			
			/*
			 * Compare les données de session 
			 * avec les infos client
			 */
			if($this->_session_client_is_valide()){
				
				$retour	=	TRUE;
				
			}else{

                            //Log de l'a tentative de la deconnexion
                            if(self::$_active_log){

                               
                                error_log(date("Y/m/d G:i:s")." Connexion à partir d'un autre site ".long2ip($this->_ipclient())." , ou tentative de vol de session : Déconnexion de ".$loginconcerne." - ".self::$_user_log." -".PHP_EOL, 3, self::$_logfile);

                            }

                        }
			
		}else{
                    //Log de l'a tentative de la deconnexion
                    if(self::$_active_log){
                        error_log(date("Y/m/d G:i:s")." ".self::$_type_auth."  du client -> ".long2ip($this->_ipclient())." n'héxiste pas.  Demande d'identification ! - ".self::$_user_log." -".PHP_EOL, 3, self::$_logfile);
                    }

                }
                
		
		return $retour;
	}
	
	
	
	
	/*
	 * Permet de savoir si une session/cookie a effectivement ete cree
	 */
	private function _session_existe(){
		
		
		switch(self::$_type_auth){
							
			case "session":

				if(!empty($_SESSION['Oscar_i'])){
					
					$sha1loginTest	=	$this->_cryptage(self::$_grain_de_sel.$_SESSION['Oscar_i']['login']);
					
					if(!empty($_SESSION['Oscar_i']) && $_SESSION['Oscar_i']['oscar_log']==$sha1loginTest){
						
						$retour	=	TRUE;
						
					}else{
						
						$retour	=	FALSE;
						
					}
				}else{
					return FALSE;
				}
										
			break;
							
			case "cookie":

				if(!empty($_COOKIE['Oscar_i'])){
					
					$sha1loginTest	=	$this->_cryptage(self::$_grain_de_sel.$_COOKIE['Oscar_i']['login']);
					
					if(!empty($_COOKIE['Oscar_i']) && $_COOKIE['Oscar_i']['oscar_log']==$sha1loginTest){
						
						$retour	=	TRUE;
						
					}else{
						
						$retour	=	FALSE;
						
					}
				}else{
					$retour	=	FALSE;
				}
						
			break;
						
		}
		
		
		return $retour;
		
	}
	
	
	
	/*
	 * Compare les données de la base avec celles du client
	 */
	private function _session_client_is_valide(){
		
		//faux par defaut
		$result	=	FALSE;
		
		$sha1ip			=	$this->_cryptage(self::$_grain_de_sel.$this->_ipclient());
		$sha1navigateur         =	$this->_cryptage(self::$_grain_de_sel.$_SERVER['HTTP_USER_AGENT']);
	
		/*
		 * Récupération des infos en base
		 */
						
            switch(self::$_type_auth){

                case "session":

                    $red = self::$_driver->session_client_is_valide($_SESSION['Oscar_i']['oscar_key'],$_SESSION['Oscar_i']['oscar_log']);

                break;

                case "cookie":

                    $red = self::$_driver->session_client_is_valide($_COOKIE['Oscar_i']['oscar_key'],$_COOKIE['Oscar_i']['oscar_log']);

                break;
            }
				
				
            //si le tableau n'est pas vide
            if(count($red)>0){

                $test	=	TRUE;

                /*
                 * Test de correspondance entre la session
                 * et les données supplémentaires en base
                 * ip
                 * navigateur
                 */
                //var_dump($red);break;
                //si le test de correspondance sur l'ip est activé
                if( self::$_test_ip ){

                    if($red[self::$_driver->get_tbl_ip_user()] != $sha1ip){
                        $test=FALSE;
                        //Log de l'a tentative de la deconnexion
                        if(self::$_active_log){
                            error_log(date("Y/m/d G:i:s")." ".self::$_type_auth."  du client -> ".long2ip($this->_ipclient())." adresse ip invalide - ".self::$_user_log." -".PHP_EOL, 3, self::$_logfile);
                        }
                    }
                }

                //si le test de correspondance sur le navigateur est activé
                if( self::$_test_nav ){
                    if($red[self::$_driver->get_tbl_nav_user()] != $sha1navigateur){
                        $test=FALSE;
                        //Log de l'a tentative de la deconnexion
                        if(self::$_active_log){
                            error_log(date("Y/m/d G:i:s")." ".self::$_type_auth."  du client -> ".long2ip($this->_ipclient())." navigateur invalide - ".self::$_user_log." -".PHP_EOL, 3, self::$_logfile);
                        }
                    }
                }

                //met à jour le resultat
                ($test==TRUE)?$result=TRUE:$result=FALSE;

                /*
                 * Si l'identification est valide ,
                 * on charge les données serializés dans l'objet
                 */
                self::$_private_data	=	unserialize($red[self::$_driver->get_tbl_data()]);

            }
			
			return $result;
		
	}
	
	
	
	
	
	/*
	 * Recoit le login , le mot de passe , le mode de cryptage
	 * et en fonction du resultat , 
	 * il retourne TRUE ou FALSE , 
	 * Et génére en cas de succes , les SESSIONS ou COOKIES ou Autres
	 */
	public function identify($login, $password){
		
		$retour	=	FALSE;

        //Log de la tentative de connexion
        if(self::$_active_log){

            error_log(date("Y/m/d G:i:s")." - Tentative connexion USER : ".$login." IP : ".long2ip($this->_ipclient())." - ".self::$_user_log." -", 3, self::$_logfile);

        }
        
			$red  =   self::$_driver->identify($login,$this->_cryptage($password));	
				
            //test le resultat
            if(count($red) > 0){

                //reusultat trouvé création de la session

                $sha1login	=	$this->_cryptage(self::$_grain_de_sel.$login);
                $sha1oscarKey	=	$this->_cryptage(self::$_grain_de_sel.time());

                $Tinfos	=	array(
                    "loginclr"	=>	$login,
                    "login"		=>	$sha1login,
                    "oscar_key"	=>	$sha1oscarKey,
                    "sql_result"=>	$red
                );



                /*
                 * Récupération des données privées demandées
                 */

                if(!empty(self::$_personal_fields_in_object)){

                    foreach ( self::$_personal_fields_in_object AS $data ){

                        //vérifie que le champ demandé existe
                        if(key_exists($data["field_name"],$Tinfos["sql_result"])){

                            //récupére la valeure à ajouter
                            $recordValue	=	$Tinfos["sql_result"][$data["field_name"]];
                            //si cryptage
                            ($data["crypt"]==TRUE)?$recordValue=$this->_cryptage($recordValue):null;

                            //ajout à l'objet
                            self::$_private_data[$data["data_name"]]	=	$recordValue;
                        }
                    }
                }

                /*
                 * Création de session ou cookie
                 */
                switch( self::$_type_auth ){

                    case "session":

                        //Création de la session
                        $this->_create_session($Tinfos);

                    break;

                    case "cookie":

                        //Création du cookie
                        $this->_create_cookie($Tinfos);

                    break;

                }


                //enregistrement dans la table temporaire
                $this->_create_temp_record($Tinfos);

                $retour	= TRUE;

                                        //Log de la tentative de connexion
                                        if(self::$_active_log){

                                            error_log(" => Succes ! ".PHP_EOL, 3, self::$_logfile);

                                        }

            }else{
                //aucun resultat trouve

                $retour = FALSE;

                                        //Log de la tentative de connexion
                                        if(self::$_active_log){

                                            error_log(" => Echec ! ".PHP_EOL, 3, self::$_logfile);

                                        }
            }
				
			
		
		
		
		//Il faut envoyer les cookies pour les valider
		if( self::$_type_auth == "cookie" ){
		
                       header('HTTP/1.1 200 OK');
                       header("Location: ".$_SERVER['REQUEST_URI']);
                       exit();
    		
		}
		
		
		return $retour;
		
	}
	
	
	/*
	 * Création en base de l'identification 
	 * qui permettera de vérifier 
	 * l'integrité du compte tout ua long de la visite
	 */
	private function _create_temp_record($infos	=	array()){
		
		$sha1login		=	$infos["login"];
		$sha1oscar_key	=	$infos["oscar_key"];
		$sha1ip			=	$this->_cryptage(self::$_grain_de_sel.$this->_ipclient());
		$sha1navigateur	=	$this->_cryptage(self::$_grain_de_sel.$_SERVER['HTTP_USER_AGENT']);
		$t_creation		=	time();
		$t_expiration	=	0;
		$_serialize_private_data	=	serialize(self::$_private_data);
		
		if(!empty($sha1login)){

			$data   =   array(
                'login'          =>  $sha1login,
                'oscar_key'      =>  $sha1oscar_key,
                'ip'             =>  $sha1ip,
                'navigateur'     =>  $sha1navigateur,
                'private_data'   => $_serialize_private_data,
                't_creation'     =>  $t_creation,
                't_expiration'   => $t_expiration
            );
			
			self::$_driver->create_temp_record( $data );

            
		}
		
		return true;
		
	}
	
	
	/*
	 * Création de la session utilisateur
	 */
	private function _create_session($infos	=	array()){
		
		//définition du temps de session
		if(self::$_session_lifeTime > 0 ){
			session_set_cookie_params  ( self::$_session_lifeTime  );
		}
		
		$sha1login		=	$infos["login"];
		$sha1oscar_key	=	$infos["oscar_key"];
				
		$_SESSION['Oscar_i']	=	array(
			"login"		=>	htmlentities($infos["loginclr"],ENT_QUOTES,"UTF-8"),
			"oscar_log"	=>	$sha1login,
			"oscar_key"	=>	$sha1oscar_key
		);
		
		
		//ajout des champs perso dans la session
		if(!empty(self::$_personal_fields_in_session)){
			
			foreach ( self::$_personal_fields_in_session AS $newrecord ){
				
				//vérifie que le champ demandé existe
				if(key_exists($newrecord["field_name"],$infos["sql_result"])){
					
					//récupére la valeure à ajouter
					$recordValue	=	$infos["sql_result"][$newrecord["field_name"]];
					//si cryptage 
					($newrecord["crypt"]==TRUE)?$recordValue=$this->_cryptage($recordValue):null;
					
					//ajout en session
					$_SESSION['Oscar_i'][$newrecord["session_name"]]	=	$recordValue;
					
					unset($recordValue);
				}
				
			}
			
		}
		
		
	}
	
	
	
	/*
	 * Création du cookie utilisateur
	 */
	private function _create_cookie( $infos	=	array() ){
			
		$sha1login		=	$infos["login"];
		$sha1oscar_key	=	$infos["oscar_key"];
		
	
		if(!setcookie("Oscar_i[acceptcookie]","valide",intval(self::$_cookie_lifeTime))){
			
			self::$_type_auth	=	"session";
			//si le navigateur ne prend pas en compte le cookie on cree une session
			$this->_create_session($infos);
			
		}else{
			
			//cookie accepté
			setcookie('Oscar_i[login]',htmlentities($infos["loginclr"],ENT_QUOTES,"UTF-8"),intval(self::$_cookie_lifeTime));
			setcookie('Oscar_i[oscar_log]',$sha1login,intval(self::$_cookie_lifeTime));
			setcookie('Oscar_i[oscar_key]',$sha1oscar_key,intval(self::$_cookie_lifeTime));
			
			//ajout des champs perso dans la session
			if(!empty(self::$_personal_fields_in_session)){
				
				foreach ( self::$_personal_fields_in_session AS $newrecord ){
					
					//vérifie que le champ demandé existe
					if(key_exists($newrecord["field_name"],$infos["sql_result"])){
						
						//récupére la valeure à ajouter
						$recordValue	=	$infos["sql_result"][$newrecord["field_name"]];
						//si cryptage 
						($newrecord["crypt"]==TRUE)?$recordValue=$this->_cryptage($recordValue):null;
						
						//ajout en session
						setcookie('Oscar_i['.$newrecord["session_name"].']',$recordValue,intval(self::$_cookie_lifeTime));
						
						unset($recordValue);
					}
					
				}
				
			}
			
			
		}
		
	}
		
	
	/*
	 * Retourne l'adresse ip du client ..
	 * s'il le peut
	 */
	private function _ipclient(){
		
		$ip = htmlspecialchars($_SERVER['REMOTE_ADDR']);
	    if (strpos($ip, '::') === 0) {
	        $ip = substr($ip, strrpos($ip, ':')+1);
	    }
	    $host = ip2long($ip);

	    return $host;
		
	}
	

	
	
	/*
	 * Méthode de cryptage 
	 * Selon le mode choisis , on crypt avec la méthode souhaité
	 * sha1
	 * md5
	 * crypt ect ...
	 */
	private function _cryptage( $data ){
		
		switch(self::$_myCrypt){
			
			case 'sha1':
				$data_crypte	=	sha1($data);
			break;
			
			case 'md5':
				$data_crypte	=	md5($data);
			break;
			
			case 'crypt_DES_STD':
				define('CRYPT_STD_DES',1);
				$data_crypte	=	crypt($data);
			break;
			
			case 'crypt_EXT_STD':
				define('CRYPT_EXT_DES',1);
				$data_crypte	=	crypt($data);
			break;
			
			case 'crypt_MD5':
				define('CRYPT_MD5',1);
				$data_crypte	=	crypt($data);
			break;
			
			case 'crypt_BLOWFISH':
				define('CRYPT_BLOWFISH',1);
				$data_crypte	=	crypt($data);
			break;
			
			default:
				$data_crypte	=	sha1($data);
			break;
			
		}
		
		return $data_crypte;
		
	}
}
?>