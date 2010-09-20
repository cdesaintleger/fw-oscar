<?php
require_once 'oscar/front/Handler/ierrorObserver.php';

class HtmlHandler implements ierrorObserver{



    
    public static function getInstance($args=null)
    {

        if(self::$_instance == null) {

            self::$_instance = new self($args);
        }
        return self::$_instance;
    }

    /*
     * Envoi les données à l'observeur
     */
    public function update( ierrorObservable $msg ){

        //Code spécifique à l'observeur Mail

    }

    /*
     * Methode de descrition de l'observeur
     */
    public function __toString(){

        return sprintf("%s ", __CLASS__);
        
    }

    
}

?>
