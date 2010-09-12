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
 * @package     Oscar_Acl
 * @subpackage
 *
 */


 /**
 * Class Oscar_Acl.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_Acl
 * @subpackage  
 */
class Oscar_Acl{
	
	/**
	 * Attributs de la classe Oscar_Acl
	 *
	 */
	
	//instance unique
	private static $_instance	=	null;
	
	//liste des régles ACL
	private static $_Rules_List	=	array();
	
	//definition de l'utilisateur
	private static $_User	=	array();
	
	//Liste passes droits
	private static $_Allow_List	=	array();
	
	//Liste des non droits
	private static $_Deny_List	=	array();
	
	
	
	/**
	 * Méhodes public 
	 *
	 */
	
	private function __construct(){
		
	}
	
	/*
	 * Crée et/ou récupére l'instance unique
	 * Singleton
	 */
	public static function getInstance(){
		
		 if(is_null(self::$_instance)){
			self::$_instance	=	new self;
		}
				
		return self::$_instance;
		
	}
	
	/*
	 * Ajoute une definition de droit sur un couple controller action
	 */
	public function addRule( $levelreq, $grouplist,  $conrollerName, $actionName=null ){
		
		//Au moins le controller doit être présent
		if(!empty($conrollerName)){
		
			self::$_Rules_List[]	=	array(
				"CN"	=>	$conrollerName,
				"AN"	=>	$actionName,
				"LR"	=>	intval($levelreq),
                                "GL"    =>      $grouplist
			);
			
		}
		
	}
	
	/*
	 * Défini l'utilisateur courant
	 */
	public function defineUser( $level, $userId, $grouplist, $options=array()){
		 
		//L'utilisateur et son level est obligatoire
		if( !empty($userId) ){
			
			self::$_User	=	array(
				"ID"	=>	$userId,
				"LE"	=>	intval($level),
                                "GL"    =>      $grouplist,
				"OP"	=>	$options
			);
			
		}
                
		
	}
	
	/*
	 * Permet de définir un passe droit pour un utilisateur donné 
	 * pour un controller et action donné
	 */
	public function allow( $userId, $controllerName, $actionName=null ){
		
		//L'utilisateur et le controlleur est le minimum obligatoire
		if( !empty($userId) && !empty($controllerName) ){
			
			self::$_Allow_List[]	=	array(
				"ID"	=>	$userId,
				"CN"	=>	$controllerName,
				"AN"	=>	$actionName
			);
			
		}
		
	}
	
	/*
	 * Permet de définir un non droit pour un utilisateur donné 
	 * pour un controller et action donné
	 */
	public function deny( $userId, $controllerName, $actionName=null ){
		
		//L'utilisateur et le controlleur est le minimum obligatoire
		if( !empty($userId) && !empty($controllerName) ){
			
			self::$_Deny_List[]	=	array(
				"ID"	=>	$userId,
				"CN"	=>	$controllerName,
				"AN"	=>	$actionName
			);
			
		}
		
	}
	
	/*
	 * Permet de tester si un utilisateur posséde le droit ou non d'acceder à la ressource
	 */
	public function isAllow( $userId, $controllerName, $actionName=null ){
		
		//par défaut , l'acces est restreint
		$allow	=	FALSE;
		
		//récupére le level du couple
		$levelreq	=	$this->_getLevel( $controllerName, $actionName );
		
		//compare le level requis avec le level utilisateur
		($levelreq<=self::$_User['LE'])?$allow=TRUE:$allow=FALSE;
		
		//vérifie que l'utilisateur ne possede pas un non droit 
		if($allow){
			//par defaut FALSE
			$ruleDeny	=	FALSE;
			
			$ruleDeny = $this->_check_deny( $userId, $controllerName, $actionName );
			
			//si une regle deny correspond , on annule le droit de passage
			($ruleDeny==TRUE)?$allow=FALSE:null;
		}
		
		//vérifie que l'utilisateur ne possede pas un passe droit
		if(!$allow){

                        //récupére les groupes qui sont autorisés
                        $groupAllow =   $this->_getGroups( $controllerName, $actionName );

                        if(array_intersect($groupAllow,self::$_User['GL'])){
                            $allow=TRUE;

                        }else{
			
                            //par defaut FALSE
                            $ruleAllow	=	FALSE;

                            $ruleAllow = $this->_check_allow( $userId, $controllerName, $actionName );

                            //si une regle allow correspond , on donne le droit de passage
                            ($ruleAllow==TRUE)?$allow=TRUE:null;
                        }
		}
		
		return $allow;
	}
	
	
	
	
	
	
	/**
	 * Méthode privées
	 */
	
	
	
	
	
