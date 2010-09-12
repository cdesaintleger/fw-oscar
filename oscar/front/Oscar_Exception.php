<?php
/*
 * Gestionaire par defaut des exceptions 
 * Oscar, qui ne fait qu'ajouter des informations 
 */
class Oscar_Exception extends Exception {
	
	public function __construct($message, $quietmode=FALSE, $code=NULL){
		
		//Envoie du message au parent Exception
		parent::__construct($message,$code);
		
	}
	
	
	public function historique(){
		
		/// a plus tard .. ;-)
		
	}
	
}
?>