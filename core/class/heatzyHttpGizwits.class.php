<?php
  /**
 *
 * @brief Class HttpGizwits de communication avec le serveur Gizwits 
 *
 */
class HttpGizwits {
    /*     * *************************Attributs****************************** */
    public static $HeatzyAppId = "c70a66ff039d41b4a220e198b0fcc8b3";
    public static $UrlGizwits = "https://euapi.gizwits.com";
    
    //public static $ConnectTimeout = 30 ;
    public static $Default_Timeout = 60 ;
    public static $RecurrenceAPI = 5 ;
    
    public static $_MaxError = 200 ;
    
    public static $DebugExport = false ;

    /*     * ***********************Methode static*************************** */
    /**
     * @brief Fonction de connexion au serveur Gizwits
     *        cette fonction permet de récuperer le token user
     * 
     * @param $User   Adresse email de l'utilisateur
     * @param $Passwd Mot de passe d'acces au cloud
     * @param $Lang   La langue en par defaut
     * 
     * @return Un tableau associatif ou false en cas d'erreur       
     */
//class HttpGizwits
    public static function Login($User, $Passwd, $Lang='en') {
     
        if(empty($User) || empty($Passwd)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }

        /// Preparation de la requete : json
        $data = json_encode( array('username' => $User, 'password' => $Passwd, 'lang' => $Lang) ) ;

        /// Parametres cUrl
        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId
            ),
            CURLOPT_URL => self::$UrlGizwits."/app/login",
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout ),
            CURLOPT_POSTFIELDS => $data
        );

        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;
             
        /// Configuration des options
        curl_setopt_array($gizwits, $params);
        
        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode );
            return false;
        }
        
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.')'.': '.$aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        if( self::$DebugExport )
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        
        return $aRep;
    }
  
   /**
     * @brief Fonction qui permet de récuperer la liste des devices did
     * 
     * @param $UserToken   Token utilisateur d'acces au cloud
     * 
     * @return Un tableau associatif ou false en cas d'erreur
     */
//class HttpGizwits
    public static function GetProduitInfo($ProductKey) {

        if(empty($ProductKey)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }
        
        /// Parametres cUrl
        $params = array(
            CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/datapoint?product_key='.$ProductKey,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout )
        );

        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;
             
        /// Configuration des options
        curl_setopt_array($gizwits, $params);

        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        if( self::$DebugExport )
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        
        return $aRep;
    }
  
    /**
     * @brief Fonction qui permet de récuperer la liste des devices did
     * 
     * @param $UserToken   Token utilisateur d'acces au cloud
     * 
     * @return Un tableau associatif ou false en cas d'erreur
     */
//class HttpGizwits
    public static function Bindings($UserToken) {
        
        if(empty($UserToken)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }
        
        /// Parametres cUrl
        $params = array(
            CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                    'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/bindings?limit=20&amp;skip=0',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout )
        );
    
        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;
             
        /// Configuration des options
        curl_setopt_array($gizwits, $params);

        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode );
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        return $aRep;
    }
    
    /**
     * @brief Fonction qui permet de récuperer la liste taches
     *        associé a un device did
     *
     * @param $UserToken   Token utilisateur d'acces au cloud
     * @param $Did           Identifiant du module dans le cloud
     *
     * @return Un tableau associatif ou false en cas d'erreur
     */
//class HttpGizwits
    public static function GetSchedulerList($UserToken, $Did, $Skip = 0, $Limit = 30) {

        /*
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $UserToken='.$UserToken);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Did='.$Did);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Skip='.$Skip);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Limit='.$Limit);
        */
    
        if(empty($UserToken) || empty($Did)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }

        /// Parametres cUrl
        $params = array(
            CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                    'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/devices/'.$Did.'/scheduler?limit='.$Limit.'&amp;skip='.$Skip,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout )
        );

        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;

        /// Configuration des options
        curl_setopt_array($gizwits, $params);

        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('DID : ', __FILE__) . $Did.' '.__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        return $aRep;
    }

    /**
     * @brief Fonction qui permet de créer une tache
     *
     * @param $UserToken   Token utilisateur d'acces au cloud
     * @param $Did           Identifiant du module dans le cloud
     * @param $Id           L'identifiant de la tache
     * @param $Param       Les parametres de la tache
     * 
     * @return l'id de la tâche ou false en cas d'erreur
     */
