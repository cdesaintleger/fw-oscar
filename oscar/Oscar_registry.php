<?php
/*
 * Motif de conception qui sert de support de 
 * stockage d'objets ou de variables , en vue de les partager 
 * entres tous les objets
 */
class Oscar_Registry{
	
	private $_store	=	array();
	private static $_instance;
	
	private function __construct(){ }
	
	public static function getInstance(){
		
		if(is_null(self::$_instance)) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function __set($label, $object){
		
		if(!isset($this->_store[$label])){
			$this->_store[$label]	=	$object;
		}
	}
	
	
	public function __unset($label){
		
		if(!isset($this->_store[$label])){
			unset($this->_store[$label]);
		}
	}
	
	public function &__get($label){
		
	if(!isset($this->_store[$label])){
			return $this->_store[$label];
		}else{
			return false;
		}
	}
	
	public function __isset($label){
		
		return isset($this->_store[$label]);
	}
	
}
?>