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
 * @package     Oscar_Date
 * @subpackage
 *
 */


 /**
 * Class Oscar_Date.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_Date
 * @subpackage
 */
class Oscar_Date{
	
	private $_day	=	null;
	private $_month	=	null;
	private $_year	=	null;
	private $_date_format	=	null;
	private $_showborder	=	null;
	private $_borderValue	=	null;
	private $_lang			=	'FR';
	
	public function __construct($day=null,$month=null,$year=null){
		
		//verifie les donnée ou les initialises
		$this->_check_var($day,"day");
		$this->_check_var($month,"month");
		$this->_check_var($year,"year");
		
		//copie dans l'objet
		$this->_day		=	$day;
		$this->_month	=	$month;
		$this->_year	=	$year;
		
		//formatage de la sortie
		$this->_date_format	=	"d-m-Y";
		
		//Affichage ou non des dates en bordure du mois
		$this->_showborder	=	TRUE;
		$this->_borderValue	=	null;
		
	}
	
	
	/*
	 * Setter & Getter
	 */
	
	
	/*
	 * Définition de la sortie des dates dans le tableau associatif
	 */
	public function set_format( $format	=	"d-m-Y" ){
		
		$this->_date_format	=	$format;
		
	}
	
	/*
	 * Définie la date 
	 */
	public function set_day( $day=null ){
		
		$this->_check_var( $day , "day" );
		$this->_day	=	$day;
		
	}
	
	public function set_month( $month=null ){
		
		$this->_check_var( $month , "month" );
		$this->_month	=	$month;
		
	}
	
	public function set_year( $year=null ){
		
		$this->_check_var( $year , "year" );
		$this->_year	=	$year;
		
	}
	
	/*
	 * Gestion des bordure du mois souhaité
	 */
	public function set_showBorder( $val=TRUE ){
		
		if(is_bool($val)){
			$this->_showborder	=	$val;
		}
		
	}
	
	public function set_borderValue( $value=null ){
		
		$this->_borderValue	=	$value;
		
	}
	
