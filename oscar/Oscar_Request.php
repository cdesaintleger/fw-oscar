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
 * @package     Oscar_Request
 * @subpackage
 *
 */


 /**
 * Class Oscar_Request.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_Request
 * @subpackage
 */
class Oscar_Request{

    //Adresse du service ex "www.google.fr"
    private $_host = null;
    //chemin à charger ex /dossier/fichier.html
    private $_path = null;

    //timeout en secondes
    private $_timeout = 15;

    //typede requete POST GET HEAD
    private $_typeReq = null;

    //definition du charset utilisé
    private $_charset   =   "utf-8";

    //definition des types gérés
    private $_Ttypes    =   array("POST","GET","HEAD");

    //definition du nvigateur simulé
    private $_userAgent =   "User-Agent : Mozilla/4.0 (compatible; MSIE 5.0; Windows 95)";

    //paramétres à passer
    private $_params    =   null;








    //definition de l'hote
    public function set_host( $host ){

        if(!empty($host)){
            $this->_host =   htmlentities($host, ENT_QUOTES, $this->_charset);
        }
    }


    //definition du chemin
    public function set_path( $path ){

        if(!empty($path)){
            $this->_path =   htmlentities($path, ENT_QUOTES, $this->_charset);
        }
    }


    //definition du timeout
    public function set_timeout( $timeout ){

        if(!empty($timeout) && is_integer($timeout)){
            $this->_timeout =   $timeout;
        }
    }


     //definition du type de la requete à executer
    public function set_type( $type ){

        if(!empty($type) && in_array($type, $this->_Ttypes)){
            $this->_typeReq =   $type;
        }
    }


    //definition du user Agent
    public function set_userAgent( $ua ){

        if(!empty($ua)){
            $this->_userAgent =   htmlentities($ua, ENT_QUOTES, $this->_charset);
        }
    }


    //definition des paramétres à passer dans l'url
    public function set_params( $param ){
        if(!empty($param)){
               $this->_params   =   $params;
        }
    }


    //definition des paramétres à passe sous forme de tableau
    public function set_params_array( array $Tparams ){

        $this->_params  =   null;

        if(!empty($Tparams) && is_array($Tparams)){

            foreach( $Tparams AS $key=>&$value){
                $params[]  =  $key."=".$value;
            }

            $this->_params  =   implode("&", $params);

        }
    }



    /*
     * execution de la requete et retour soit directement à l'ecran , soit dans une variable
     */
    public function execute(&$retour=null){

        //definition des entetes specifiques au type de requete
        switch( $this->_typeReq ){

            case 'HEAD':
                
                $out = "HEAD $this->_path HTTP/1.1\r\n";
                $out .= "Host: $this->_host\r\n";
                $out .= "$this->_userAgent\r\n";
                $out .= "Connection : Close\r\n\r\n";

            break;

            case 'GET':

                $out = "GET $this->_path?$this->_params HTTP/1.1\r\n";
                $out .= "Host: $this->_host\r\n";
                $out .= "Content-type: application/x-www-form-urlencoded\r\n";
                $out .= "$this->_userAgent\r\n";
                $out .= "Connection: close\r\n\r\n";

            break;

            case 'POST':


                $out = "POST $this->_path HTTP/1.1\r\n";
                $out .= "Host: $this->_host\r\n";
                $out .= "Content-type: application/x-www-form-urlencoded\r\n";
                $out .= "Content-length: ".strlen($this->_params)."\r\n";
                $out .= "$this->_userAgent\r\n";
                $out .= "Connection: close\r\n\r\n";
                $out .= $this->_params."\r\n\r\n";

            break;

        }


        //connexion
        $result =   null;

        $fp = fsockopen($this->_host, 80, $errno, $errstr, $this->_timeout);
        if (!$fp) {
            echo "Erreur : $errstr ($errno)<br />\n";

        }else{
            
            //connexion effectué envoie des entetes
            fwrite($fp, $out);

             while (!feof($fp)) {
                $result .=   fgets($fp, 128);
            }
            fclose($fp);

        }

        //sortie
        if($retour == null){
            echo $result;
        }else{
            $retour = $result;
        }

    }

}
?>
