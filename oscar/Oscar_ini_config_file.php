<?php
class Oscar_ini_config_file{
	
	
	protected $config	=	array();
	
	
	/*
	 * Constructeur de la classe , qui attend en paramétre , le fichier complet , avec son chemin , 
	 * et une section ( optionnel ) s'il l'on désire extraire qu'une partie de la configuration
	 */
	public function __construct($file	=	null, $section	=	null){
		
		try{
			
			if(!file_exists($file)){
				throw new Exception("Erreur : Le fichier de configuration n'éxiste pas !");
			}elseif (!is_readable($file)){
				throw new Exception("Erreur : Le fichier de configuration ne peut être lu !");
			}else {
				
				//lecture du fichier de conf
				if(!empty($section)){
					
					$config	=	parse_ini_file($file, true);
					$this->config	=	$config[$section];
					
				}else{
					
					$this->config	=	parse_ini_file($file,false);
					
				}
			}
			
		}catch(Exception $e){
			
			print_r($e->getMessage());
			exit(1);
		}
		
	}
	
	
	
	/*
	 * Retourne un element du fichier de configuration , 
	 * si ceului-ci existe sinon , retourne false
	 */
	public function __get( $name ){
		
		if(array_key_exists($name,$this->config)){
			
			return $this->config[$name];
			
		}elseif ( in_array($name, $this->config )){
			
			return $this->config[$name];
			
		}else{
			
			return false;
			
		}
		
		
	}
	
	
	
}
?>