	/*
	 * Défini la langue de l'affichage des mois
	 */
	public function set_langue( $lang	=	'FR' ){
		
		$tablang	=	array(
			'FR',
			'ENG'
		);
		
		if(!empty($lang) && in_array($lang,$tablang)){
			
			$this->_lang	=	$lang;
		}
		
	}
	
	
	
	
	
	
	/*
	 * Retoune le mois complet sous forme de tableau associatif
	 * les clef sont les numéros de semaine
	 * d'autres infos y sont inséré , 
	 * le jour
	 * le mois
	 * l'année
	 * ... à compléter
	 */
	public function get_month(){
		
		//tableau retour
		$TabCalendar	=	array();
		
		//récupére le jour du mois
		$jour_du_mois	=	date("w",mktime(0, 0, 0, $this->_month, 1, $this->_year));
		
		//récupére le numéro de la semaine
		$numeroSemaine	=	date("W",mktime(0, 0, 0, $this->_month, 1, $this->_year));
		//definition du sous tableau
		$TabCalendar[$numeroSemaine]	=	array();
		
		//Si ce n'est pas un lundi , on récupére les jours précedents
		if( $jour_du_mois != 1 ){
			
			//Correction bug 
			($jour_du_mois==0)?$jour_du_mois=7:null;
			
			//permet de savoir quel jour nous sommes
			$position	=	$jour_du_mois;
			//permet de savoir combien de jour nous avons déjà remonté
			$njoursAvt	=	1;
			
			while( $position != 1 ){
				
				$timestamp	=	mktime(0, 0, 0, $this->_month  , 1-$njoursAvt, $this->_year );
				
				if( $this->_showborder	==	FALSE ){
					
					//verifie si nous somme en bordure
					$bordercheck	=	date("m",$timestamp);
				
				}else{
					$bordercheck	=	$this->_month;
				}
				
				//Le mois correspond avec le mois voulu 
				if(	$bordercheck	==	$this->_month){
					
					array_unshift( $TabCalendar[$numeroSemaine], date($this->_date_format,$timestamp) );
				}else{
					
					array_unshift( $TabCalendar[$numeroSemaine], $this->_borderValue );
				}
				
				$njoursAvt++;
				$position--;
			}
			
		}
		
		
		
		//Puis on compléte la semaine
		
		//permet de savoir quel jour nous sommes
		$position	=	$jour_du_mois;
		//permet de savoir combien de jour nous avons déjà avancé ( 0 pour prendre en compte le jourJ )
		$njoursApr	=	0;
		
		while( $position <= 7 ){
			
			$timestamp	=	mktime(0, 0, 0, $this->_month  , 1+$njoursApr, $this->_year );
			
			
			if( $this->_showborder	==	FALSE ){
				
				//verifie si nous somme en bordure
				$bordercheck	=	date("m",$timestamp);
			
			}else{
				$bordercheck	=	$this->_month;
			}
			
			//Le mois correspond avec le mois voulu 
			if(	$bordercheck	==	$this->_month){
				
				array_push( $TabCalendar[$numeroSemaine], date($this->_date_format,$timestamp) );
				
			}else{//Le mois ne correspond pas correspond avec le mois voulu 
				
				array_push( $TabCalendar[$numeroSemaine],$this->_borderValue);
				
			}
			
			$njoursApr++;
			$position++;
		}
		
		
		$flag_end_month	=	FALSE;
		//Maintenant on ajoute les semaines suivantes
		while( $flag_end_month	==	FALSE ){
			
			//Passe à la semaine suivante
			$numeroSemaine++;
			
			//définition du sous tableau 
			$TabCalendar[$numeroSemaine]	=	array();
			
			//nous avons nos 6 lignes on s'arrete là
			if( count($TabCalendar) == 6 ){
				$flag_end_month	=	TRUE;
			}
			
			//initialise la position
			$position	=	1;
			
			while( $position <= 7 ){
							
				$timestamp	=	mktime(0, 0, 0, $this->_month  , 1+$njoursApr, $this->_year );
				
				if( $this->_showborder	==	FALSE ){
					
					//verifie si nous somme en bordure
					$bordercheck	=	date("m",$timestamp);
				
				}else{
					$bordercheck	=	$this->_month;
				}
				
				//Le mois correspond avec le mois voulu 
				if(	$bordercheck	==	$this->_month){
					
					array_push( $TabCalendar[$numeroSemaine], date($this->_date_format,$timestamp) );
					
				}else{
					
					array_push( $TabCalendar[$numeroSemaine],$this->_borderValue);

					//fin du mois
					$flag_end_month	=	TRUE;
				}
			
				$njoursApr++;
				$position++;
				
			}
			
		}
		
		/*
		 * Au finale on fait un beau tableau pour renvoyer
		 * le mois et les infos utiles
		 */
		
		$calendar	=	array(
			"calendar"	=>	$TabCalendar,
			"jour_int"	=>	$this->_day,
			"mois_int"	=>	$this->_month,
			"month_txt"	=>	$this->get_month_textuel($this->_month),
			"annee"		=>	$this->_year
		);
		
		return $calendar;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	 * Vérifie que variable soit bien renseignée
	 * sinon , on donne une valeur par defaut
	 */
	private function _check_var(&$var, $type){
		
		switch($type){
			
			case 'day':
				if( empty($var) || $var > 31 ){
					$var	=	date("d");
				}
			break;
			
			case 'month':
				if( empty($var) || $var > 12 ){
					$var	=	date("n");
				}
			break;
			
			case 'year':
				if( empty($var) ){
					$var	=	date("Y");
				}
			break;
			
			default:
					
			break;
		}
		
	}
	
	
	
	/*
	 * Retourne le mois de façon textuel
	 */
	private function get_month_textuel( $month	= null ){
		
		/*
		 * Tableau Francais
		 */
		$T_month_FR	=	array(
		1	=>	"Janvier",
		2	=>	"Février",
		3	=>	"Mars",
		4	=>	"Avril",
		5	=>	"Mai",
		6	=>	"Juin",
		7	=>	"Juillet",
		8	=>	"Aout",
		9	=>	"Septembre",
		10	=>	"Octobre",
		11	=>	"Novembre",
		12	=>	"Decembre"
		);
		
		if(!empty($month)){
			
			switch ($this->_lang) {
				case 'FR':
					$month_txt	=	$T_month_FR[$month];
				break;
				
				case 'ENG':
					$month_txt	=	date("F",mktime(0, 0, 0, $this->_month, $this->_day, $this->_year));
				break;
				
				default:
					$month_txt	=	$T_month_FR[$month];
				break;
			}
			
			
		}		
		
		return $month_txt;
	}
	
	
	
	
}
?>