<?php

require_once 'oscar/front/Handler/HtmlHandler.php';
require_once 'oscar/front/Handler/MailHandler.php';
require_once 'oscar/front/Handler/StdHandler.php';
require_once 'oscar/front/Handler/MokHandler.php';
require_once 'oscar/front/Handler/ierrorObservable.php';

/*
 * Gestionaire par defaut des exceptions 
 * Oscar, qui ne fait qu'ajouter des informations
 *
 * Oscar_Exception::getInstance()
            ->attach(Oscar_Exception::factory('File', array('path/to/foo.log')))
 *
 */
class Oscar_Exception extends Exception implements IteratorAggregate, Countable, ierrorObservable {


    private $_error;
    private $_observers = array();
    protected static $_instance;


    public function __construct(){

        $this->_observers = new SplObjectStorage();

    	
    }

    /*
     * Pattern singleton
     */
    public static function getInstance()
    {
        if(self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function resetInstance()
    {
        self::$_instance = null;
        return self::getInstance();
    }


    /*
     * Pattern Factory
     */
    public static function factory($listener, array $args = array())
    {
        $class = $listener."Handler";
        try {
            return $class::getInstance($args) ;
        } catch ( Exception $e) {
             echo $e->getMessage();
        }
    }



    public function error($errno, $errstr, $errfile, $errline)
    {
        if(error_reporting() == 0) {
            return;
        }
        $this->_error = array($errno, $errstr, $errfile, $errline);
        $this->notify();
    }

    /*
     * Recupére l'erreur courante
     */
    public function getError(){

        if (!$this->_error) {
            return false;
        }
        return vsprintf("Error %d: %s", $this->_error);

    }



    /*
     * Permet de lier un observeur
     */
    public function attach( ierrorObserver $obs ){

        $this->_observers->attach($obs);
        return $this;

    }

    /*
     * Retir un observeur
     */
    public function detach( ierrorObserver $obs ){

        $this->_observers->detach($obs);
        return $this;

    }

    /*
     * Envoi les données aux observateurs
     */
    public function notify( ){

        // $this est intercepté par l'itérateur
        foreach ($this as $observer) {
            try{
                $observer->update($this);
            }catch(\Exception $e){
                die($e->getMessage());
            }
        }

    }

    public function getIterator()
    {
        return $this->_observers; //SplObjectStorage est itératif
    }

    public function count()
    {
        return count($this->_observers); //SplObjectStorage est comptable
    }


        
	
}
?>