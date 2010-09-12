<?php
/**
 * Oscar Framework http://fw-oscar.fr
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
 * @package     Oscar_couchDB
 * @subpackage
 *
 */


 /**
 * Class Oscar_couchDB.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3.2
 * @package     Oscar_couchDB
 * @subpackage
 */
class Oscar_couchDB{

    private static $_host  =   null;
    private static $_port  =   null;

    private $_body  =   null;
    private $_headers=  null;

    private $_types =   array("GET","PUT","POST","DELETE","COPY");

    private static $_user  =   null;
    private static $_pwd   =   null;

    public static $_instance = null;

    /*
     * Paramétres de replication
     */
    private static $_replica_host_src    =   null;
    private static $_replica_port_src    =   null;
    private static $_replica_ddb_src     =   null;

    private static $_replica_host_dst    =   null;
    private static $_replica_port_dst    =   null;
    private static $_replica_ddb_dst     =   null;

    private static $_autoReplique        =   FALSE;
    private static $_test_replication    =   FALSE;




    /***
     * Constructeur
     */
    private function __construct($host='127.0.0.1',$port=5984){

        self::$_host    =   $host;
        self::$_port    =   $port;

    }


    /*
     * Désactive la fonction magique clone
     */
    private function __clone(){
        
    }



    /*
     * Singleton
     */
    public static function getInstance(){

        if( !(self::$_instance instanceof self) ){

            self::$_instance    =   new self;

            return self::$_instance;

        }else{

            return self::$_instance;
        }

    }





    /*
     * Accesseurs
     */
    public function set_user($value){

        self::$_user    =   $value;

    }

    public function set_pwd($value){

        self::$_pwd =   $value;

    }

    public function set_host($value){

        self::$_host    =   $value;

    }

    public function set_port($value){

        self::$_port    =   $value;

    }



    /*
     * Permet d'activer la réplication automatique
     * à chaque modification
     */
    public function auto_replique($auto =   FALSE){

        if( is_bool($auto)){
            self::$_autoReplique    =   $auto;
        }

    }


