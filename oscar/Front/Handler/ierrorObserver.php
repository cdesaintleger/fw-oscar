<?php
/*
 * Interface Observer pour la gestion des erreurs
 *
 */
interface ierrorObserver{
    
    /*
     * Envoi les données à l'observeur
     */
    public function update( ierrorObservable $msg );

    /*
     * Methode de descrition de l'observeur
     */
    public function __toString();
    

}
?>
