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
 * @package     Oscar_Wab
 * @subpackage
 *
 */


 /**
 * Class Oscar_wab.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_Wab
 * @subpackage
 */
 class Oscar_Wab{

  //instance à une base de données
  private $_pdo =   null;

  /*
   * Nombre de tentatives echoué avant d'être blacklisté
   */
  private $_seuil_avant_blackliste  =   5;


  /*
   * Temps pendant lequel les erreurs sont comptées
   * en minutes
   */
  private $_laps_time   =   10;


  /*
   * Définition de l'instance de la base de données
   */
  public function set_database_instance( $pdo ){
      $this->_pdo   =   $pdo;
  }

  /*
   * Permet de modifier le seuil avant le blacklistage
   */
  public function set_seuil_blacklist( $nb ){

      if( is_int($nb) ){
        $this->_seuil_avant_blackliste  =   $nb;
      }
  }

/*
 * Definit le temps pendant lequel les erreurs sont compté
 */
 public function set_lapstime_minute( $min ){

      if( is_int($nb) ){
        $this->_laps_time  =   $min;
      }
  }

  /*
   * Permet d'ajouter le client courant ,
   * dans la base , comme tentative de sondage
   */
  public function add_fail_request(){

    $blacklisted  =   FALSE;

    //A récupération de quelques informations :
    $tInfos =   array();

    $tInfos[":ip"]   =   $_SERVER['REMOTE_ADDR'];
    $tInfos[":destination"]  =   htmlentities($_SERVER['REQUEST_URI'],ENT_QUOTES,"utf-8");
    $tInfos[":date"] =   date("Y-m-d h:i:s");

    //B insertion en base
    $sql    =   '
    INSERT INTO req_fail (
        ip,
        destination,
        date
    )
    VALUES
    (
        :ip,
        :destination,
        :date
    )';

    if( $statement = $this->_pdo->prepare($sql)){

        if( $statement->execute($tInfos)){

            //C récupération du nombre d'entrées pour cette ip
            $sql2 = '
                SELECT count(ip) AS nbenrg FROM req_fail WHERE ip = :ip
                AND date BETWEEN TIMESTAMPADD(MINUTE,-'.$this->_laps_time.', NOW()) AND NOW()
                GROUP BY ip
            ';

            if($statement2 = $this->_pdo->prepare($sql2)){

                if( $statement2->execute(array(":ip"=>$tInfos[":ip"]) ) ){

                    $reponse    =   $statement2->fetchAll(PDO::FETCH_ASSOC);
                    
                    //D si le seuil est atteind , on blackliste
                    if($reponse[0]['nbenrg'] >= $this->_seuil_avant_blackliste){

                        $blacklisted  =   TRUE;

                        $sql3   =   '
                        INSERT INTO blacklist (
                            ip,
                            date
                        ) VALUES (
                            :ip,
                            :date
                        )
                        ';

                        if( $statement3 = $this->_pdo->prepare($sql3)){

                            if( $statement3->execute(array(":ip"=>$tInfos[":ip"],":date"=>date("Y-m-d h:i:s") ) ) ){
                                //Ip blacklisté ... plus d'enregistrement pour elle ...
                            }

                        }

                    }

                }

            }

        }

    }

    //retourne le drapeau indiquant si l'on est blacklisté ou non
    return $blacklisted;

  }





  /*
   * Test si une adresse ip est blacklisté
   */
  public function is_blacklisted($ip = null ){

      if( $ip == null ){
          $ip = $_SERVER['REMOTE_ADDR'];
      }

      $blacklisted  =   FALSE;

      if( !empty($ip) ){

          $sql  =   '
          SELECT id FROM blacklist
          WHERE ip = :ip LIMIT 1
          ';

          if( $statement = $this->_pdo->prepare($sql)){

                if( $statement->execute(array(":ip"=>$ip) )){

                    $resultat   =   $statement->fetchAll(PDO::FETCH_ASSOC);
                    
                }
          }

          if( count($resultat) > 0 ){

              $blacklisted  =   TRUE;

          }

      }

      return $blacklisted;

  }

 }
?>