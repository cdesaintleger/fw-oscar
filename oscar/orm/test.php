<?php
require_once 'Oscar_Orm.php';


/*
 * Connexion à la base
 */
$PDO	=	new Oscar_db_manager("mysql:host=127.0.0.1;dbname=amavis",'root','2006');


/*
 * Creation du model
 */
class users extends Oscar_Orm {
	
	//definition de la clef primaire
	protected $_table	=	"users"; 
	protected $_pkey	=	"id";
		

	
}

//Appel du model
$users = new users();

//INSERT

//création de la ligne à creer #Méthode 1

/*
$data	=	array('priority'=>12,
	'policy_id'=>11,
	'email'=>'a',
	'fullname'=>'cdsll');
*/
//creation de l'enregistrement , et retour de l'id
//$idrecord	=	$users->createRecord($data);

//creation de la ligne à creer #Méthode 2

$users->set_field('priority',12);
$users->set_field('policy_id',12);
$users->set_field('email','christophe@dsl-dfr.eu');
$users->set_field('fullname',12);

//creation de l'enregistrement , et retour de l'id
//$idrecord	=	$users->createRecord();

//Pour ajouter un nouvel enregistrement dans la foulé , il faut appeler 
//$users->cleanRecord();



//UPDATE



//$users->set_field('fullname','stopher');

//$nbrowsupdated	=	$users->updateRecord();



//READ

//$users->getRecord(12);

//var_dump($users->get_field('email'));

$success	=	$users->deleteRecord(12);

var_dump($success);

?>