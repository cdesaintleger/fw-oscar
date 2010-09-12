<?php
//Inclusiotn de la bibliothéque
require_once "Oscar_Cache.php";

//récupére l'instance unique
$cache	=	Oscar_cache::getInstance();

/*
 * NOTE:
 * le temps de validité par defaut est modifiable via la méthode 
 * 		setTime_valid($time_valid)
 * Le répertoire par defaut est modifiable via la méthode 
 * 		setCache_directory($cache_directory)
 */

$cache->setCache_directory("/var/www/CACHE/");


//variable de test à mettre en cache
$toto	=	array("aa"=>"bb");

/*
 * ajout de la variable en cache avec les infos suivantes:
 * "mon_tableau" => clef indéxée 
 * $toto	=>	variable à mettre en cache
 * 3600		=> 	temps de validité de ce cache ( si null alors valeur par defaut  )
 * array('CACHE_DIR'=>'reptest/')	=> tableau d'options ( pour le moment une seule option dispo , 
 * le répertoire de destination du fichier de cache pour cette valeure )
 * 
 */
if($cache->get_cache("mon_tableau",$reponse)){
	
	echo "Val provient du cache";
	echo $reponse.'<br>';
}else{
	echo "Val pas en cache";
	$cache->add_cache("mon_tableau",$toto,3600,array('CACHE_DIR'=>'reptest/'));
}





echo '<br>';

/*
 * Méthode pour savoir si une valeur est actuellement en cache , et encore valide
 * retourne TRUE => ok 
 * retourne FALSE=> périmé ou inéxistante
 */
//var_dump($cache->is_cached("mon_tableau"));

echo '<br>';

/*
 * Récupére une valeure en cache 
 * ( la valeur est récupéré dans le deuxiéme argument passé par référence )
 * Les paramétres sont les suivants : 
 *  get_cache(clef_a_récupérer,variable dans laquelle on récupere les resultats)
 */
/*
if(!$cache->get_cache("mon_tableau",$reponse)){
	echo "Impossible de retrouver cette valeur en cache";
}else{
	var_dump($reponse);
}

echo '<br>';

*/
/*
 * Suppression d'une valeure en cache
 * si un paramétre est présent , il tente de supprimer 
 * cette clef du cache uniquement .
 * 
 * S'il n'y a pas de paramétre , tout le cache est réinitialisé
 */
//$cache->cache_destroy("mon_tableau");


/*
 * Cache supprimé , retourne donc false
 */
//var_dump($cache->is_cached("mon_tableau"));

?>