    /*
     * Paramétrage a réplication couchDB
     */
    public function set_replication($adr_source,$adr_dest,$port_dest,$database_source=null,$database_dest=null,$checkup = FALSE){

        try{
            if( !empty($adr_dest) && !empty($adr_source) ){

                $err    =   false;

                //permute les adresses pour tester
                $adr_actuelle   =   self::$_host;
                self::$_host    =   $adr_dest;
                //permute les ports
                $port_actuel   =   self::$_port;
                self::$_port    =   $port_dest;

                //si l'on demande des vérifications de paramétres
                if( $checkup ){
                    //récupére les infos
                    $Tinfo  =   $this->send("GET",'/');

                    //test la connexion au serveur distant
                    if( $Tinfo == null ){
                        //on remet l'adresse paramétré et le port
                        self::$_host   =    $adr_actuelle;
                        self::$_port   =    $port_actuel;

                        throw new Exception("Le serveur cible n'est pas accessible ! ", 011);
                        return (-1);
                    }
                }

                
               

                    //on remet l'adresse paramétré et le port
                    self::$_host   =    $adr_actuelle;
                    self::$_port   =    $port_actuel;

                    //test l'existance des base source et destination
                    if( !empty($database_source) && !empty($database_dest) ){

                        //permute les adresses pour tester
                        $adr_actuelle   =   self::$_host;
                        self::$_host    =   $adr_source;

                        //si l'on demande des vérifications de paramétres
                        if( $checkup ){
                            if( !$this->database_exists($database_source) ){

                                //création de la base inexistante
                                if( !$this->add($database_source) ){
                                    throw new Exception("La base source n'héxiste pas ... tentative de création échoué !", 001);
                                    return -1;
                                }

                                //on remet l'adresse paramétré et le port
                                self::$_host   =    $adr_actuelle;
                                self::$_port   =    $port_actuel;

                            }
                        }

                        //on remet l'adresse paramétré et le port
                        self::$_host   =    $adr_actuelle;
                        self::$_port   =    $port_actuel;

                        //permute les adresses pour tester
                        $adr_actuelle   =   self::$_host;
                        self::$_host    =   $adr_dest;
                        //permute les ports
                        $port_actuel   =   self::$_port;
                        self::$_port    =   $port_dest;

                        //si l'on demande des vérifications de paramétres
                        if( $checkup ){
                            if( !$this->database_exists($database_dest) ){

                                //création de la base inexistante
                                if( !$this->add($database_dest) ){
                                    throw new Exception("La base cible n'héxiste pas ... tentative de création échoué !", 001);
                                    return -1;
                                }



                                //on remet l'adresse paramétré et le port
                                self::$_host   =    $adr_actuelle;
                                self::$_port   =    $port_actuel;

                            }
                        }

                        //on remet l'adresse paramétré et le port
                        self::$_host   =    $adr_actuelle;
                        self::$_port   =    $port_actuel;

                        //enregistre les paramétres
                        self::$_replica_host_src    =   $adr_source;
                        self::$_replica_port_src    =   self::$_port;
                        self::$_replica_ddb_src     =   $database_source;

                        self::$_replica_host_dst    =   $adr_dest;
                        self::$_replica_port_dst    =   $port_dest;
                        self::$_replica_ddb_dst     =   $database_dest;

                        //si un checkup a été fait .. on ne le refait pas
                        if( $checkup ){
                            self::$_test_replication    =   TRUE;
                        }


                        return true;

                            

                        
                    }else{

                        //enregistre les paramétres
                        /*
                        self::$_replica_host_src    =   $adr_source;
                        self::$_replica_port_src    =   self::$_port;

                        self::$_replica_host_dst    =   $adr_dest;
                        self::$_replica_port_dst    =   $port_dest;
                         *
                         */

                        throw new Exception("Erreur , paramétres manquants set_replication",001 );

                        return false;
                    }
                
            }
        }catch(Exception $e){

            echo 'CouchDB - Erreur rencontrée : ' . $e->getMessage();
            
        }
    }


    /*
     * Envoi une requête http au serveur couchDB
     * data doit être un tableau associatif 
     */
    function send($type, $url="/", $data=null, $reponse="array"){

        try{

            //il doit y avoir un type valide
            if(in_array($type, $this->_types)){

                $json_data  =   null;

                if(!empty($data)){

                    if(is_array($data)){

                        //encodage des données
                        $json_data =   self::_encode_data($data);

                    }else{
                        if($type != "COPY"){

                            throw new Exception("Les données doivent etre contruites sous forme de tableua associatif ! <br>", 003);

                        }else{
                            $json_data  =   $data;
                        }
                    }

                }

                //envoie de la requete
                $this->requeteur($type, $url, $json_data );

                //reponse
                switch( $reponse ){

                    case "array":

                        $retour =   json_decode($this->_body,true);

                    break;

                    case "json":

                        $retour =   $this->_body;

                    break;

                    default:

                        $retour =   false;

                    break;
                }


            }else{

                throw new Exception("Le type n'est pas valide", 001);

            }


            return $retour;

        }catch(Exception $e){

             echo 'Erreur rencontrée : ' . $e->getMessage();

        }

        
    }



