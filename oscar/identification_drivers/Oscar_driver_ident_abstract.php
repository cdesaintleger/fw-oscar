<?php
abstract  class Oscar_driver_ident_interface{

    /*
	 * Nom de la table utilise pour stocker les infos de session
	 */
	protected static $_oscar_temp_table_name	=	"Oscar_temp_ident";

	/*
	 * Nom du champ de la table temporaire pour stocker la clef unique ( timestamp+grain de sel )
	 */
	protected static $_oscar_key_unique	=	"Oscar_ukey";

	/*
	 * Nom du champ de la table temporaire pour stocker le login ( login+grain de sel )
	 */
	protected static $_oscar_user_login	=	"Oscar_ulog";

	/*
	 * Nom du champ de la table temporaire pour stocker l'adresse ip ( ip+grain de sel )
	 */
	protected static $_oscar_ip_user		=	"Oscar_ipc";

	/*
	 * Nom du champ de la table temporaire pour stocker le navigateur ( navigateur+grain de sel )
	 */
	protected static $_oscar_nav_user		=	"Oscar_nus";

	/*
	 * Nom du champ de la table temporaire pour stocker les données privés serialisés
	 */
	protected static $_oscar_data	=	"Oscar_data";

	/*
	 * Nom du champ de la table temporaire pour stocker le timestamp de création ( timestamp+grain de sel )
	 */
	protected static $_oscar_tcreation	=	"Oscar_tcr";

	/*
	 * Nom du champ de la table temporaire pour stocker le timestamp d'expiration ( timestamp+grain de sel )
	 */
	protected static $_oscar_texpiration	=	"Oscar_tex";


	/**
	 * FIN DONNE MANUELLES
	 */


	/*
	 * Nom de la table contenant les comptes utilisateurs
	 */
	protected static $_ident_table_name	=	null;

	/*
	 * Nom du champ pour les loggin
	 */
	protected static $_login_field_name	=	null;

	/*
	 * Nom du champ pour les mots de passe
	 */
	protected static $_password_field_name	=	null;

	/*
	 * Nom du champ pour l'etat du compte 1 actif 0 inactif
	 */
	protected static $_status_field_name	=	null;

    /*
     *
     */
    protected static $_Instance_Ddb = null;


    
}
?>
