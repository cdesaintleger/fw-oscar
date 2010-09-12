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
 * @category    Oscar ORM
 * @package     Oscar_Orm
 * @subpackage
 *
 */


  /**
 * Class Oscar_Orm.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_Orm
 * @subpackage
 */
class Oscar_Orm {
	

	private $fields;
	
	
	
	//private static $_pdo;

        //Instances maître et esclave
        private static $_pdo_slave;
        private static $_pdo_master;
	
	
	public static function setPDOInstance($value , $pool = null){

            switch ( $pool ){

                case 'master':
                    self::$_pdo_master	=	$value;
                break;

                case 'slave':
                    self::$_pdo_slave	=	$value;
                break;

                default:
                    self::$_pdo_master	=	$value;
                break;
                
            }
		
	}
	
	
	
	/*
	 * Retourne l'instance de l'objet ORM
	 */
	public static function getPDOInstance( $pool = null){


                switch ( $pool ){

                    case 'master':

                        if(self::$_pdo_master!=null){
                                return self::$_pdo_master;
                        }else{
                                return false;
                        }

                    break;

                    case 'slave':

                        if(self::$_pdo_slave!=null){
                                return self::$_pdo_slave;
                        }else{
                                return false;
                        }

                    break;

                    default:

                        if(self::$_pdo_master!=null){
                                return self::$_pdo_master;
                        }else{
                                return false;
                        }
                        
                    break;

            }
		
	}
	
	
	/*
	 * Constructeur
	 */
	public function __construct($validkey=null){
		
		/*
		 * Mise en UTF-8
		 */
		//$sql ='SET NAMES utf8';
                //self::$_pdo->exec($sql);
		
		//Créé le tableau des champs valeurs
		$this->fields	=	array();
		
		//Si une valeur est passé en paramétre , on charge l'enregistremnet
		if($validkey!=null){
			//Chargement 
			$this->getRecord($validkey);
		}
		
	}
	
	
	
	/*
	 * Insert une une valeur à un champ
	 */
	public function set_field($name, $value){
		
		$this->fields[$name]	=	$value;
		
	}
	
	/*
	 * Retourne la valeure d'un champ
	 */
	public function get_field($name){
		
		if( is_array($this->fields) ){
			
			if( array_key_exists($name, $this->fields)){
				
				return stripslashes($this->fields[$name]);
				
			}
			
		}
		
	}
	
	
	/*
	 * Retourne tous les champs de l'enregistrement courant
	 */
	public function get_fields(){
		
		if(!empty($this->fields)){
			
			return $this->fields;
			
		}
		
	}
	
	
	
