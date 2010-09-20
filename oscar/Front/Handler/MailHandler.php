<?php
require_once 'oscar/front/Handler/ierrorObserver.php';

class MailHandler implements ierrorObserver{


    protected static $_instance;
    private $_to;
    const SUBJECT = 'erreur signalée';

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




    public function __construct($to)
    {
        $this->_to = (string)$to[0];
        if(filter_var($this->_to,FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException('Adresse email non conforme');
        }
    }

    
    /*
     * Envoi les données à l'observeur
     */
    public function update( ierrorObservable $msg ){

        @mail($this->_to, self::SUBJECT, $msg->getError());

    }

    /*
     * Methode de descrition de l'observeur
     */
    public function __toString(){

        return sprintf("%s ", __CLASS__);
        
    }

    
}

?>