//class HttpGizwits
    public static function CreateScheduler($UserToken, $Did, $Param) {

        /*
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $UserToken='.$UserToken);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Did='.$Did);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Param='.var_export($Param, true));
        */
      
        if(empty($UserToken) || empty($Did) || empty($Param)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }
    
        /// Preparation de la requete : json
        $data = json_encode( $Param ) ;
        
        /// Parametres cUrl
        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                    'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/devices/'.$Did.'/scheduler' ,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout ),
            CURLOPT_POSTFIELDS => $data
        );
        
        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;

        /// Configuration des options
        curl_setopt_array($gizwits, $params);

        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 201 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
            return false;
        }
        
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
    
        //if(isset($aRep['error_message'])) {
        //    throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        //}
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        return $aRep;
    }
  
    /**
     * @brief Fonction qui permet de modifier une tache
     *
     * @param $UserToken   Token utilisateur d'acces au cloud
     * @param $Did           Identifiant du module dans le cloud
     * @param $Id           L'identifiant de la tache
     * @param $Param       Les parametres de la tache
     * 
     * @return Un tableau associatif ou false en cas d'erreur
     */
//class HttpGizwits
    public static function UpdateScheduler($UserToken, $Did, $Id, $Param) {

        /*
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $UserToken='.$UserToken);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Did='.$Did);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Id='.$Id);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Param='.var_export($Param, true));
        */
        
        if(empty($UserToken) || empty($Did) || empty($Id) || empty($Param)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }
    
        /// Preparation de la requete : json
        $data = json_encode( $Param ) ;

        /// Parametres cUrl
        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                    'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/devices/'.$Did.'/scheduler/'.$Id,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout ),
            CURLOPT_POSTFIELDS => $data
        );
        
        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;

        /// Configuration des options
        curl_setopt_array($gizwits, $params);

        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
            return false;
        }
        
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
    
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        return $aRep;
    }
  
    /**
     * @brief Fonction qui permet de supprimer une tache
     *
     * @param $UserToken   Token utilisateur d'acces au cloud
     * @param $Did           Identifiant du module dans le cloud
     * @param $Id           L'identifiant de la tache
     * 
     * @return true ou false en cas d'erreur
     */
//class HttpGizwits
    public static function DeleteScheduler($UserToken, $Did, $Id ) {
    
        /*
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $UserToken='.$UserToken);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Did='.$Did);
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $Id='.$Id);
        */
    
        if(empty($UserToken) || empty($Did) || empty($Id) ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }
        
        /// Parametres cUrl
        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                    'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/devices/'.$Did.'/scheduler/'.$Id,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout ),
        );
        
        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;

        /// Configuration des options
        curl_setopt_array($gizwits, $params);

        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
            return false;
        }
        
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
    
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        return true ;
    }
  
    /**
     * @brief Fonction qui permet de modifier les informations d'accroche
     * 
     * @param $UserToken   Token utilisateur d'acces au cloud
     * @param $Did         Identifiant du module dans le cloud
     * @param $DevAlias       Nouvel alias
     * 
     * @return ou false en cas d'erreur
     */
