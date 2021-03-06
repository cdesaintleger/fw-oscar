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
 * @package     Oscar_db_manager
 * @subpackage  Oscar_Orm
 *
 */


/*
 * Oscar_Orm librarie
 */
require_once 'Oscar_Morm.php';


 /**
 * Class Oscar_db_manager.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_db_manager
 * @subpackage  PDO
 */
class Oscar_dbm_manager extends Mongo {
	
	/* @param array $dsn array('host'=>..., 'port'=>..., 'database'=>... )
     * @param string $username
     * @param string $password
     * @param array $driver_options array( "persist" => "identifier" )
     */
    final public function __construct($dsn, $username = '', $password = '', $driver_options = array() )
    {

        //si une authentification existe
        $auth   =   "";
        if( !empty($username) && !empty($password) ){
            $auth   =   $username.":".$password."@";
        }

        //Création de la chaine de connexion
        if(is_array($dsn) ){

            foreach($dsn as &$con){
            
                $lst_serv[]   =   $auth.$con['host'].":".$con['port']."/".$con['database'];

            }

        }
        
        
        //connexion ( option persistante ou non )
        parent::__construct("mongodb://".implode(",",$lst_serv),$driver_options);

        //Retourne l'acces à la base
        Oscar_Morm::setDbInstance( $this->$dsn[0]['database'] );

    }
}
?>