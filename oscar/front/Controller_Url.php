<?php
require_once 'Oscar_Exception.php';
/*
 *
 *
 * Gestion des chemins URL
 * pour en déduire action et controlleurs
 */


class Oscar_Front_Controller_Url{

    /*
     * Controller par defaut ou celui trouvé dans l'url
     */
    private static $_url_controller = "Index";

    /*
     * Action par defaut , ou celle trouvé dans l'url
     */
    private static $_url_action    =   "defaut";

    /*
     * Permet de traiter les paramétres de l'url de deux façon
     * ASSOC => clef valeur
     * NUM   => valeur uniquement qui retournera donc un tableau indéxé numérique
     */
    private static $_url_style_params   =   "ASSOC";

    
    public function analyse_url($url = null, $controller_params = null ){

        try{

            if( !empty($url)){

                if( $controller_params  !=   null ){

                    //on retire les paramétres en GET ( ils ne sont pas géré pour le moment )
                    $TurlSansGet =   explode("?",rawurldecode($url));
                    $url    =   $TurlSansGet[0];

                    //Debut de l'analyse
                    $T_url	=	array();
                    $T_url	=	explode('/',rawurldecode($url));


                    $nb_elements	=	0;
                    $nb_elements	=	count($T_url);

                    switch($nb_elements){

			case 2:
				if(!empty($T_url[1])){
					self::$_url_controller	=	htmlentities(strip_tags($T_url[1]));
				}

			break;

			case 3:

				if(!empty($T_url[1])){
					self::$_url_controller	=	htmlentities(strip_tags($T_url[1]));
				}

				if(!empty($T_url[2])){
					self::$_url_action		=	htmlentities(strip_tags($T_url[2]));
				}

			break;

			case ($nb_elements>3):

				if(!empty($T_url[1])){
					self::$_url_controller	=	htmlentities(strip_tags($T_url[1]));
				}
				if(!empty($T_url[2])){
					self::$_url_action		=	htmlentities(strip_tags($T_url[2]));
				}

                                //recupére les paramétres sous forme clef/valeur
                                switch ( self::$_url_style_params ){

                                    case 'ASSOC':

                                        for($i=3;$i<=$nb_elements;){

                                                if(!empty($T_url[$i]) ){
                                                        $controller_params->set_param( $T_url[$i] , $T_url[$i+1] );
                                                }
                                                //paramétre suivant
                                                $i	+= 2;

                                        }

                                    break;
                                    
                                    case 'NUM':
                                        
                                        $positionParam  =   1;
                                        $tabAllParams   =   array();
                                        for($i=3;$i<=$nb_elements;){

                                                if(!empty($T_url[$i]) ){
                                                        $controller_params->set_param( $positionParam++ , $T_url[$i] );
                                                        $tabAllParams[] =   $T_url[$i];
                                                }
                                                //paramétre suivant
                                                $i	+= 1;

                                        }
                                        //set_param(0) correspond à un tableau contenant tout les paramétres
                                        $controller_params->set_param( 0 , $tabAllParams );
                                        
                                    break;
                                }

			break;
                    }


                        /*
                         * Gestion des paramétres en $_POST et Get
                         */
                        if(!empty($_POST)){

                                foreach( $_POST AS $key	=>	&$value ){
                                        $controller_params->set_param( "CPOST_".$key , $value );
                                }
                        }

                        if(!empty($_GET)){

                                foreach( $_GET As $key	=>	&$value ){
                                        $controller_params->set_param( "CGET_".$key , $value );
                                }
                        }


                }else{
                    throw new Oscar_Exception("L'acces au controller de paramétres n'est pas valide ! ");
                }

            }else{
                throw new Oscar_Exception("L'url ne peut être vide ! ");
            }


        }catch(Oscar_Exception $e){
            echo $e->getMessage();
        }catch(Excetpion $e){
            echo $e->getMessage();
        }

        
    }





    /*
     * Retourne le controller
     */
    public function get_url_controller(){
        return self::$_url_controller;
    }


    /*
     * Retourne l'action
     */
    public function get_url_action(){
        return self::$_url_action;
    }


    /*
     * Définition du controller par defaut .
     * Ne peut être utilisé qu'une fois au bootstrap
     * utf-8 imposé
     */
    public function set_default_url_controller( $controller = 'Index' ){

        if( self::$_url_controller == "Index" ){

            self::$_url_controller =    htmlentities(strip_tags($controller),ENT_QUOTES,'utf-8');

        }

    }


    /*
     * Definition de l'action par defaut
     * Ne peut être utilisé qu'une fois au bootstrap
     * utf-8 imposé
     */
    public function set_default_url_action( $action = 'defaut' ){

        if( self::$_url_action == "defaut" ){

            self::$_url_action =    htmlentities(strip_tags($action),ENT_QUOTES,'utf-8');

        }
    }



    /*
     * Définition du style de paramétre passé dans l'url
     */
    public function set_url_style_params( $style = 'ASSOC' ){

        $types  =   array('NUM','ASSOC');

        if( in_array($style, $types) ){
            self::$_url_style_params    =   $style;
        }

    }
    
}

?>