//class HttpGizwits
    public static function SetBindingInformation($UserToken, $Did, $DevAlias) {
    
        if(empty($UserToken) || empty($Did) || empty($DevAlias)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }

        /// Preparation de la requete : json
        $data = json_encode( array('dev_alias' => $DevAlias ) ) ;
        
        /// Parametres cUrl
        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                    'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/bindings/'.$Did,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout ),
            CURLOPT_POSTFIELDS => $data
        );
        
        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;

        /// Configuration des options
        curl_setopt_array($gizwits, $params);

        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        return $aRep;
    }
    
    /**
     * @brief Fonction qui permet de positionner le status du device did
     * 
     * @param $UserToken   Token utilisateur d'acces au cloud
     * @param $Did         Identifiant du module dans le cloud
     * @param $Consigne           La consigne
     * 
     * @return Un tableau vide ou false en cas d'erreur
     */
//class HttpGizwits
    public static function SetConsigne($Did, $Consigne, $Recurrence = 0) {
        
		$UserToken = config::byKey('UserToken','heatzy','');
      
        if(empty($UserToken) || empty($Did) || empty($Consigne)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }

        /// Preparation de la requete : json
        $data = json_encode( $Consigne ) ;

        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'-'.var_export($data, true).' (Recurrence '.$Recurrence.')');
        
        /// Parametres cUrl
        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                    'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/control/'.$Did,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            //CURLOPT_CONNECTTIMEOUT => self::$ConnectTimeout,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy', self::$Default_Timeout ),
            CURLOPT_POSTFIELDS => $data
        );

        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
          return false;

        /// Configuration des options
        curl_setopt_array($gizwits, $params);
    
        /// Excute la requete
        $result = curl_exec($gizwits);
    
        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){ // Si erreur technique
            log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- erreur http '.$httpcode.' - timeout '.config::byKey('Timeout_value','heatzy',self::$Default_Timeout ).'s (Recurrence '.$Recurrence.')');
            
            if( $Recurrence < self::$RecurrenceAPI ){
                log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- On retente (Recurrence '.$Recurrence.')');
                sleep(2); // tempo
                
                //$aRep = self::SetConsigne($UserToken, $Did, $Consigne , $Recurrence + 1) ;
              	$aRep = self::SetConsigne( $Did, $Consigne , $Recurrence + 1) ;
                if( $aRep === false ){
                    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative KO (Recurrence '.$Recurrence.')');
                    return false;
                }
                log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative OK (Recurrence '.$Recurrence.')');
            }
            else
                return false; // Retour KO si trop de tentative récursive
        }
        else{ // Le serveur a répondu
            $aRep = json_decode($result, true);
            if( $aRep['error_code'] == '9004' || $aRep['error_code'] == '9006' ) {
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': error_code '.$aRep['error_code'].' (Recurrence '.$Recurrence.')');
                
                if( $Recurrence < self::$RecurrenceAPI ){
                    // erreur token invalide, alors on va en chercher un nouveau
                    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- On retente (Recurrence '.$Recurrence.')');
                    
                    if( heatzy::Login() ){
                        $UserToken = config::byKey('UserToken','heatzy','none'); // Je récupère le nouveau token
                        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Login() OK - Nouveau Token ('.$UserToken.') (Recurrence '.$Recurrence.')');
                        
                        //$aRep = self::SetConsigne($UserToken, $Did, $Consigne, $Recurrence + 1) ;
                        $aRep = self::SetConsigne( $Did, $Consigne, $Recurrence + 1) ;
                        if( $aRep === false ){
                            log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative KO (Recurrence '.$Recurrence.')');
                            return false;
                        }
                        log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative OK (Recurrence '.$Recurrence.')');
                    }
                    else{
                        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Login() KO'.$Recurrence.')');
                        return false;
                    }
                }
                else
                    return false; // Retour KO si trop de tentative récursive
            }
            else{
                /// Réponse OK - Décodage de la réponse
                $aRep = json_decode($result, true);
            }
        }
        
        log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- http OK (Recurrence '.$Recurrence.')');
        
        //   if(isset($aRep['error_message'])) {
        //       throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        //   }
     
     /*
        if( $aRep['error_code'] == '9004' || $aRep['error_code'] == '9006' ) { 
            // erreur token invalide, alors on va en chercher un nouveau
            if( heatzy::Login() ){
              
                $UserToken = config::byKey('UserToken','heatzy','none');
                
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Login() OK - Nouveau Token ('.$UserToken.')');
              
                /// Parametres cUrl
                $params = array(
                    CURLOPT_POST => 1,
                    CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                            'X-Gizwits-User-token: '.$UserToken
                    ),
                    CURLOPT_URL => self::$UrlGizwits.'/app/control/'.$Did,
                    CURLOPT_FRESH_CONNECT => 1,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_FORBID_REUSE => 1,
                    CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout ),
                    CURLOPT_POSTFIELDS => $data
                );

                /// Initialisation de la ressources curl
                $gizwits = curl_init();
                if ($gizwits === false)
                  return false;

                /// Configuration des options
                curl_setopt_array($gizwits, $params);
            
                /// Excute la requete
                $result = curl_exec($gizwits);
            
                /// Test le code retour http
                $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

                /// Ferme la connexion
                curl_close($gizwits);

                if( $httpcode != 200 && $httpcode != 400 ){
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
                    return false;
                }

                ///Décodage de la réponse
                $aRep = json_decode($result, true);
            }
            else{
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Login() KO');
                return false;
            }
        }
    */
        
        return $aRep;
    }
    
    /**
     * @brief Fonction qui permet de récuperer le dernier status du device did
     * 
     * @param $Did         Identifiant du module dans le cloud
     * 
     * @return Un tableau associatif ou false en cas d'erreur
     */
