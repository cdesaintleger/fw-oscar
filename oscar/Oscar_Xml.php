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
 * @package     Oscar_Xml
 * @subpackage
 *
 */


 /**
 * Class Oscar_Acl.
 *
 *
 *
 * @author      Christophe DE SAINT LEGER <christophe@fw-oscar.fr>
 * @copyright   Copyright (c) 2009, 2009 Christophe DE SAINT LEGER.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     3.1
 * @package     Oscar_Xml
 * @subpackage
 */
class Oscar_Xml{
	
	/*
	 * Instance de DomDocument
	 */
	private $domInstance	=	null;
	
	/*
	 * Directory+fichier xml
	 */
	private $fichierXml	=	null;
	
	/*
	 * Racine du document XML
	 */
	private $_Racine	=	null;
	
	
	/*
	 * Défini l'emplacement du fichier xml
	 */
	public function set_FileXml($dir){
		
		$this->fichierXml	=	$dir;
		
	}
	
	
	
	/*
	 * 
	 */
	public function __construct($version='1.0',$encodage='UTF-8'){
		
		$this->domInstance = new DomDocument($version, $encodage);
		
	}
	
	/*
	 * Retourne le document xml 
	 * sour forme de chaine , 
	 * si domdocument existe
	 */
	public function __toString(){
		
		if($this->domInstance!=null){
			
			return $this->xmlToString();
			
		}else{
			
			return "Oscar_Xml , V1.0 : De saint leger Christophe.";
		}
		
	}
	
	
	/*
	 * Cree un document XML , avec sa racine
	 */
	public function createXml($dir=null,$rootNode='root'){
		
		$fichierXML	=	null;
		
		if($dir!=null){
						
			$fichierXML	=	$dir;
			
			$this->set_FileXml($dir);
			
		}elseif ($this->fichierXml!=null){
			
			$fichierXML	=	$this->fichierXml;
			
		}
		
		if($fichierXML!=null){
			
			$nod	=	$this->domInstance->createElement($rootNode);
			$this->domInstance->appendChild($nod);
			$this->domInstance->save($fichierXML);
			
		}
		
	}
	
	
	/*
	 * Permet de charger un document .xml existant
	 */
	public function loadDocument( $dir=null ){
		
		$fichierXML	=	null;
		
		if($dir!=null){
						
			$fichierXML	=	$dir;
			$this->set_FileXml($dir);
			
		}elseif ($this->fichierXml!=null){
			
			$fichierXML	=	$this->fichierXml;
			
		}
		
		//Le fichier trouvé est il valide 
		if( is_file($fichierXML) && file_exists($fichierXML) ){
				
				$this->domInstance->load($fichierXML);
				
		}else{
                    error_log("Ooups ... the file does not exist !: ".$fichierXML);
                }
	}
	
	
	/*
	 * Permet de charger une chaine type xml dans notre domdocument
	 */
	public function loadString( $chaine=null ){
		
		if( $chaine!=null ){
			
			$this->domInstance->loadXML($chaine);
			
		}
		
	}
	
	
	/*
	 * Enregistre le document dom 
	 * dans le systéme de fichier
	 */
	public function saveFile($dir=null){
		
		$fichierXML	=	null;
		
		if($dir!=null){
						
			$fichierXML	=	$dir;
			$this->set_FileXml($dir);
			
		}elseif ($this->fichierXml!=null){
			
			$fichierXML	=	$this->fichierXml;
			
		}
		
		if($fichierXML!=null){
			
			$ret    =   $this->domInstance->save($fichierXML);
			
		}

                return $ret;
		
	}
	
	
	
	/*
	 * Retourne le documentXml sous forme de chaine de caractéres
	 */
	public function xmlToString(){
		
		$chaineXml	=	null;
		
		$chaineXml	=	$this->domInstance->saveXML();
		
		return $chaineXml;
	}
	
	
	
	/*
	 * Charge la racine
	 */
	private function loadRacine(){
		
		if(!empty($this->domInstance)){
			
			$this->_Racine	=	$this->domInstance->documentElement;
			
		}
		
	}
	
	
	/*
	 * Recherche des elements à partir de la racine , 
	 * et retourne un tableau contenant les valeurs des balises
	 * trouvées
	 */
	public function searchElem( $elemName=null, $attributName=null ){
		
		$Tresults	=	array();
		
		if($elemName!=null && $this->domInstance!=null ){
			
			//récupére la racine du document
			$this->loadRacine();

                    //Si le document n'hexiste pas , la racine ne peut être trouvé
                    if( $this->_Racine != null){
			
			$domNodeList	=	$this->_Racine->getElementsByTagName($elemName);
						
			foreach($domNodeList as $node){
			
				$valNode		=	null;
				$valAttribut	=	null;
				
				//récupére la valeure du noeud
				$valNode	=	$node->nodeValue;
				
				//récupére la valeur de l'attribut s'il est spécifié et s'il existe
				if($attributName!=null && $node->hasAttribute($attributName) ){
					
					$valAttribut	=	$node->getAttribute($attributName);
					
				}
				
				//insertion dans le tableau
    			array_push($Tresults,array("value"=>$valNode,"attribut"=>$valAttribut));
			
			}
                    }
		}
		
		
		return $Tresults;
	}
	
	
	
