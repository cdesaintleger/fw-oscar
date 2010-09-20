<?php
/*
 * Interface Observer pour la gestion des erreurs
 *
 */
interface ierrorObservable{


    /*
     * Permet de lier un observeur
     */
    public function attach( ierrorObserver $obs );
    
    /*
     * Retir un observeur
     */
    public function detach( ierrorObserver $obs );
    
    /*
     * Envoi les données aux observateurs
     */
    public function notify( );

    /*
     * Recupére l'erreur courante
     */
    public function getError();

    /*
     * Methode de descrition de l'observeur
     */
    public function __toString();
    

}
?>