	/*
	 * Assesseurs
	 */
	public function __get( $name ){
		
		if(key_exists($name,$this->fields)){
			
			return $this->get_field($name);
			
		}else{
			
			return $this->$name;
			
		}
		
	}
	
	
	public function __set( $name, $value ){
		
		if($name!="fields"){
			$this->set_field($name, $value);
		}else{
			$this->$name	=	$value;
		}
		
	}
	
	
	
	
	
	
	/*
	 * Initialise les champs 
	 */
	public function cleanRecord(){
		
		unset($this->fields);
		$this->fields	=	array();
		
	}
	
	
	/*
	 * Retourne la liste des champs d'un table donnée
	 */
	private function liste_champs($table, $instance = 'master'){

            $_pdo	=	self::getPDOInstance($instance);

            $sql	=	"SHOW TABLES";

            $rq	=	$_pdo->prepare($sql);
            $rq->execute();
            $instance	=	$rq->fetchAll();

            return $instance;
	}
	
	
	/*
	 * Création d'un nouvel enregistrement suivant un model ou un objet
	 */
	public function createRecord($data=null, $instance = 'master'){

            $_pdo	=	self::getPDOInstance($instance);
		/*
		 * Si le tableau est là , et qu'il est effectivement un tableau
		 */
			
			$sql		=	"";
			$chaineCol	=	array();
			$chaineVal	=	array();
			$retour		=	FALSE;
			$statement	=	null;
			
			//Récupéres les colonnes et les valeurs à insérer 
			if($data==null){
				
				//Supprime la clef primaire pour éviter les doublons eventuels
				if(array_key_exists($this->_pkey,$this->fields)){
					unset($this->fields[$this->_pkey]);
				}
				
				//utilisation des attributs de l'objet
				$chaineCol	=	array_keys($this->fields);
				$chaineVal	=	array_values($this->fields);
				
			}else{
				
				$chaineCol	=	array_keys($data);
				$chaineVal	=	array_values($data);
				
				//on charge le tableau fourni dans l'objet
				$this->fields	=	$data;
				
			}
			
			
			
			
			
			//Création de la requete préparé 
			$sql	=	'INSERT INTO 
				'.$this->_table. '
					( '.implode(', ',$chaineCol).' ) 
				VALUES ( 
				'.substr(str_repeat(' ?,',(count($chaineCol))),0 ,-1).' ) ';
			
			//Prépare la requete
			if($statement = $_pdo->prepare($sql)){
				//Execute la requete
				if($statement->execute($chaineVal)){
					
					//Success de l'operation retourne l'id
					$retour	=	$_pdo->lastInsertId();
					
					//enregistrement de l'id dans l'objet
					$this->set_field($this->_pkey, $retour);
					
				}else{
					$retour	=	FALSE;
				}
			}
		
		
		return $retour;		
		
	}
	
	
	
	
	
	
	/*
	 * Maj de l'enregistrement courant
	 * maj suivant $data ou l'objet 
	 */
	public function updateRecord($data=null,$instance = 'master'){

            $_pdo	=	self::getPDOInstance($instance);

		//si le tableau est vide , on passe pas l'objet
		if($data==null){
			
			//utilisation des attributs de l'objet
			$chaineCol	=	array_keys($this->fields);
			$chaineVal	=	array_values($this->fields);
			
		}else{
			
			if(!empty($this->fields[$this->_pkey])){
				
				//on conserve la clef actuel
				$_pkey_orig	=	$this->fields[$this->_pkey];
				
			}elseif(!empty($data[$this->_pkey])){
				
				//sinon on prend celle fourni dans le tableau
				$_pkey_orig	=	$data[$this->_pkey];
				
			}
						
			$chaineCol	=	array_keys($data);
			$chaineVal	=	array_values($data);
			
			//on charge le tableau fourni dans l'objet
			$this->fields	=	$data;
			
			//on remet la clef pour modifier le bon enregistrement
			$this->fields[$this->_pkey]	=	$_pkey_orig;
				
		}
			
		//Actif uniquement si l'on attaque bien un enregistrement existant
		if(!empty($this->fields[$this->_pkey])){
			
			
			//Ajout de la derniére valeur qui est l'id
			$chaineVal[]	=	$this->fields[$this->_pkey];
			
			
			$sql	=	'UPDATE '.$this->_table.'
				SET  ';
				$sql.= implode(' = ? ,',$chaineCol).' = ? ';
				$sql.=	' 
				WHERE '.$this->_pkey.' = ? LIMIT 1';
			
			//Prépare la requete
			if($statement = $_pdo->prepare($sql)){
				//Execute la requete
				if($statement->execute($chaineVal)){
					$retour	=	TRUE;					
				}else{
					$retour	=	FALSE;
				}
			}
				
		}else{
                    return FALSE;
                }
		
	}
	
	