	/*
	 * Ajoute un noeud au document courant 
	 */
	public function addElem( $nodeName, $nodeValue=null, $nodeParent=null, $attributName=null, $attributValue=null){
		
		if(!empty($this->domInstance)){
			
			if(!empty($nodeName)){
				
				//création du noeud
				$element = $this->domInstance->createElement($nodeName, $this->_cleanValue($nodeValue));
				
				//ajout de l'attribut au noeud
				if(!empty($attributName) && !empty($attributValue)){
					
					 $element->setAttribute($attributName, $this->_cleanValue($attributValue));
					
				}
				
				
				if($nodeParent!=null){
					
					//Recherche du noeud parent
					$nodeParent = $this->domInstance->getElementsByTagName($nodeParent)->item(0);
					
				}else{
					
					$this->loadRacine();
					$nodeParent	=	$this->_Racine;
					
				}
				//Liaison du nouveau noeud à son parent
				if($nodeParent!=null){
					
  					$nodeParent->appendChild($element);
  					
				}
  				
				
			}
			
			
		}
		
		
	}
	
	
	
	/*
	 * Supprime un noeud de son parent
	 * par defaut le parent , est la racine du cocument xml
	 */
	public function delElem( $nodeName=null, $nodeParent=null ){
		
		if( $this->domInstance!=null && $nodeName!=null ){
						
			if($nodeParent!=null){
				
				//Recherche du noeud parent
				$nodeParent = $this->domInstance->getElementsByTagName($nodeParent)->item(0);
				
			}else{
				
				$this->loadRacine();
				$nodeParent	=	$this->_Racine;
				
			}
			
			if($nodeParent!=null){
				
				//recherche le node à supprimer
				$nodeAsuppr = $nodeParent->getElementsByTagName($nodeName)->item(0);
				
				//si le node à supprimé a ete trouvé
				if($nodeAsuppr!=null){
					
					$nodeParent->removeChild($nodeAsuppr);
					
				}
			}
		}
	}
	
	
	/*
	 * Ajout un attribut à un noeud
	 */
	public function addAttribut( $nameAttr, $valAttr=null, $node=null, $nodeParent=null ){
		
		
		if(!empty($nameAttr) && !empty($valAttr)){
			
			if($nodeParent!=null){
				
				//Recherche du noeud parent
				$nodeParent = $this->domInstance->getElementsByTagName($nodeParent)->item(0);
				
			}else{
				
				$this->loadRacine();
				$nodeParent	=	$this->_Racine;
				
			}
			
			//On est dans le node parent
			if($nodeParent!=null){
				
				//on cherche le node à modifier
				$nodeAmodif = $nodeParent->getElementsByTagName($node)->item(0);
				
				if($nodeAmodif!=null){
					
					$nodeAmodif->setAttribute($nameAttr, $this->_cleanValue($valAttr));
					
				}
				
			}
			
			
		}
		
	}
	
	
	/*
	 * modifie un attribut à un noeud (Alias)
	 */
	public function updateAttribut( $nameAttr, $valAttr=null, $node=null, $nodeParent=null ){
		
		$this->addAttribut( $nameAttr, $valAttr, $node, $nodeParent );
		
	}
	
	
	/*
	 * Supprime l'attribut passé en paramétre
	 */
	public function deleteAttribut( $nameAttr, $node=null, $nodeParent=null ){
		
		if(!empty($nameAttr) ){
			
			if($nodeParent!=null){
				
				//Recherche du noeud parent
				$nodeParent = $this->domInstance->getElementsByTagName($nodeParent)->item(0);
				
			}else{
				
				$this->loadRacine();
				$nodeParent	=	$this->_Racine;
				
			}
			
			//On est dans le node parent
			if($nodeParent!=null){
				
				//on cherche le node à modifier
				$nodeAmodif = $nodeParent->getElementsByTagName($node)->item(0);
				
				if($nodeAmodif!=null && $nodeAmodif->hasAttribute($nameAttr) ){
					
					$nodeAmodif->removeAttribute($nameAttr);
					
				}
				
			}
			
			
		}
		
	}
	
	
	
	/*
	 * Modifie la valeur ou les attributs d'un noeud
	 */
	public function updateElem( $nodeName, $nodeValue=null, $nodeParent=null, $attributName=null, $attributValue=null ){
		
		if(!empty($this->domInstance)){
			
			if(!empty($nodeName)){
											
				if($nodeParent!=null){
					
					//Recherche du noeud parent
					$nodeParent = $this->domInstance->getElementsByTagName($nodeParent)->item(0);
					
				}else{
					
					$this->loadRacine();
					$nodeParent	=	$this->_Racine;
					
				}
				
				
				
				
				//Liaison du nouveau noeud à son parent
				if($nodeParent!=null){
					
					//recherche de l'ancien noeud
					$nodeAmodifier = $nodeParent->getElementsByTagName($nodeName)->item(0);
				
					//si le node à modifier a ete trouvé
					if($nodeAmodifier!=null){
						
						//création du nouveau noeud
						$element = $this->domInstance->createElement($nodeName, $this->_cleanValue($nodeValue));
						
						//ajout de l'attribut au noeud
						if(!empty($attributName) && !empty($attributValue)){
							
							 $element->setAttribute($attributName, $this->_cleanValue($attributValue));
							
						}
											
	  					$nodeParent->replaceChild($element, $nodeAmodifier);
						
						
					}
					  					
				}
  				
				
			}
			
			
		}
		
	}
	
	
	/*
	 * Permet de nettoyer / convertir les valeurs passant par cette méthode
	 */
	private function _cleanValue( &$value ){
		
		addslashes(trim($value));
		
		return $value;
		
	}
	
}
?>