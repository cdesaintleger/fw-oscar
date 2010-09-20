<?php
require_once 'oscar/front/Handler/ierrorObserver.php';

class StdHandler implements ierrorObserver{

    protected static $_instance;

    /*
     * Pattern singleton
     */
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

        echo $msg->getError()."<br>";
        
    }

    /*
     * Methode de descrition de l'observeur
     */
    public function __toString(){

        return sprintf("%s ", __CLASS__);
        
    }

    
}

?>
