<?php
class Oscar_samba{

    //stdErr ou erreur transmise par l'objet
    private $_stdErr = array();

    //stdOut
    private $_stdOut = null;

    //Partage à monter, syntaxe : //x.x.x.x/partage
    private $_share =   null;

    /*Point de montage
     * doit être un repertoire existant
     * appartenant à l'utilisateur courant
     */
    private $_pmount    =   null;
    
    //Etat du montage de l'objet TRUE | FALSE
    private $_ismounted =   false;

    //option de montage
    private $_options   =   array();

    //paramétres de commande
    private $_params    =   array();

    private $_no_password   =   null;

    public $_cmd    =   "";



    /** * Constructeur
    * @param string $share Partage distant syntaxe : //x.x.x.x/ShareName
    * @param string $pmount	Point de montage , chemin valide relatif ou absolue
    * @param array $options tableau associatif contenant les options necessaires au montage , lef clef sont les suivantes
    * array(
     * "username" => "Login pour si connecter au partage",
     * "password" => "Mot de passe si necessaire",
     * "workgroup"=> "Groupe de travail"
     * )
    * @throws Exception
    * @return null */
    public function __construct( $share, $pmount, $options = array() ){

        try {
            $this->_stdErr['object']    =   "";
            $this->_stdErr['exec']    =   "";
            
            //définition des attributd de l'objet
            $this->_share   =   $share;

            //Dfinition du partage en non monté
            $this->_ismounted   =   FALSE;

            //Verification et initialisation du point de montage
            if( !$this->_set_pmount($pmount) ){
                //Erreur sur le point de montage
                throw new Exception($this->_stdErr['object']);

            }

            //Présence de paramétre pour le montage
            if( !empty( $options) && is_array($options) ){

                if( array_key_exists("username", $options) ){
                    $this->_options["username"]  =   $options["username"];
                }
                if( array_key_exists("password", $options) && $options["password"] != null ){
                    $this->_options["password"]  =   $options["password"];
                    $this->_no_password  =   FALSE;
                }else{
                    //Option nécessaire s'il n'y a pas de mot de passe
                    $this->_params[]    =   "guest";
                    $this->_no_password  =   TRUE;
                }
                if( array_key_exists("workgroup", $options) ){
                    $this->_options["workgroup"]  =   $options["workgroup"];
                }

            }
            return TRUE;
            
        }  catch (Exception $e){

            $err    =   $e->getMessage();
            if(is_array($err) ){
                $err    =   implode(PHP_EOL."--->", $err);
            }

            echo "Une erreur est survenue : ".$err.PHP_EOL;
            return FALSE;

        }
        
    }

    /** * Informe si le partage est monté ou non
    * @return boolean TRUE si monté FALSE si non monté */
    public function is_mounted(){
        return $this->_ismounted;
    }

    /** * Retourne la sortie standard de la derniére commande
    * @return string retour stdout*/
    public function get_stdOut(){
        return $this->_stdOut;
    }

    /** * Retourne les derniére erreurs
    * @return array retour stdErr*/
    public function get_stdErr(){
        return $this->_stdErr;
    }

    /** * Monter un partage reseau
    * @throws Exception
    * @return boolean TRUE si pas de probleme , FALSE dans le cas contraire */
    public function mount(){

        //Si le partage est déjà monté , on ne le remonte pas une seconde fois
        if( !$this->_ismounted ){

            //Gestion des paramétres et options à utiliser
            $params =   "";
            if( $this->_no_password ){
                $params = implode (" ",$this->_params);
            }

            //Transforme le tableau de paramétres en une chaine sécurisé : option=value,option2=value
            foreach( $this->_options AS $key => &$value ){
                $option[] = $key."=".escapeshellarg($value);
            }
            $options    = " -o ".implode(",", $option)." ".$params.",file_mode=0777,dir_mode=0777";

            //La commande finale devient
            $cmd    =   "sudo /usr/bin/smbmount ".$this->_share." ".$this->_pmount." ".$options;

            try{
                //Execution de la commande de montage
                if( !$this->_shell_execute($cmd) ){
                    throw new Exception($this->_stdErr['exec']);
                }
                //Test le retour de la commande qui doit �tre vide
                if( !empty($this->_stdOut) ){
                    throw new Exception($this->_stdErr['exec']);
                }

            }  catch (Exception $e){

                $err    =   $e->getMessage();
                if(is_array($err) ){
                    $err    =   implode(PHP_EOL."--->", $err);
                }
                
                echo "Une erreur est survenue au montage : ".$err.PHP_EOL;
                return false;
            }

            //Définition du partage en : "monté"
            $this->_ismounted   =   TRUE;
            
            return true;
            

        }else{
            return true;
        }

    }

