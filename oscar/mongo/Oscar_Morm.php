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
 * @category    Oscar MORM
 * @package     Oscar_Morm
 * @subpackage
 *
 */


  /**
 * Class Oscar_Morm.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_Morm
 * @subpackage
 */
class Oscar_Morm {

    private static $_mongo_master;

    private static $_collection;

    private static $_cur_res;
	
	
	public static function setInstance($value ){

        self::$_mongo_master	=	$value;
		
	}
	
	
	
	/*
	 * Retourne l'instance de l'objet MORM
	 */
	public static function getInstance( ){
            
        if(self::$_mongo_master!=null){
                return self::$_mongo_master;
        }else{
                return false;
        }

	}


       /*
     * Retourne la collection courante
     */
    public function getCollection(){

        return self::$_collection;

    }
	
	/*
	 * Constructeur
	 */
	public function __construct(){

        //monte la collection
        if( empty( self::$_collection ) ){
            self::$_collection  =   $this->getInstance()->{$this->_table};
        }
		
	}
	
	
	
	
	
	/*
	 * Assesseurs
	 */
	public function __get( $name ){
		
        switch ($name){

            case 'result':

                return self::$_cur_res;

                break;

            default :

                return null;

                break;

        }
		
	}


	
	
}
?>