    /*
     * Méthode permettant d'envoiyer des requetes HTTP au serveur couchDB
     */
    private function requeteur($type, $url, $jsondata=null){

        try{
            //Tableau qui vat contenir les entêtes
            $UserAgent = "User-Agent : Mozilla/4.0 (compatible; MSIE 5.0; Windows 95)";

            //gestion de la copie
            $headplus   =   null;
            if($type    ==   "COPY"){
                $cible  =   $jsondata;
                $jsondata   =   null;
                $headplus   =   "Destination: ".$cible."\r\n";
            }


            $fp = fsockopen(self::$_host, self::$_port, $errno, $errstr, 30);
            if (!$fp) {
                throw new Exception("$errstr ($errno)<br />\n",002);
            } else {
                $out = $type." $url HTTP/1.0\r\n";
                $out .= "Host: ".self::$_host."\r\n";
                $out .= $headplus;
                $out .= 'Accept: application/json'."\r\n";

                if(self::$_user || self::$_pwd){
                    $out .= 'Authorization: Basic '.base64_encode(self::$_user.':'.self::$_pwd)."\r\n";
                }

                if($jsondata !=null){
                    $out .= 'Content-Length: '.strlen($jsondata)."\r\n";
                    $out .= 'Content-Type: application/json; charset=UTF-8'."\r\n\r\n";
                    $out .= $jsondata."\r\n";
                } else {
                    $out .= "\r\n";
                }

                /*
                 * Envoi des données
                 */
                fwrite($fp, $out);

                $reponse    =   "";
                $this->_body    =   "";
                $this->_headers =   "";

                /*
                 * Récupération de la réponse
                 */
                while (!feof($fp)) {
                    $reponse .=   fgets($fp);
                }

                /*
                 * Séparation des entetes du corps
                 */
                list($this->_headers, $this->_body) = explode("\r\n\r\n", $reponse);


                fclose($fp);
            }
        }catch(Exception $e){

             echo 'Erreur rencontrée : ' . $e->getMessage();

        }

    }



    /*
     * Encode les données pour les envoyer au format json
     */
    private static function _encode_data($data,$charset="UTF-8"){

        $jason_data =   json_encode($data);

        return $jason_data;

    }


    /*
     * Methode de recupération de document
     */
    public function get($database,$id=null,$rev=null){

        try{
            //Vérification de l'existance de la base
            if( $this->database_exists($database) ){

                //test l'existance de l'id et de la revision
                if($this->data_exists($database,$id,$rev)){

                    if(!empty($rev)){
                        $Trev   =   array("_rev"=>$rev);
                    }else{
                        $Trev   =   null;
                    }

                    $ret    =   $this->send("GET","/".$database."/".$id,$Trev);
                    return $ret;

                }else{
                    return false;
                }
            }else{
                throw new Exception("La base de données n'héxiste pas !", 0004);
            }
        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }
        


    }





    /*
     * Methode de récupération des versions et de leurs etats
     */
    public function all_revs($database,$id){

        try{
            //Vérification de l'existance de la base
            if( $this->database_exists($database) ){

                //test l'existance de l'id et de la revision
                if($this->data_exists($database,$id)){


                    $ret    =   $this->send("GET","/".$database."/".$id."?revs_info=true");

                    $Tret   =   array();
                    $Tret['actuel'] =   $ret["_rev"];
                    $Tret['liste']  =   $ret["_revs_info"];

                    return $Tret;

                }else{
                    return false;
                }
            }else{
                throw new Exception("La base de données n'héxiste pas !", 0004);
            }
        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }

    }






    /*
     * Méthode de creation base / donne
     */
    public function add($database, $id=null, $data=null){

        try{
            //si c'est une creation de base
            if(!empty($database) &&  $id==null && $data==null  ){

                //test l'existance de la base
                if( !$this->database_exists($database) ){
                    
                    $ret    =   $this->send("PUT","/".$database);

                    //replication
                    if( self::$_autoReplique    ==  TRUE ){

                        $this->replique();

                    }

                    return $ret["ok"];
                }else{
                    throw new Exception("La base existe déjà ! ", 006);

                }
            }else{

                //creation d'un enregistrement
                if( $this->database_exists($database) ){

                    if( !empty($id) ){

                        //vérifie que l'i n'est pas déjà utilisé
                        if( !$this->data_exists($database,$id) ){

                            if(is_array($data)){

                                $ret    =   $this->send("PUT","/".$database."/".$id.'/', $data);

                                //replication
                                if( self::$_autoReplique    ==  TRUE ){

                                    $this->replique();

                                }

                                return $ret;

                            }else{
                                throw new Exception("Les données doivent etre contruites sous forme de tableua associatif ! <br>", 003);

                            }

                        }else{
                            throw new Exception("L'id unique existe déjà !", 007);

                        }

                    }

                }else{
                    throw new Exception("La base de données n'héxiste pas !", 0004);

                }

            }
        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }

        

    }





