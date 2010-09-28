<?php
/*
 * Gestion des services d'oscar
 */
class Oscar_Front_Controller_Params{


    private static $_Tparams    =   array();

    //Zend Registry actif ou non
    private static $_zendRegistry   =   FALSE;


    /*
     * Permet d'utiliser les fonction isset et empty dans les controller
     * avec les attributs des objets Oscar_front_controller
     */
    public function  __isset($name) {
        
        if (isset(self::$_Tparams[$name])) {
            return (false === empty(self::$_Tparams[$name]));
        } else {
            return null;
        }
    }


    public function set_asArray_param( $name , $key , $value, $nosecure = FALSE ){

        try{

            if( !empty( $name ) && !empty( $key )){

                        if($nosecure === TRUE){
                            self::$_Tparams[$name][ htmlentities($key,ENT_QUOTES,"UTF-8") ]	=	settype(htmlentities($value,ENT_QUOTES,"UTF-8"),gettype($value) );
                        }else{

                            if(is_array($value)){
                                array_walk  ( $value  , create_function('&$v,$k','$v=htmlentities($v,ENT_QUOTES,"UTF-8");')    );
                                self::$_Tparams[$name][htmlentities($key,ENT_QUOTES,"UTF-8")]	=	$value;
                            }else{
                                $keysecure  =   htmlentities($key,ENT_QUOTES,"UTF-8");
                                settype($keysecure, gettype($key));

                                self::$_Tparams[htmlentities($name,ENT_QUOTES,"UTF-8")][$keysecure]	=	htmlentities($value,ENT_QUOTES,"UTF-8");
                                settype( self::$_Tparams[htmlentities($name,ENT_QUOTES,"UTF-8")][$keysecure] , gettype($value) );
                            }
                        }

                }elseif(!$silent){
                    throw new Exception('Paramétre manquant $name fonction set_asArray_param !',500);
                }

        }catch(Exception $e){
            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);
        }

    }


   /*
    * Enregistrement d'un paramétre de façon sécurisé
    */
    public function set_param( $name , $value, $nosecure = FALSE , $silent = TRUE ){

        try{
                if( !empty( $name ) || intval($name) == 0 ){

                        if($nosecure === TRUE){
                            self::$_Tparams[$name]	=	$value;
                        }else{
                            
                            if(is_array($value)){
                                
                                array_walk  ( $value  , create_function('&$v,$k','$v=htmlentities($v,ENT_QUOTES,"UTF-8");')    );
                                self::$_Tparams[$name]	=	$value;
                                
                            }else{
                                 self::$_Tparams[htmlentities($name,ENT_QUOTES,"UTF-8")] =	 htmlentities($value,ENT_QUOTES,"UTF-8");
                                 settype( self::$_Tparams[htmlentities($name,ENT_QUOTES,"UTF-8")] ,gettype($value));
                            }
                        }

                        //ZendRegistry actif ?
                        if( self::$_zendRegistry === TRUE ){
                            Zend_Registry::set($name,$value);
                        }

                        
                }elseif(!$silent){
                    throw new Exception('Paramétre manquant $name fonction set_param !',501);
                }
                
            }catch(Exception $e){
                Oscar_Exception::getInstance()
                    ->error($e->getCode(),$e->getMessage(),null,null);
            }
    }



    /*
     * Recupére un paramétre de façon sécurisé
     */
    public function get_param( $name , $silent = TRUE){

        try{
            if( !empty($name) || intval($name) == 0 ){
                if(  $this->isRegistered($name) ){
                        return self::$_Tparams[$name];
                }elseif(!$silent){
                        throw new Exception('Demande d\'acces à une variable qui n\'héxiste pas ! => '.htmlentities($name,ENT_QUOTES,"UTF-8").'<br>',502);
                }	
            }else{
                throw new Exception('Demande d\'acces vide !',503);
            }
        }catch(Exception $e){
            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);
        }

    }



    /*
     * Supprime un paramétre de la mémoire
     */
    public function unset_param( $name ){

        try{
            if(!empty($name)){
                if(  $this->isRegistered($name) ){
                        unset(self::$_Tparams[htmlentities($name,ENT_QUOTES,"UTF-8")]);
                }
            }else{
                throw new Exception('Suppression d\'une data qui n\'existe pas !',504);
            }
        }catch(Exception $e){
            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);
        }
        
    }



    /*
     * Check si le paramétre existe ( vide ou pas )
     */
    public function isRegistered( $name ){

        try{


            if(!empty( $name ) || intval($name) == 0 ){

                $reponse	=	FALSE;

                if(array_key_exists(htmlentities($name,ENT_QUOTES,"UTF-8"), self::$_Tparams)){
                        $reponse	=	TRUE;
                }

            }else{
                throw new Exception('Demande d\'acces vide !',505);
            }

        }catch(Exception $e){
            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);
        }

        return $reponse;

    }



    /*
     * Active le stockage dans le zend Registry
     * afin de rendre comptaible les premiere
     * et derniére versions
     */
    public function enableZendRegistry(){

        self::$_zendRegistry    =   TRUE;

    }

}
?>