	/*
	 * Retourne un tableau ou initialise 
	 * l'objet avec les valeure trouvés 
	 */
	public function getRecord($valPkey=null, $instance = 'master'){

            $_pdo	=	self::getPDOInstance($instance);

		//Il nous faut une clef
		if(!empty($valPkey)){
			
			$sql	=	'SELECT * FROM '.$this->_table.' 
				WHERE '.$this->_pkey.' = '.$_pdo->quote($valPkey).'
				 LIMIT 1';
			
			if($statement = $_pdo->prepare($sql)){
				
				//Execute la requete
				if($statement->execute()){
					//récupére le resultat
					$red = $statement->fetchAll(PDO::FETCH_ASSOC);
					
					//Si le tableau n'est pas vide ,on initialise l'objet
					if(!empty($red[0])){
						
						$this->cleanRecord();
						//copie des resultats
						$this->fields	=	$red[0];
					}
					
				}
				
			}
			
		}
		
		return $red[0];
		
		unset($red);
	}
	
	
	
	
	/*
	 * Methode de suppression d'enregistrement
	 */
	public function deleteRecord($valPkey=null, $instance = 'master'){

            $_pdo	=	self::getPDOInstance($instance);
		
		$retour	=	FALSE;
		$statement	=	null;
		$sql	=	null;
		$Tcond	=	null;
		
		//So pas de clef donnée , on supprime l'object courant
		if(empty($valPkey)){
			
			if(!empty($this->fields[$this->_pkey])){
				//Utilise l'objet courant
				$Tcond	=	$this->fields[$this->_pkey];
				
				//Vide l'objet
				$this->cleanRecord();
			}
			
		}else{
			//Utilise la clef passé en paramétre
			$Tcond	=	$valPkey;
		}

		//Verifie qu'une condition existe bien
		if(!empty($Tcond)){
		
			//création de la requete
			$sql	=	'DELETE FROM '.$this->_table.' 
			 WHERE '.$this->_pkey.' = :pvalkey LIMIT 1';
			
			if($statement = $_pdo->prepare($sql)){
				
					$statement->bindParam(':pvalkey', $Tcond);
										
					//Execute la requete
					if($statement->execute()){
						$retour	=	TRUE;
					}
			}
		
		}
		 
		return $retour;
		
	}
	
	

        public function getAllRecord( $filters = array( ), $resultType = OSCAR_ORM_RES_ASSOC, $instance = 'master' ){

            $_pdo = self::getPDOInstance( $instance );

            $sql = 'SELECT * FROM '.$this->_table;
            if( is_array( $filters )&&count( $filters )>0 ){

                 if( isset( $filters[ "where" ] ) )
                     $sql .= " WHERE ".$filters[ "where" ];

                 if( isset( $filters[ "groupby" ] ) )
                     $sql .= " GROUP BY ".$filters[ "groupby" ];

                 if( isset( $filters[ "having" ] ) )
                     $sql .= " HAVING ".$filters[ "having" ];

                 if( isset( $filters[ "orderby" ] ) )
                     $sql .= " ORDER BY ".$filters[ "orderby" ];

                 if( isset( $filters[ "limit" ] ) )
                     $sql .= " LIMIT ".$filters[ "limit" ];
             }

             $red = array( );

             if( $statement = $_pdo->prepare( $sql ) ){

                 //Execute la requete
                 if( $statement->execute() ){

                    //récupére le resultat
                    $red = $statement->fetchAll( $resultType );
                 }//execute
             }//statement
             //retourne les resultats
             return $red;
         }





        /*
         * Execution simple d'une requete ,
         * retourne un resultat sous forme de tableau associatif
         */
        public function execute($sql,$data , $instance = 'master'){

            $_pdo	=	self::getPDOInstance($instance);

            if( !empty($sql) ){

            if($statement = $_pdo->prepare($sql)){

                        if( !empty($data) ){

                            if( is_array( $data )){

                                $data_tested    =   $data;

                            }else{

                                return false;

                            }

                        }else{

                            $data_tested    =   null;

                        }

			//Execute la requete
			if($statement->execute($data_tested)){
                            //retourne les resultats uniquement pour les resqutes de types select
                            if( preg_match("/^SELECT/i",trim($sql)) ){
				//récupére le resultat
				$red = $statement->fetchAll(PDO::FETCH_ASSOC);
                            }else{
                                $red    =   null;
                            }

			}//execute

		}//statement

		//retourne les resultats
		return $red;

            }else{
                return false;
            }

        }
	
	
	
	
}
?>