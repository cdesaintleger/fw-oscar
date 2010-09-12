<?php
/**
 * Oscar_Cache
 * 
 * Classe de gestion de cache  
 * 
 * @author de saint leger christophe
 * @version 1.0
 */
class Oscar_Cache{
	
	/*
	 * Instance du gestionnaire de cache , qui DOIT être unique
	 */
	protected static $_instance	=	null;
	
	/*
	 * Tableau de registre du cache
	 */
	private $cache_register	=	array();
	
	/*
	 * Répértoire par défaut des fichiers de cache
	 */
	private $cache_directory	=	"";
	
	/*
	 * Temps de validité d'une donnée en cache par defaut 1h
	 */
	private $time_valid	=	3660;
	
	
	/*
	 * Gestion d'erreur
	 */
	private $error	=	FALSE;
	
	/**
	 * @return unknown
	 */
	public function getCache_directory() {
		return $this->cache_directory;
	}
	
	/**
	 * @param unknown_type $cache_directory
	 */
	public function setCache_directory($cache_directory) {
		if(is_dir($cache_directory) && is_writable($cache_directory) ){
			$this->cache_directory = $cache_directory;
		}
	}
	/**
	 * @return unknown
	 */
	public function getTime_valid() {
		return $this->time_valid;
	}
	
	/**
	 * @param unknown_type $time_valid
	 */
	public function setTime_valid($time_valid) {
		if(is_int($time_valid)){
			$this->time_valid = $time_valid;
		}
	}
	
	
	
	
	
	
	
	private function __construct(){
		//Mise en privé le constructeur , pour garder la validite du singleton
	}
	
	/*
	 * Destructeur qui enregistre le registre du cache
	 */
	public function __destruct(){
		
		//Sauvegarde du registre
		$registre_file	=	sha1("Oscar_key_registered");
		
		try {
				$handle	=	fopen($this->cache_directory.$registre_file,"w+");
				
				//réussite de la creation du fichier de cache
				if($handle){
					
					//vérouillage du fichier 
					if(!flock($handle, LOCK_EX)){
						throw new Exception("EC2 : Erreur au vérouillage du fichier de cache");				
					}
					
					
					if(!fwrite($handle, serialize($this->cache_register))){
						throw new Exception("EC4 : Erreur à l'écriture des données ");
					}
					
					
					if(!flock($handle, LOCK_UN)){
						throw new Exception("EC3 : Erreur au dévérouillage du fichier de cache");
					}
					
				}else{
					throw new Exception("EC1 : Erreur à la création du fichier de cache");
				}
			}catch (Exception $e){
				echo $e->getMessage();
				
				//Détection d'erreur
				$this->error	=	true;
				
			}
		
	}
	
	
	
	
	
	
	/*
	 * Singleton du controller frontal
	 */
	public static function getInstance(){

		if(is_null(self::$_instance)){
			self::$_instance	=	new self;
		}
				
		//définition du répertoire par defaut
		$defaut_dir	=	dirname(__FILE__).'/cache_dir/';
		self::$_instance->cache_directory	=	$defaut_dir;
		
		//Ouverture du registre
		self::$_instance->wakup_register();

		
		return self::$_instance;
		
	}
	
	
	
	public function cache_init(){
		
		
		
	}
	
	
	
	/*
	 * Méthode d'ajout de signature sha1 en registre 
	 */
	private function registre_put($key, $dir=null, $delay=0){
		
		//Met la valeur par defaut en cas d'ommision du dir des fichiers en cache
		if($dir==null){
			$dir	=	$this->cache_directory;
		}
		//Met la valeur par defaut si $delay=0
		if($delay==0){
			$delay	=	$this->time_valid+time();
		}
		
		
		//Ajout dans le tableau de signatures de cache
		$this->cache_register[sha1($key)]	=	array();
		$this->cache_register[sha1($key)]['delay']	=	$delay+time();
		$this->cache_register[sha1($key)]['dir']	=	$dir;
		
		
	}
	
	
	
	
	/*
	 * Mise en mémoire du registre de clef en cache
	 */
	public function wakup_register(){
		
		$registre_file	=	sha1("Oscar_key_registered");
		
		if(file_exists($this->cache_directory.$registre_file)){
			
			try {
									
					$this->cache_register	=	unserialize(file_get_contents($this->cache_directory.$registre_file));
									
				}catch (Exception $e){
					echo $e->getMessage();
					
					//Détection d'erreur
					$this->error	=	true;
					
				}
		}
		
	}
	
	
	