    /*
     * Méthode de modification d'un enregistrement
     */
    public function update($database,$id,$rev,$data){

        try{
            //Vérification de l'existance de la base
            if( $this->database_exists($database) ){

                //test l'existance de l'id et de la revision
                if($this->data_exists($database,$id,$rev)){

                    //alors modification
                    if( !empty($data) && is_array($data)){

                        //insertion de l'id et de la rev
                        $data["_id"]    =   $id;
                        $data["_rev"]   =   $rev;
                        $ret    =   $this->send("PUT","/".$database."/".$id."/",$data);

                        //replication
                        if( self::$_autoReplique    ==  TRUE ){

                            $this->replique();

                        }

                        return $ret;


                    }else{
                        throw new Exception("Les données doivent etre contruites sous forme de tableua associatif ! <br>", 003);

                    }

                }else{
                    throw new Exception("Impossible de modifier un enregistrement non existant ! <br>", 008);

                }

            }else{
                throw new Exception("La base de données n'héxiste pas !", 0004);

            }
        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }


    }






    /*
     * Méthode de copy de document vers une cible donnée ( possibilité de spécifier une revision )
     */
    public function copy($database,$idOrig,$idcible,$revcible=null){

        try{
            if( !empty($database) && !empty($idOrig) && !empty($idcible) ){

                // test l'existance de la base
                if($this->database_exists($database)){

                    //test l'existance du document source
                    if($this->data_exists($database,$idOrig)){

                        // si c'est un remplacement de document existant
                        if($revcible !=null){

                            if($this->data_exists($database,$idOrig,$revcible) ){

                                //copy
                                $ret    =   $this->send("COPY","/".$database."/".$idOrig."/",$idcible."?rev=".$revcible);

                                //replication
                                if( self::$_autoReplique    ==  TRUE ){

                                    $this->replique();

                                }

                                return $ret;
                            }

                        }else{
                            //copy
                            $ret    =   $this->send("COPY","/".$database."/".$idOrig."/",$idcible);

                            //replication
                            if( self::$_autoReplique    ==  TRUE ){

                                $this->replique();

                            }

                            return $ret;
                        }

                    }else{
                        throw new Exception("Impossible de copier un enregistrement non existant ! <br>", 010);

                    }

                }else{
                    throw new Exception("La base de données n'héxiste pas !", 0004);

                }

            }else{

                throw new Exception("Données manquantes pour la copie !", 0009);


            }
        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }

    }












    /*
     * Méthode de suppression d'un enregistrement / ou d'une base
     */
    public function delete($database,$id,$rev){

        try{
            if( !empty($database) ){

                //test l'existance de la base
                if( !$this->database_exists($database) ){
                    throw new Exception("La base de données n'héxiste pas !", 0004);
                }else{

                    if( !empty($id) && !empty($rev) ){

                        //test l'existance des donnée avec la revision specifié
                        if(!$this->data_exists($database,$id,$rev)){
                            throw new Exception("L'enregistrement ou la révision n'hexiste pas !", 0005);
                        }else{
                            $ret    =   null;
                            $ret    =   $this->send("DELETE","/".$database."/".$id."?rev=".$rev);

                            //replication
                            if( self::$_autoReplique    ==  TRUE ){

                                $this->replique();

                            }

                            if( array_key_exists("ok", $ret) ){
                                return $ret['ok'];
                            }else{
                                return false;
                            }
                        }
                    }else{
                        //si c'est une suppression de base
                        if( empty ($id) && empty($rev) ){

                            $ret    =   null;
                            $ret    =   $this->send("DELETE","/".$database.'/');

                            //replication
                            if( self::$_autoReplique    ==  TRUE ){

                                $this->replique();

                            }

                            if( array_key_exists("ok", $ret) ){
                                return $ret['ok'];
                            }else{
                                return false;
                            }

                        }else{

                            return false;
                        }

                    }
                }

            }
        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }

    }


