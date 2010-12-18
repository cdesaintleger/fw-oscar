<?php
//interface du driver
require_once 'Oscar_driver_ident_abstract.php';

class mongo_driver extends Oscar_driver_ident_interface{


    /**
     * Accesseurs
     *
     */
    /*
	 * Définition d'un handle sur l'acces à la base de données
	 */
	public function set_instance_DDB( $value ){

		(!empty($value))?self::$_Instance_Ddb	=	$value:null;

	}

    /*
	 * Définition de la table contenant les login/mdp
	 */
	public function set_ident_table_name( $value ){

		(!empty($value))?self::$_ident_table_name	=	$value:null;

	}


	/*
	 * Définition de la table contenant les connexions et infos secrete
	 * Par defaut : Oscar_temp_ident ( 1 nom != par domaine )
	 */
	public function set_Oscar_temp_table_name( $value ){

		(!empty($value))?self::$_oscar_temp_table_name	=	$value:null;

	}

	/*
	 * Définition du champ contenant les logins
	 */
	public function set_login_field_name( $value ){

		(!empty($value))?self::$_login_field_name	=	$value:null;

	}

	/*
	 * Définition du champ contenant les mots de passe
	 */
	public function set_password_field_name( $value ){

		(!empty($value))?self::$_password_field_name	=	$value:null;

	}

  
    /* Retourne le champ ipuser paramétré */
    public function get_tbl_ip_user(){

        return self::$_oscar_ip_user;

    }

    /* Retourne le champ navuser paramétré */
    public function get_tbl_nav_user(){

        return self::$_oscar_nav_user;

    }

    /* Retourne le champ data */
    public function get_tbl_data(){

        return self::$_oscar_data;
    }


    /*
     * Methodes
     *
     * //monte la collection
        parent::__construct($this->getDbInstance(),$this->_table);
     *
     */

    public function session_client_is_valide( $key, $login ){

        //on monte la collection
        $temp_ident =   new MongoCollection( self::$_Instance_Ddb, self::$_oscar_temp_table_name);

        //on recherche les data utilisateur
        $tdata  =   $temp_ident->findOne(array(
            self::$_oscar_key_unique => $key,
            self::$_oscar_user_login => $login
            ),
                array(
                    self::$_oscar_user_login,
					self::$_oscar_key_unique,
					self::$_oscar_ip_user,
					self::$_oscar_nav_user,
					self::$_oscar_data,
					self::$_oscar_tcreation,
					self::$_oscar_texpiration
                ));

        return $tdata;

    }

    public function identify( $login, $pwd ){

        //on monte la collection
        $ident_table =   new MongoCollection( self::$_Instance_Ddb, self::$_ident_table_name);

        //on cherche les infos client
        $res    =   $ident_table->findOne(array(
            self::$_login_field_name => $login,
            self::$_password_field_name => $pwd
            ));

        return $res;

    }

    public function create_temp_record( array $data ){

        //on monte la collection
        $temp_ident =   new MongoCollection( self::$_Instance_Ddb, self::$_oscar_temp_table_name);

        //on commence par faire le ménage
        $temp_ident->remove(array(self::$_oscar_user_login => $data['login']), array("justOne" => true));

        //et on recree l'authentification
        $temp_ident->insert(array(
            self::$_oscar_user_login => $data['login'],
            self::$_oscar_key_unique => $data['oscar_key'],
            self::$_oscar_ip_user    => $data['ip'],
            self::$_oscar_nav_user   => $data['navigateur'],
            self::$_oscar_data       => $data['private_data'],
            self::$_oscar_tcreation  => $data['t_creation'],
            self::$_oscar_texpiration=> $data['t_expiration']
        ));
        
		
    }
}
?>
