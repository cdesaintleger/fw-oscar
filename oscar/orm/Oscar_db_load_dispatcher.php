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
 * @package     Oscar_db_load_dispatcher
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
 * @package     Oscar_db_load_dispatcher
 * @subpackage
 */
require_once 'Oscar_db_manager.php';


class Oscar_db_load_dispatcher {


    private static $_cfg    =   array();

    public static function set_dsn( $MaOrSl, $ArrayDsn, $user, $password, $options = array() ){

        switch ($MaOrSl){

            case 'master':
                self::$_cfg['master']   =   $ArrayDsn;

                self::$_cfg['master_param'] =   array(
                    "user"      =>  $user,
                    "password"  =>  $password,
                    "options"   =>  $options
                );

            break;

            case 'slave':

                self::$_cfg['slave']   =   $ArrayDsn;

                self::$_cfg['slave_param'] =   array(
                    "user"      =>  $user,
                    "password"  =>  $password,
                    "options"   =>  $options
                );

            break;

        }

    }


    /*
     * Instancie le maitre et l'esclave ,
     * passe les paramétre à oscar_Orm
     */
    public static function start_dispatcher(){

        //nombre d'esclave(s)
        $maxSl  =   count(self::$_cfg['slave']) - 1;

        //nombre de maitre(s)
        $maxMa  =   count(self::$_cfg['master']) - 1;

        //instanciation d'un maitre
        $Master =   new Oscar_db_manager(
                self::$_cfg['master'][mt_rand(0, $maxMa)],
                self::$_cfg['master_param']['user'],
                self::$_cfg['master_param']['password'],
                self::$_cfg['master_param']['options'],
                'master'
                );

        //instanciation d'un esclave
        $Slave =   new Oscar_db_manager(
                self::$_cfg['slave'][mt_rand(0, $maxSl)],
                self::$_cfg['slave_param']['user'],
                self::$_cfg['slave_param']['password'],
                self::$_cfg['slave_param']['options'],
                'slave'
                );

    }


}
?>