    /*
     * Test l'existance d'une base
     */
    public function database_exists($database){

        $ret    =   $this->send("GET","/".$database);
        if( array_key_exists("error", $ret) ){

            return false;

        }else{
            return true;
        }

    }


    /*
     * Test l'existance d'un enregistrement dans la base ( revision  )
     */
    public function data_exists($database, $id, $rev=null){

        $ret    =   null;

        if($rev !=null){
            $ret    =   $this->send("GET","/".$database."/".$id."?_rev=".$rev);
        }else{
            $ret    =   $this->send("GET","/".$database."/".$id);
        }

        if( array_key_exists("error", $ret) ){
            return false;
        }else{
            return true;
        }

    }



    /*
     * Simple affichage des bases existantes
     */
    public function show_databases(){

        $ret    =   $this->send("GET","/_all_dbs");
        return $ret;

    }


    /*
     * Simple affichage de tables d'une base de données
     */
    public function show_tables($database){

        try{
            if( $this->database_exists($database)){

                $ret    =   $this->send("GET","/".$database."/_all_docs");
                return $ret;

            }else{
                throw new Exception("La base de données n'héxiste pas !", 0004);
            }
        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }

    }


    /*
     * Méthode de compactaged'une base
     */
    public function compact_database($database){

        try{
            if( $this->database_exists($database)){

                $ret    =   $this->send("POST","/".$database."/_compact");

                //replication
                if( self::$_autoReplique    ==  TRUE ){

                    $this->replique();

                }

                return $ret["ok"];

            }else{
                throw new Exception("La base de données n'héxiste pas !", 0004);

            }
        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }

    }



    /*
     * Méthode de réplication
     */
    public function replique($ddt_src=null,$ddb_dest=null){

        try{
            $flag_replication_ok    =   false;

            //test si la réplication est bien configurée
            if( self::$_replica_host_src == null ||
                self::$_replica_port_src == null ||
                self::$_replica_host_dst == null ||
                self::$_replica_port_dst == null
                ){
                    throw new Exception("Vous devez paramétrer votre réplication avant d'utiliser cette méthode !", 0012);
                }else{

                    
                    if( empty($ddt_src) || empty($ddb_dest) ){
                        $ddt_src    =   self::$_replica_ddb_src;
                        $ddb_dest   =   self::$_replica_ddb_dst;
                    }

                    //Si les base n'ont pas été testées
                    if ( self::$_test_replication == FALSE  ){

                        //on test de nouveau les bases
                        if( $this->set_replication(self::$_replica_host_src,self::$_replica_host_dst,self::$_replica_port_dst,$ddt_src,$ddb_dest,TRUE) ){

                            //on peu lancer la réplication
                            $flag_replication_ok    =   true;

                        }else{

                           throw new Exception("La configuration de la réplication n'est pas correct !", 0013);

                        }


                    }else{

                        //on peu lancer la réplication
                        $flag_replication_ok    =   true;

                    }
                }


            //si la configuration est bonne , l'on peut lancer la réplication
            if( $flag_replication_ok == true ){

                $params_replication    =   array(
                    "source"=>"http://".self::$_replica_host_src.":".self::$_replica_port_src."/".self::$_replica_ddb_src,
                    "target"=>"http://".self::$_replica_host_dst.":".self::$_replica_port_dst."/".self::$_replica_ddb_dst);

                    $retour    =   $this->send("POST", $url="/_replicate", $params_replication, "array");

                    return $retour;
            }

        }catch(Exception $e){

         echo 'Erreur rencontrée : ' . $e->getMessage();
         return false;
        }

    }
    



}
?>