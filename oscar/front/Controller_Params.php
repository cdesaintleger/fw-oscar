<?php
require_once 'Oscar_Exception.php';

/*
 * Gestion des services d'oscar
 */
class Oscar_Front_Controller_Params{


    private static $_Tparams    =   array();

    //Zend Registry actif ou non
    private static $_zendRegistry   =   FALSE;


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
                    throw new Oscar_Exception('Erreur: paramétre manquant $name fonction set_asArray_param !');
                }

        }catch (Oscar_Exception $e){
            echo $e->getMessage();
        }catch(Exception $e){
            echo $e->getMessage();
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
                    throw new Oscar_Exception('Erreur: paramétre manquant $name fonction set_param !');
                }
                
            }catch (Oscar_Exception $e){
                echo $e->getMessage();
            }catch(Exception $e){
                echo $e->getMessage();
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
                        throw new Exception('Erreur: demande d\'acces à une variable qui n\'héxiste pas ! => '.htmlentities($name,ENT_QUOTES,"UTF-8").'<br>');
                }	
            }else{
                throw new Exception('demande d\'acces vide !');
            }
        }catch(Oscar_Exception$e){
            echo $e->getMessage();
        }catch(Exception $e){
            echo $e->getMessage();
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
                throw new Exception('Suppression d\'une data qui n\'existe pas !');
            }
        }catch(Oscar_Exception$e){
            echo $e->getMessage();
        }catch(Exception $e){
            echo $e->getMessage();
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
                throw new Exception('demande d\'acces vide !');
            }

        }catch(Oscar_Exception $e){
            echo $e->getMessage();
        }catch(Exception $e){
            echo $e->getMessage();
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
