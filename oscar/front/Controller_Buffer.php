<?php
require_once 'Oscar_Exception.php';

/*
 * Gestion des instanciations et executions de controller/action
 */
class Oscar_Front_Controller_Buffer{


    /*
     * Tableau contenant les data à garder en memoire
     * avant affichage ou suppression
     */
    private $_tabMemory =   array();


    public function start(){
        ob_start();
    }

    public function stop(){
        ob_end_clean();
    }

    public function clean(){
        ob_clean();
    }

    public function flush(){
        ob_flush();
    }

    public function get(){
        return ob_get_contents();
    }



    /*
     * Enregistrmeent en memoire des affichage , et retour de fonctions
     *
     * Disponible ensuite pour un afficchage ultérieur
     */
    public function putInMemory( $data , $destination = null ){

        try{

            if( $destination != null ){
                //Enregistrement des affichage , plus retour de fonction 
                $this->_tabMemory[$destination] =   $data;
                $this->_tabMemory[$destination] .=  ob_get_contents();

            }else{
                
                throw new Oscar_Exception(" Destination du buffer inconnue , perte de données ");
            }

        }catch(Oscar_Exception $e){
            echo $e->getMessage();
        }catch(Exception $e){
            echo $e->getMessage();
        }
        
    }



    /*
     * Récupére des valeures presentes dans la mémoire
     * du buffer pour les traiter
     */
    public function getInMemory( $emplacement = null ){

        try{

            if(!empty($emplacement) ){
                if( array_key_exists($emplacement, $this->_tabMemory) ){

                    return $this->_tabMemory[$emplacement];
                }else{
                    return null;
                }


            }else{
                throw new Oscar_Exception(" Emplacement de la memoire à afficher inconnue ! ");
            }

        }catch(Oscar_Exception $e){
            $e->getMessage();
        }catch(Exception $e){
            $e->getMessage();
        }

    }

}
?>
