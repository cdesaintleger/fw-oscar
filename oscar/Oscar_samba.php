<?php
/*
 * chmod 755 /sbin/umount.cifs
 * chmod +s /sbin/umount.cifs
 *
 *
 *
 */


class Oscar_samba {

    /* Descriteur de flux */
    private $_flux  =   null;

    /* Attributs reponse et erreur */
    private $_reponse   =   null;
    private $_erreur    =   null;

    public function  __construct() {
        $this->_flux    =   array(
            0   =>  array('pipe', 'r'),
            1   =>  array('pipe','w'),
            2   =>  array('pipe','r')
        );
    }

    /* Retourne l'erreur généré par la derniére commande */
    public function get_erreur(){
        return $this->_erreur;
    }

    /* Retourne la reponse généré par la derniére commande */
    public function get_reponse(){
        return $this->_reponse;
    }



    /*
     * Execution des commandes samba
     */
    private function _execute($cmd){

        /* Ouverture du flux */
        $smb    = proc_open($cmd, $this->_flux, $pipesarray);

        /* Recupération de la réponse */
        $this->_reponse    =   stream_get_contents($pipesarray[1]);

        /* Recuération des erreurs */
        $this->_erreur    =  stream_get_contents($pipesarray[2]);

        /* Fermeture des descripteurs */
        fclose( $pipesarray[0] );
        fclose( $pipesarray[2] );
        fclose( $pipesarray[1] );
        $return_value    =   proc_close($smb);

        /* Retour en fonction de la réponse */
        if( $return_value == 0 ){
            return true;
        }else{
            return false;
        }

    }


    
    
    /*
     * Récupére les montages courants de la machine
     * avec ou sans filtre
     */
    public function mount_list($rch=null){


        /* Préparation de la commande */
        if( !empty( $rch ) ){
            $cmd = 'mount -l | grep '.escapeshellarg( $rch ).' ';
        }else{
            $cmd = 'mount -l ';
        }

        /* Execution */
        return $this->_execute($cmd);

    }


    /*
     * Montage d'un partage réseau
     */
    public function mount_dir($orig,$dest,$options=array()){

        $opt =  array();
        $err    =   false;

        $this->_erreur  =   "";
        $this->_reponse =   "";

        if(array_key_exists("username", $options) ){

            //authentification par login mot de passe
            $opt[]  =   'username='.escapeshellarg($options['username']);

        }

        if(array_key_exists("password", $options) ){

            //authentification par login mot de passe
            $opt[]  =   'password='.escapeshellarg($options['password']);

        }

        if(array_key_exists("domain", $options) ){

            //authentification par login mot de passe dans un domaine
            $opt[]  =   'workgroup='.escapeshellarg($options['domain']);

        }

       
        /* Creation de la chaine d'options */
        $cmdopt =   '';
        if( count($opt)> 0 ){

            $cmdopt = "-o ".implode(',', $opt);

        }

        /* Test de l'existance de la destination */
        if( !is_dir($dest) ){

            /* Tentative de création du repertoire destination */
            if( !mkdir($dest) ){
                $erreur = "Impossible de créer le Répertoire de destination ".PHP_EOL;
                /* Déclaration d'erreur */
                $err    =   TRUE;
            }

        }

        /* Si aucune erreur détecté on lance la commande */
        if( $err === FALSE ){

            $cmd    =   "/usr/bin/smbmount ".$orig." ".$dest." ".$cmdopt;
            
            /* Execution du montage */
            return $this->_execute($cmd);

        }else{

            $this->_erreur  =   $err;
            return false;

        }

    }



    /* Démonte un répertoire */
    public function umount_dir( $point_montage ){

        $cmd    =   "smbumount ".escapeshellarg($point_montage);

        return $this->_execute($cmd);

    }


}
?>