//class HttpGizwits
    public static function GetConsigne($Did, $Recurrence = 0 ) {
              
      	$UserToken = config::byKey('UserToken','heatzy','');
      
        if(empty($Did) || empty($UserToken)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }
              
        /// Parametres cUrl
        $params = array(
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/devdata/'.$Did.'/latest',
            CURLOPT_RETURNTRANSFER => 1,
            //CURLOPT_CONNECTTIMEOUT => self::$ConnectTimeout,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy', self::$Default_Timeout )
        );
        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;
        /// Configuration des options
        curl_setopt_array($gizwits, $params);
        
        /// Excute la requete
        $result = curl_exec($gizwits);
      
        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- erreur http '.$httpcode.' - timeout '.config::byKey('Timeout_value','heatzy',self::$Default_Timeout ).'s (Recurrence '.$Recurrence.')');
            
            if( $Recurrence < self::$RecurrenceAPI ){
                log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- On retente (Recurrence '.$Recurrence.')');
                sleep(2); // tempo
                
                //$aRep = self::GetConsigne($UserToken, $Did , $Recurrence + 1) ;
              $aRep = self::GetConsigne( $Did , $Recurrence + 1) ;
                if( $aRep === false ){
                    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative KO (Recurrence '.$Recurrence.')');
                    return false;
                }
                log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative OK (Recurrence '.$Recurrence.')');
            }
            else
                return false;
        }
        else{ // Le serveur a répondu
            $aRep = json_decode($result, true);
            if( $aRep['error_code'] == '9004' || $aRep['error_code'] == '9006' ) {
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': error_code '.$aRep['error_code'].' (Recurrence '.$Recurrence.')');
                
                if( $Recurrence < self::$RecurrenceAPI ){
                    // erreur token invalide, alors on va en chercher un nouveau
                    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- On retente (Recurrence '.$Recurrence.')');
                    
                    if( heatzy::Login() ){
                        $UserToken = config::byKey('UserToken','heatzy','none'); // Je récupère le nouveau token
                        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Login() OK - Nouveau Token ('.$UserToken.') (Recurrence '.$Recurrence.')');
                        
                        //$aRep = self::GetConsigne($UserToken, $Did , $Recurrence + 1) ;
                      	$aRep = self::GetConsigne( $Did , $Recurrence + 1) ;
                        if( $aRep === false ){
                            log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative KO (Recurrence '.$Recurrence.')');
                            return false;
                        }
                        log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative OK (Recurrence '.$Recurrence.')');
                    }
                    else{
                        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Login() KO'.$Recurrence.')');
                        return false;
                    }
                }
                else
                    return false; // Retour KO si trop de tentative récursive
            }
            else{
                /// Réponse OK - Décodage de la réponse
                $aRep = json_decode($result, true);
            }
        }
      
        log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- http OK (Recurrence '.$Recurrence.')');
      
        ///Décodage de la réponse
        //$aRep = json_decode($result, true);
        //if(isset($aRep['error_message'])) {
        //    throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        // }
      /*
        if( $aRep['error_code'] == '9004' || $aRep['error_code'] == '9006' ) {
            // erreur token invalide, alors on va en chercher un nouveau
            if( heatzy::Login() ){
              
                $UserToken = config::byKey('UserToken','heatzy','none');
                
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Login() OK - Nouveau Token ('.$UserToken.')');
              
                /// Parametres cUrl
                $params = array(
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                        'X-Gizwits-User-token: '.$UserToken
                    ),
                    CURLOPT_URL => self::$UrlGizwits.'/app/devdata/'.$Did.'/latest',
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout )
                );
              
                /// Initialisation de la ressources curl
                $gizwits = curl_init();
                if ($gizwits === false)
                    return false;
                /// Configuration des options
                curl_setopt_array($gizwits, $params);

                /// Excute la requete
                $result = curl_exec($gizwits);

                /// Test le code retour http
                $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

                /// Ferme la connexion
                curl_close($gizwits);

                if( $httpcode != 200 && $httpcode != 400 ){
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
                    return false;
                }

                ///Décodage de la réponse
                $aRep = json_decode($result, true);
            }
            else{
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Login() KO');
                return false;
            }
        }*/
        
        if( self::$DebugExport ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($params, true));
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        }
        
        return $aRep;
    }
    
    /*****/
    /**
     * @brief Fonction qui permet de récuperer le detail du devbice
     *
     * @param $Did         Identifiant du module dans le cloud
     *
     * @return Un tableau associatif ou false en cas d'erreur
     */
