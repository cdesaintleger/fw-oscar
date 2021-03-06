<?php
require_once 'oscar/front/Handler/ierrorObserver.php';

class MailHandler implements ierrorObserver{


    protected static $_instance;
    private $_to;
    private $_subject;
    const SUBJECT = 'erreur signalée - ';

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




    public function __construct($args)
    {
        $this->_to = (string)$args[0];
        $this->_subject = (string)$args[1];
        if(filter_var($this->_to,FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException('Adresse email non conforme');
        }
    }

    
    /*
     * Envoi les données à l'observeur
     */
    public function update( ierrorObservable $msg ){

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $err    =   $msg->getError();

        //Linéarise les erreurs sous forme de chaine
        if(is_array($err) ){

            $err    =   implode("<br/>",$err);

        }


        @mail($this->_to, self::SUBJECT.$this->_subject, $err."<br><br> URL : ".$_SERVER['REQUEST_URI'], $headers);

    }

    /*
     * Methode de descrition de l'observeur
     */
    public function __toString(){

        return sprintf("%s ", __CLASS__);
        
    }

    
}

?>
