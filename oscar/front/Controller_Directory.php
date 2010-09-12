<?php

require_once 'Oscar_Exception.php';
/*
 *
 *
 * Gestion des chemins 
 */


class Oscar_Front_Controller_Directory{


    public $controllersDirectory =   array();

    /*
     * Définition des chemins des controllers
     * Suppression des anciens s'il en existe
     */
    public function set_controller_directory( $path, $autoload, $pathauto  ){

        try{

			$this->controllersDirectory	=	array();

			if(!is_array($path)){

				if( is_dir($path)){

					$this->controllersDirectory[]	=	$path;

                                        //Gestion de l'autoload
                                        if( $autoload   ==  TRUE ){

                                            self::_configure_include_path( $chemin, $pathauto );

                                        }

				}else{

					throw new Oscar_Exception("Le chemin $path n'est pas valide ! ");
				}
			}else{
				foreach ( $path AS &$chemin ){

					if( is_dir($chemin)){

						$this->controllersDirectory[]	=	$chemin;


                                                //Gestion de l'autoload
                                                if( $autoload   ==  TRUE ){

                                                    self::_configure_include_path( $chemin, $pathauto );

                                                }

					}else{

						throw new Oscar_Exception("Le chemin $chemin n'est pas valide ! ");
					}
				}
			}

		}catch(Oscar_Exception $e){
                    echo $e->getMessage();
                }catch(Exception $e){
                    echo $e->getMessage();
                }

    }




    /*
     * Ajoute un chemin vers des controllers
     */
    public function add_controller_directory( $path = null ){

        try{

            if( $path != null ){

                if(!is_array($path)){
			if( is_dir($path)){
				$this->controllersDirectory[]	=	$path;

                                //Gestion de l'autoload
                                if( $autoload   ==  TRUE ){

                                    self::_configure_include_path( $path, $pathauto );

                                }
			}else{
                            throw new Oscar_Exception("\$path ne peut pas être un tableau! ");
                        }
		}else{
                    throw new Oscar_Exception("Le chemin $path n'est pas valide ! ");
                }

            }else{
                throw new Oscar_Exception("pas de chemin donné ! ");
            }

        }catch(Oscar_Exception $e){
            echo $e->getMessage();
        }catch(Exception $e){
            echo $e->getMessage();
        }

    }



    /*
     * Supprime un chemin vers les controllers
     */
    public function remove_controller_directory( $path = null ){
        try{
            if($path != null ){
                if(!is_array($path)){
                    if(in_array( $path , $this->controllersDirectory )){
                            $this->controllersDirectory	=	array_diff( $this->controllersDirectory , array($path) );
                    }
                }else{
                    throw new Oscar_Exception("\$path ne peut pas être un tableau! ");
                }
            }else{
                throw new Oscar_Exception("pas de chemin donné ! ");
            }


        }catch(Oscar_Exception $e){
            echo $e->getMessage();
        }catch(Exception $e){
            echo $e->getMessage();
        }
        

    }





    /*
     * Configure l'include path pour l'utoload
     */
    private static function _configure_include_path( $chemin , $pathauto ){

        if( !empty($pathauto) && is_array($pathauto)){

            foreach ( $pathauto AS &$dirauto ){
                //transforme le chemin '../application/module/controllers' vers '/../var/www/projet/application/module/<dirauto>'
                $dirauto    =   dirname(realpath($chemin)).'/'.$dirauto;

            }

            $includePath    =   implode(PATH_SEPARATOR, $pathauto);

            //configuration de l'include path
            set_include_path(get_include_path().PATH_SEPARATOR.$includePath);
            
        }

    }

    

    
}
?>
