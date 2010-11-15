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
 * @package     Smarty_factory
 * @subpackage  Smarty
 *
 */


/*
 * Smarty
 */
require_once 'Smarty/Smarty.class.php';


 /**
 * Class Smarty_factory.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Smarty_factory
 * @subpackage  Smarty
 */
class Smarty_factory extends Smarty {

	 function Smarty_factory() {
		 	
		// Constructeur de la classe.
		// Appelé automatiquement à l'instanciation de la classe.
		
        parent::__construct();

		$this->template_dir = 'smarty/templates/';
		$this->compile_dir = 'smarty/templates_c/';
		$this->config_dir = 'smarty/configs/';
		$this->cache_dir = 'smarty/cache/';

		$this->caching = false;
		
		//active les modifiers par defaut
		$this->modules_modifier();
		
	}
	
	
	
	
	/*
	 * Définie le repertoire ou se situe le template
	 */
	function set_template_dir( $dir ){
		try{
			if(is_dir($dir)){
				//Ajoute le / à la fin s'il n'y est pas !
				if(!preg_match('/\/$/',$dir)){
					$dir	=	$dir."/";
				}
				
				$this->template_dir	=	$dir;
			}else{
				throw new Exception("Erreur : le chemin des template , n'est pas valide ! ");
			}
		}catch (Exception $e){
			print_r($e->getMessage());
		}
	}
	
	
	
	
	/*
	 * Activation ou non du cache de smarty
	 */
	function set_caching( $value ){
		
		if( is_bool($value)){
			$this->caching	=	$value;
		}else{
			$this->caching	=	false;
		}
		
	}
	
	
	
	/*
	 * Active des modifier utiles par defaut
	 */
	private function modules_modifier(){
		
		// Associons la fonction PHP stripslashes a un modificateur Smarty.
		$this->register_modifier('ss', 'stripslashes');

	}

        /*
         * bind les paramétres passé en param vers l'instance smarty
         */
        public function bind_params( array $params ){

            //bind les paramétres de la vue
            if( !empty($params) ){

                foreach( $params AS $key => &$param ){

                    $this->assign($key, $param);

                }

            }else{
                return false;
            }

        }

        /*
         * Permet de définir des options à smarty avant
         * l'interprétation de la vue 
         */
        public function set_options( array $options){

            foreach( $options AS $pname => &$value ){

                switch($pname){
                    case "cache":
                        if ( is_bool( $value) ){
                            $this->caching = false;
                        }
                    break;

                    case "templates_dir":
                        $this->set_template_dir($value);
                    break;
                }

            }

        }

}
?>