	/**
	 * Retourne le niveau attribué 
	 *
	 * @param unknown_type $controllerName
	 * @param unknown_type $actionName
	 * @return unknown
	 */
	private function _getLevel( $controllerName, $actionName=null ){
		
		//définition du level à -1
		$level	=	-1;
		
		//Test dans les rules générales
		//array=array_intersect_assoc(array1, array2)
		$Ressource	=	array(
			"CN"	=>	$controllerName,
			"AN"	=>	$actionName
		);
		
		$Tintersec	=	array();
		
		if(!empty(self::$_Rules_List)){
			
			foreach(self::$_Rules_List AS &$Rule){
				
				$intersec	=	array_intersect_assoc($Rule, $Ressource);
				
				// si le couple est trouvé , on quitte la boucle
				if(count($intersec) == 2){
					
					$level	=	$Rule['LR'];
					
					//quite la boulce
					break 1;
				}
				
				//Ajoute aux résultats l'instersection trouvé
				if(!empty($intersec)){
					
					//récupére la valeur du level uniquement si seul controllerName est defini
					if(empty($Rule["AN"])){
						
						$Tintersec[]	=	$Rule['LR'];
						
					}
					
				}
			}
		}
		
		//si le level vaut encore -1 , le coupe n'a donc pas été trouvé , on cherche alors le controller seul
		if( $level<0 && !empty($Tintersec) ){
			
			//récupére la premiere intersection trouvé 			
			$level	=	$Tintersec[0];
			
		}
		
		
		//si level < 0 arrivé ici , c'est qu'il n'existe pas de regle pour ce controller
		if($level<0){
			$level	=	0;
		}
		
		return $level;
		
	}



        /*
         * Retourne les groups autorisé à se connecter
         */
        private function _getGroups( $controllerName, $actionName=null ){

            //définition du level à -1
		$group	=	array();

		//Test dans les rules générales
		//array=array_intersect_assoc(array1, array2)
		$Ressource	=	array(
			"CN"	=>	$controllerName,
			"AN"	=>	$actionName
		);

		$Tintersec	=	array();

		if(!empty(self::$_Rules_List)){

			foreach(self::$_Rules_List AS &$Rule){

				$intersec	=	array_intersect_assoc($Rule, $Ressource);

				// si le couple est trouvé , on quitte la boucle
				if(count($intersec) == 2){

					$group	=	$Rule['GL'];

					//quite la boulce
					break 1;
				}

				//Ajoute aux résultats l'instersection trouvé
				if(!empty($intersec)){

					//récupére la valeur du level uniquement si seul controllerName est defini
					if(empty($Rule["AN"])){

						$Tintersec[]	=	$Rule['GL'];

					}

				}
			}
		}

		//si le level vaut encore -1 , le coupe n'a donc pas été trouvé , on cherche alors le controller seul
		if( count($group)==0 && !empty($Tintersec) ){

			//récupére la premiere intersection trouvé
			$group	=	$Tintersec[0];

		}

		return $group;

        }
	
	
	
	
	/*
	 * Test si une régle deny correspond à la ressource
	 * et utilisateur courant
	 */
	private function _check_deny( $userId, $controllerName, $actionName ){
		
		//par defaut , pas de deny
		$Flag_Deny	=	FALSE;
		
		$ressource	=	array(
			"ID"	=>	$userId,
			"CN"	=>	$controllerName,
			"AN"	=>	$actionName
		);
		
		if(!empty( self::$_Deny_List )){
			
			foreach( self::$_Deny_List AS &$Deny ){
				
				//Si la ressource actuelle correspond à 100% avec un deny , on change le flag
				$intersec	=	array();
				$intersec	=	array_intersect_assoc( $ressource , $Deny );
				
				$nb_intersections	=	count($intersec);
				
				if( $nb_intersections == 3 || ( $nb_intersections == 2 && $Deny['AN']==NULL)){
					
					$Flag_Deny	=	TRUE;
					
					//quitte la boucle
					break 1;
					
				}
				
			}
			
		}
		
		return $Flag_Deny;
		
	}
	
	/*
	 * Test si une régle allow correspond à la ressource
	 * et utilisateur courant
	 */
	private function _check_allow( $userId, $controllerName, $actionName ){
		
		//par defaut , pas de deny
		$Flag_Allow	=	FALSE;
		
		$ressource	=	array(
			"ID"	=>	$userId,
			"CN"	=>	$controllerName,
			"AN"	=>	$actionName
		);
		
		if(!empty( self::$_Allow_List )){
			
			foreach( self::$_Allow_List AS &$Allow ){
				
				//Si la ressource actuelle correspond à 100% avec un deny , on change le flag
				$intersec	=	array();
				$intersec	=	array_intersect_assoc( $ressource , $Allow );
				
				$nb_intersections	=	count($intersec);
				
				if( $nb_intersections == 3 || ( $nb_intersections == 2 && $Allow['AN']==NULL)){
					
					$Flag_Allow	=	TRUE;
					
					//quitte la boucle
					break 1;
					
				}
				
			}
			
		}
		
		return $Flag_Allow;
		
	}
	
}
?>