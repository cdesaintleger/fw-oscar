<?php
class Oscar_compta{


    /*
     * Test la validité d'un numéro de siret
     */
    public static function check_siret($numsiret){

        //transformation de la chaine en tableau 
        $Tsiret =   str_split($numsiret);

        //Le numéro de siret doit contenir 14 chiffres
        if( count($Tsiret) != 14){
            return false;
        }

        //initialise le resultat
        $Restest    =   0;
        $curseur    =   0;

        //Passage sur chaque chiffre
        foreach($Tsiret AS &$num){

            //si le chiffre est paire
            $num    =  ((($curseur+1)%2)+1) * $num;

            //Si > 10 on lui retire 9
            if( $num >= 10 ){
                $num-=9;
            }
            $Restest    +=  $num;

            $curseur++;
        }

       
        //Si $Restest et multiple de 10 le siret est correct ..
        if( $Restest%10 == 0 ){
            return true;
        }else{
            return false;
        }


    }
}