//class HttpGizwits
    public static function GetDeviceDetails($UserToken, $Did) {
    
        if(empty($Did)){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': argument invalide');
            return false;
        }
    
        /// Parametres cUrl
        $params = array(
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/devices/'.$Did,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => config::byKey('Timeout_value','heatzy',self::$Default_Timeout )
        );
        
        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
            return false;
        
        /// Configuration des options
        curl_setopt_array($gizwits, $params);

        /// Excute la requete
        $result = curl_exec($gizwits);

        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode);
            return false;
        }
        
        /// Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.var_export($aRep, true));
        return $aRep;
    }
  
  
  //class HttpGizwits
    public static function SetStatsHeatzy( $data ) {

        //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.': $data='.$data );
        /// Parametres cUrl
        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array(
                    'Accept: application/json'
            ),
            CURLOPT_URL => 'http://bodbod.whf.bz/insert.php',
          	//CURLOPT_URL => 'http://192.168.3.118/insert.php',
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            //CURLOPT_CONNECTTIMEOUT => self::$ConnectTimeout,
            CURLOPT_TIMEOUT => 100,
            CURLOPT_POSTFIELDS => $data,
        );

        /// Initialisation de la ressources curl
        $gizwits = curl_init();
        if ($gizwits === false)
          return false;

        /// Configuration des options
        curl_setopt_array($gizwits, $params);
    
        /// Excute la requete
        $result = curl_exec($gizwits);
    
        /// Test le code retour http
        $httpcode = curl_getinfo($gizwits, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($gizwits);

        if( $httpcode != 200 ){ // Si erreur technique
            log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode.' - timeout '.self::$Default_Timeout.' $result='.$result );
        }
        else{ // Le serveur a répondu
            log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.': $httpcode='.$httpcode );
            log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.': $result='.$result );
        }
        
        return true;
    }
  
}