	/*
	 * Vérifie sur une clef , est actuellement en cache , 
	 * si oui elle vérifie si elle est valide
	 */
	public function is_cached($key){
		
		if( key_exists(sha1($key),$this->cache_register) ) {
			
			if($this->cache_register[sha1($key)]['delay'] > time() ){
				$answer	=	true;
			}else{
				$answer	=	false;
				//Suppression du fichier de cache
				unlink($this->cache_register[sha1($key)]['dir'][sha1($key)]);
				//destruction de la clef 
				unset($this->cache_register[sha1($key)]);
			}
			
		}else{
			$answer	=	false;
		}
		
		return $answer;
	}
	
	
	
	
	/*
	 * Supprime une valeur du cache ou toutes les valeurs selon 
	 * le paramétre passé 
	 * null => tout le cahce
	 * key	=> uniquement la clef concerné 
	 */
	public function cache_destroy($key=null){
		
		if( !empty($key) ){
			if($this->is_cached($key)){
				//Suppression du fichier de cache
				unlink($this->cache_register[sha1($key)]['dir'].sha1($key));
				//puis de la clef du registre
				unset($this->cache_register[sha1($key)]);
			}
		}else{
			//récupére toutes les clef 
			$old_files	=	array_keys($this->cache_register);
			
			//supprime tous les fichiers de cache
			if(is_array($old_files)){
				foreach ($old_files AS &$value){
					//Suppression du fichier de cache
					unlink($this->cache_register[$value]['dir'].$value);
				}
			}
			
			//Suppression de toutes les clef du cache
			unset($this->cache_register);
			$this->cache_register	=	array();
		}
		
	}
	
	
	
	
	
	/*
	 * Méthode pour ajouter une valeur au cache
	 */
	public function add_cache($key, $value, $time=0, $options=null){
		
		//récupération du répertoire de cache par defaut
		$dir_cache	=	$this->cache_directory;
		
		
		//Mise en cache des données gestion des options
		if( !empty($options) && is_array($options)){
			
			//Repertoire de stockage 
			if(key_exists('CACHE_DIR',$options)){
				
				if(is_writable($options['CACHE_DIR'])){
					$dir_cache	=	$options['CACHE_DIR'];
				}else{
					$dir_cache	=	$this->cache_directory;
				}
			}else{
				$dir_cache	=	$this->cache_directory;
			}
			
			
			
		}//Fin de la gestion des options
		
		
		//Définition du nom de fichier de cache
		$file_cache	=	sha1($key);
		
		
		try {
			$handle	=	@fopen($dir_cache.$file_cache,"w+");
			
			//réussite de la creation du fichier de cache
			if($handle){
				
				//vérouillage du fichier 
				if(!flock($handle, LOCK_EX)){
					throw new Exception("EC2 : Erreur au vérouillage du fichier de cahce");				
				}
				
				
				if(!fwrite($handle, serialize($value))){
					throw new Exception("EC4 : Erreur à l'écriture des données ");
				}
				
				
				if(!flock($handle, LOCK_UN)){
					throw new Exception("EC3 : Erreur au dévérouillage du fichier de cache");
				}
				
			}else{
				throw new Exception("EC1 : Erreur à la création du fichier de cache");
			}
		}catch (Exception $e){
			echo $e->getMessage();
			
			//Détection d'erreur
			$this->error	=	true;
			
		}
		
		
		/*
		 * Si aucune erreur , alors on ajoute la clef au registre
		 */
		if( !$this->error){
			$this->registre_put($key, $dir_cache, $time);
			
			$retour	=	true;
		}else{
			$retour	=	false;
		}
		
		
		return $retour;
	}
	
	
	
	
	
	/*
	 * Méthode pour recupérer une donnée en cache
	 */
	public function get_cache($key, &$data){
		
		//test si effectivement la variable est en cache et est encore valide
		if($this->is_cached($key)){
			
			//nom du fichier
			$name_cached_file	=	sha1($key);
			
			//récupération du répertoire de stockage 
			$directory	=	$this->cache_register[sha1($key)]['dir'];
			
			try{
				
				if(file_exists($directory.$name_cached_file)){
					
					//récupération des data
					$data	=	unserialize(file_get_contents($directory.$name_cached_file));
					
					$retour	=	true;
					
				}else{
					
					throw new Exception("EC6 : Erreur à la récupération des données en cache");
					
				}
				
				
			}catch (Exception $e){
				echo $e->getMessage();
				$retour	=	false;
			}
			
			
		}else{
			$retour	=	false;
		}
		
		return $retour;
		
	}
	
	
	
	
}
?>