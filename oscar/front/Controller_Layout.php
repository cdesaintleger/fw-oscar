<?php
require_once 'Smarty/Smarty.class.php';
require_once 'oscar/Smarty_Factory.php';
/*
 *
 *
 * Gestion des vues
 */


class Oscar_Front_Controller_Layout{


    /*
     * Template à utiliser pour la vue
     * principale
     */
    private static $template   =   null;


    /*
     * Liaison nom variable aux vues
     */
    private static $bindSmarty  =   array();


    /*
     * Instance de smarty
     */
     private static $_smarty_instance   =   null;



    /*
     * singleton de smarty du controller Frontal
     */
    public static function SmartyGetInstance(){

            if(is_null(self::$_smarty_instance)){
                    self::$_smarty_instance	=	new Smarty_factory();
            }
            return self::$_smarty_instance;

    }


    /*
     * Définition et paramétrge
     * de la vue via smarty
     */
    public function set_smarty_layout( $controller_proc , $params ){

        try{

            if(!empty( $controller_proc)){

                if(is_array($params) && !empty($params)){

                        //récupére une instance de smarty
                        $smarty	=	self::SmartyGetInstance();

                        /*
                         * on recupere le template à utiliser qui se trouve par defaut dans
                         * /html/smarty/templates
                         */
                        if(!empty($params['dir_tpls']) && is_dir($params['dir_tpls'])){
                                $smarty->template_dir	=	$params['dir_tpls'];
                        }
                        if(!empty($params['template'])){
                                self::$template	=	$params['template'];
                        }else{
                                throw new Exception("Un template doit être definie et lisible !",400);
                        }
                        if( !is_readable($smarty->template_dir.self::$template )){
                                throw new Exception("Un template doit être definie et lisible !",401);
                        }
                        //Liaison des nom->val
                        if(is_array($params['binding']) && !empty($params['binding'])){

                                self::$bindSmarty	=	$params['binding'];

                        }

                        //Active ou non le cache de smarty
                        switch($params['cache']){
                                case true:
                                        $smarty->caching = true;
                                break;

                                case false:
                                        $smarty->caching = false;
                                break;

                                default:
                                        $smarty->caching = false;
                                break;
                        }
                        

                        //Informe de l'activation du layout
                        $controller_proc->activeSmartyTemplate();
                        

                }else{
                        throw new Exception("Set_Layout à besoin d'un tableau en paramétre !",402);
                }

            }else{
                throw new Exception("Pas de controller de processus !",403);
            }

        }catch(Exception $e){
            Oscar_Exception::getInstance()
                ->error($e->getCode(),$e->getMessage(),null,null);
        }

    }




    /*
     * Bind les param
     * et lance l'affichage via smarty
     */
    public function smartyDisplayMainView( $controller_buffer , $controller_params , $controller_proc ){

        //verifie si le template est actif
        if( $controller_proc->statusSmartyTemplate() ){

            $smarty =   self::SmartyGetInstance();

            //definie les variables reservés
            $var_sys	=	array(
                    "_pre_content"  => 'pre_content',
                    "_init_content" => 'init_content',
                    "_content"      => 'content',
                    "_post_content" => 'post_content'
            );

            //lie les nom aux variables
            foreach( self::$bindSmarty AS $key	=>	$name ){

                    if(array_key_exists($name,$var_sys)){

                            //Verification si l'affichage de ce module n'est pas annulé
                            if($controller_proc->getStatusDisplay($var_sys[$name])){

                                    $smarty->assign($key,$controller_buffer->getInMemory($var_sys[$name]));

                            }

                            }else{

                                    $smarty->assign($key,$controller_params->get_param($name));

                            }



            }


            //affiche le template
            $smarty->display(self::$template);

        }else{

            //Affichage du tout :-)
            //Verification si l'affichage de ce module n'est pas annulé
            if( $controller_proc->getStatusDisplay('pre_content') ){
                    echo $controller_buffer->getInMemory('pre_content');
            }

            if( $controller_proc->getStatusDisplay('init_content') ){
                    echo $controller_buffer->getInMemory('init_content');
            }

            if( $controller_proc->getStatusDisplay('content') ){
                    echo $controller_buffer->getInMemory('content');
            }

            if( $controller_proc->getStatusDisplay('post_content') ){
                    echo $controller_buffer->getInMemory('post_content');
            }

        }


    }
    
}

?>