    /** * Démonter un partage réseau
    * @throws Exception
    * @return boolean TRUE si pas de probleme , FALSE dans le cas contraire */
    public function umount(){

        $cmd    =   "sudo umount ".$this->_pmount;

        try{
            //Execution
            if( !$this->_shell_execute($cmd) ){
                throw new Exception($this->_stdErr['exec']);
            }
            //Test le retour de la commande qui doit �tre vide
            if( !empty($this->_stdOut) ){
                throw new Exception($this->_stdErr['exec']);
            }
        }  catch (Exception $e){

            $err    =   $e->getMessage();
            if(is_array($err) ){
                $err    =   implode(PHP_EOL."--->", $err);
            }

            echo "Une erreur est survenue au d�montage : ".$err.PHP_EOL;
            return FALSE;
        }

        //Définition du partage en : "non monté"
        $this->_ismounted   =   FALSE;

        return true;

    }

    /** * Récupérer les partages actuellement montés
    * @throws Exception
    * @return boolean TRUE si pas de probleme , FALSE dans le cas contraire */
    public function mount_list( $rch=null ){

        /* Préparation de la commande */
        if( !empty( $rch ) ){
            $cmd = 'mount -l | grep '.escapeshellarg( $rch ).' ';
        }else{
            $cmd = 'mount -l ';
        }

        try{
            //Execution
            if( !$this->_shell_execute($cmd) ){
                throw new Exception($this->_stdErr['exec']);
            }
        }  catch (Exception $e){

            $err    =   $e->getMessage();
            if(is_array($err) ){
                $err    =   implode(PHP_EOL."--->", $err);
            }

            echo "Une erreur est survenue : ".$err.PHP_EOL;
            return false;
        }

        return true;

    }

    /** * Execution des processus systéme
    * @param string $cmd commande shell à executer
    * @return boolean TRUE si tout est ok , FALSE dans le cas contraire */
    private function _shell_execute( $cmd ){

        $descripteurs    =   array(
            0   =>  array('pipe', 'r'),
            1   =>  array('pipe','w'),
            2   =>  array('pipe','w')
        );

        //Enregistrement de la commande
        $this->_cmd =   $cmd;

        /* Ouverture process */
        $smb    = proc_open($cmd, $descripteurs, $pipes);

        /* Recupération de la réponse */
        $this->_stdOut    =   stream_get_contents($pipes[1]);

        /* Recuération des erreurs */
        $this->_stdErr["shell"]     =   null;
        $this->_stdErr["shell"]     =  stream_get_contents($pipes[2]);

        /* Fermeture des descripteurs */
        fclose( $pipes[0] );
        fclose( $pipes[2] );
        fclose( $pipes[1] );
        $return_value    =   proc_close($smb);

        //En cas d'echec de la fermeture du processus
        if( $return_value == -1 ){
            $this->_stdErr["exec"]    =   "Erreur � la fermeture du process ".PHP_EOL;
            return FALSE;
        }

        //En cas d'erreur pendant l'execution de la commande
        if(!empty( $this->_stdErr["shell"] )){
            return FALSE;
        }else{
            return TRUE;
        }

        

    }

    /** * Vérifie et initialise le point de montage
    * @param string $dir chemin à vérifier
    * @return boolean TRUE si tout est ok , FALSE dans le cas contraire */
    private function _set_pmount( $dir = null ){

        /*
         * Tente de le creer s'il n'hexiste pas encore
         */
        if(!file_exists($dir)){
            mkdir($dir,0777,TRUE);
            chmod($dir,0777);
        }

                //Recherche du chemin absolue
        $absolute_dir   =   realpath($dir);

        /*
         * Vérifie l'existance du point de montage
         * et sont access en écriture
         */
        if(!is_dir($absolute_dir) || !is_writable($absolute_dir)){

            $this->_stdErr['object']    =   "Le point de montage doit être existant et accessible en écriture";
            return FALSE;
        }

        /*
         * Vérifie le propriétaire du point de montage
         */
         $pwuid =   posix_getpwuid(fileowner($absolute_dir));
         $whoami    =   exec("whoami");

         if( $pwuid['name'] != $whoami ){

            $this->_stdErr['object']    =   "Le point de montage doit appartenir à l'utilisateur courant : ".$whoami;
            return FALSE;

         }
         
         /*
          * Définition du point de montage
          */
         $this->_pmount =   $absolute_dir;

         return true;

    }

}
?>
