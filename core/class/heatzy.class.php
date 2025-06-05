<?php

/* This file is part of Jeedom.
 *
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

/**
 *
 * @brief Class HttpGizwits de communication avec le serveur Gizwits 
 *
 */
class HttpGizwits {
    /*     * *************************Attributs****************************** */
    public static $HeatzyAppId = "c70a66ff039d41b4a220e198b0fcc8b3";
    public static $UrlGizwits = "https://euapi.gizwits.com";

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
    public static function Login($User, $Passwd, $Lang='en') {
     
        if(empty($User) || empty($Passwd)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
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
            CURLOPT_TIMEOUT => 10,
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }
        
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.':'.var_export($aRep, true));
        return $aRep;
    }
  
   /**
     * @brief Fonction qui permet de récuperer la liste des devices did
     * 
     * @param $UserToken   Token utilisateur d'acces au cloud
     * 
     * @return Un tableau associatif ou false en cas d'erreur
     */
    public static function GetProduitInfo($ProductKey) {
        
        if(empty($ProductKey)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
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
            CURLOPT_TIMEOUT => 10
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.':'.var_export($aRep, true));
        return $aRep;
    }
  
    /**
     * @brief Fonction qui permet de récuperer la liste des devices did
     * 
     * @param $UserToken   Token utilisateur d'acces au cloud
     * 
     * @return Un tableau associatif ou false en cas d'erreur
     */
    public static function Bindings($UserToken) {
        
        if(empty($UserToken)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
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
            CURLOPT_TIMEOUT => 10
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.':'.var_export($aRep, true));
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
    public static function GetSchedulerList($UserToken, $Did, $Skip = 0, $Limit = 20) {
    
        if(empty($UserToken) || empty($Did)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
            return false;
        }
    
        log::add('heatzy', 'debug',  __METHOD__.':skip '.$Skip);
        /// Parametres cUrl
        $params = array(
            CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                    'X-Gizwits-User-token: '.$UserToken
            ),
            CURLOPT_URL => self::$UrlGizwits.'/app/devices/'.$Did.'/scheduler?limit='.$Limit.'&amp;skip='.$Skip,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('DID : ', __FILE__) . $Did.' '.__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        //log::add('heatzy', 'debug',  __METHOD__.':'.var_export($aRep, true));
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
    public static function SetScheduler($UserToken, $Did, $Id, $Param) {
    
        if(empty($UserToken) || empty($Did) || empty($Id) || empty($Param)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
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
            CURLOPT_TIMEOUT => 10,
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }
        
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
    
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        //log::add('heatzy', 'debug',  __METHOD__.':'.var_export($aRep, true));
        return $aRep;
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
    public static function SetBindingInformation($UserToken, $Did, $DevAlias) {
    
        if(empty($UserToken) || empty($Did) || empty($DevAlias)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
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
            CURLOPT_TIMEOUT => 10,
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.':'.var_export($aRep, true));
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
    public static function SetConsigne($UserToken, $Did, $Consigne) {
        
        if(empty($UserToken) || empty($Did) || empty($Consigne)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
            return false;
        }

        /// Preparation de la requete : json
        $data = json_encode( $Consigne ) ;

        log::add('heatzy', 'debug',  __METHOD__.':'.var_export($data, true));
        
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
            CURLOPT_TIMEOUT => 10,
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
     //   if(isset($aRep['error_message'])) {
     //       throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
     //   }
        return $aRep;
    }
    /**
     * @brief Fonction qui permet de récuperer le dernier status du device did
     * 
     * @param $Did         Identifiant du module dans le cloud
     * 
     * @return Un tableau associatif ou false en cas d'erreur
     */
    public static function GetConsigne($UserToken, $Did) {
        
        if(empty($Did)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
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
            CURLOPT_TIMEOUT => 10
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }
      
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        //if(isset($aRep['error_message'])) {
        //    throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        // }
        log::add('heatzy', 'debug',  __METHOD__.':'.var_export($params, true));
        log::add('heatzy', 'debug',  __METHOD__.':'.var_export($aRep, true));
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
    public static function GetDeviceDetails($UserToken, $Did) {
    
        if(empty($Did)){
            log::add('heatzy', 'debug',  __METHOD__.': argument invalide');
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
            CURLOPT_TIMEOUT => 10
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

        if( $httpcode == 500 ){
            log::add('heatzy', 'debug',  __METHOD__.': erreur 500');
            return false;
        }
        
        /// Décodage de la réponse
        $aRep = json_decode($result, true);
        if(isset($aRep['error_message'])) {
            throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        }
        log::add('heatzy', 'debug',  __METHOD__.':'.var_export($aRep, true));
        return $aRep;
    }
}

/**
 * 
 * @brief Class heatzy                    
 *
 */
class heatzy extends eqLogic {
    /*     * *************************Attributs****************************** */
  //  public static $_widgetPossibility = array('custom' => true, 'custom::layout' => false);
    
    public static $_widgetPossibility = array('custom' => array(
      'visibility' => true,
      'displayName' => array('dashboard' => true, 'view' => true),
      'optionalParameters' => true,
));
  
    /**
     * @var $_HeatzyMode Différent mode de fonctionnement du module heatzy
     *      /!\ Les clefs des valeurs du tableau correspond
     *      aux valeurs supportées par les devices
     */
    public static $_HeatzyMode = array('Confort', 'Eco', 'HorsGel', 'Off','Confort-1','Confort-2');
    
    /**
     * @brief Fonction qui permet de tirer un nouveau token utilisateur
     */
    public static function Login() {

        $email = config::byKey('email', 'heatzy', '');
        $password = config::byKey('password', 'heatzy', '');
        
        /// Login
        $aResult = HttpGizwits::Login($email, $password );
        if ($aResult === false) {
            log::add('heatzy', 'error', __METHOD__.' : impossible de se connecter a: '.HttpGizwits::$UrlGizwits);
            return false;
        }
        log::add('heatzy', 'debug',  '$aResult :'.var_export($aResult, true));
         
        $TokenExpire = date('Y-m-d H:i:s', $aResult['expire_at']);
        $UserToken = $aResult['token'];
        
        if( config::byKey('UserToken', 'heatzy', '') != $UserToken)
            message::add("Heatzy", 'Génération du token heatzy AVANT='.config::byKey('ExpireToken', 'heatzy', '').'/'.config::byKey('UserToken', 'heatzy', '').' -> '.$TokenExpire.'/'.$UserToken);
        //else
        //    message::add("Heatzy", 'Génération du token heatzy -> Pas de changement');
        
        config::save('UserToken', $UserToken, 'heatzy'); /// => Sauvegarde du token utilisateur
        config::save('ExpireToken', $TokenExpire, 'heatzy'); /// => Sauvegarde de l'expiration du token
        
        /// Prepare le prochain cron
        $cron = cron::byClassAndFunction('heatzy', 'Login');
        if (!is_object($cron)) {
            $cron = new cron();
            $cron->setClass('heatzy');
            $cron->setFunction('Login');
            $cron->setLastRun(date('Y-m-d H:i:s'));
        }
        
        $nextLogin = date('i H d m * Y', strtotime($TokenExpire." - 1 day"));
        log::add('heatzy', 'debug',  'cron prochain Login :'.$nextLogin);
        $cron->setSchedule($nextLogin);
        $cron->save();
    }
    
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
    public static function Synchronize() {
        /// Login + creation du cron
        heatzy::Login();
        $UserToken = config::byKey('UserToken','heatzy','none');
      
        /// Bindings
        $aDevices = HttpGizwits::Bindings($UserToken);
        if($aDevices === false) {
            log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
            return false;
        }
        
        log::add('heatzy', 'debug',  '$aDevice :'.var_export($aDevices, true));
        foreach ($aDevices ['devices'] as $DeviceNum => $aDevice) {
            
            $eqLogic = self::byLogicalId( $aDevice['did'] , 'heatzy', false);
            if (! is_object($eqLogic)) {   /// Creation des dids inexistants
                $eqLogic = new heatzy();
            }
            $eqLogic->setEqType_name('heatzy');
            $eqLogic->setLogicalId($aDevice['did']);
            $eqLogic->setIsVisible(1);
            if($aDevice['is_disabled'] === 'false')
                $eqLogic->setIsEnable(0);
            else
                $eqLogic->setIsEnable(1);
            
            if (empty($aDevice['dev_alias']))
                $eqLogic->setName(strtoupper($aDevice['mac']));
            else
                $eqLogic->setName($aDevice['dev_alias']);
            
            if(isset($aDevice['mac']))
                $eqLogic->setConfiguration('mac',implode(':',str_split($aDevice['mac'], 2)));

            /// Retourne les informations sur le produit
            $aProductInfo = HttpGizwits::GetProduitInfo($aDevice['product_key']) ;
            
            if (isset ($aProductInfo['name']))
                $eqLogic->setConfiguration('product',$aProductInfo['name']);
            if (isset ($aProductInfo['product_key']))
		        		$eqLogic->setConfiguration('product_key',$aProductInfo['product_key']);

            if  ( strcmp( $aProductInfo['name'] , "INEA" ) === 0 )
                 $eqLogic->setConfiguration('heatzytype','flam');
	        else if ( strncmp ( $aProductInfo['name'] , "Flam" , 4 ) === 0 )
                 $eqLogic->setConfiguration('heatzytype','flam');
            else
                 $eqLogic->setConfiguration('heatzytype','pilote');
          
            /// Si connecté ou pas
            if(isset($aDevice['is_online'])) {
                if($aDevice['is_online'] == 'true')
                    $eqLogic->setStatus('timeout','0');
                else
                    $eqLogic->setStatus('timeout','1');
            }            
            $eqLogic->save();
                          
            if ($eqLogic->getIsEnable() == 1) { /// mise à jour du did
                 $eqLogic->updateHeatzyDid($UserToken,$aStatus);
            }
        }
        
        log::add('heatzy', 'info', 'Synchronistation de '. count($aDevices ['devices']).' module(s) Heatzy');
        //message::add("Heatzy", 'Synchronistation de '. count($aDevices ['devices']).' module(s) Heatzy');
        return count($aDevices ['devices']);    
    }
    /**
     * @brief Fonction de mise à jour du device did
     */
    public function updateHeatzyDid($UserToken, $aDevice = array()) {
      
        if(empty($aDevice)) {
            /// Lecture de l'etat
            $UserToken = config::byKey('UserToken','heatzy','none');
            $aDevice = HttpGizwits::GetConsigne($UserToken, $this->getLogicalId());
            if($aDevice === false) {
                log::add('heatzy', 'warning',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                $this->setStatus('timeout','1');
                $this->save();
                return false;
            }
             ///// --- TEST ----
            else if(isset($aDevice['error_message']) && isset($aDevice['error_code'])) {
                if($aDevice['error_code'] == '9004') {
                    log::add('heatzy', 'error',  __METHOD__.' : '.$aDevice['error_code'].' '.$aDevice['error_message']);
                    $Nb = $this->Synchronize(); //$Nb = $eqLogic->Synchronize();
                    if ($Nb == false) {
                        log::add('heatzy', 'error',  __METHOD__.' : erreur synchronisation');
                        return false;
                }
                else{
                    log::add('heatzy', 'info',  __METHOD__.' : '.$Nb. 'module(s) synchronise(s)');
                    $UserToken = config::byKey('UserToken','heatzy','none');
                    $aDevice = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
                    if(isset($aDevice['error_message']) && isset($aDevice['error_code'])) {
                      log::add('heatzy', 'error',  __METHOD__.' : '.$aDevice['error_code'].' - '.$aDevice['error_message']);
                      return false;
                    }
                }
            }
            else {
                log::add('heatzy', 'error',  __METHOD__.' : '.$aDevice['error_code'].' - '.$aDevice['error_message']);
                return false;
            }
          }
          ///// --- FIN TEST ----
        }
      
        /// Mise à jour de la derniere communication
		if(isset($aDevice['updated_at']) && $aDevice['updated_at'] != 0 ) {
            $this->setStatus('timeout','0');
            log::add('heatzy', 'debug',  'lastCommunication :'.date('Y-m-d H:i:s', $aDevice['updated_at']));
            $this->setConfiguration('lastCommunication', date('Y-m-d H:i:s', $aDevice['updated_at']));
        }
      
		// Modes de chauffe
		// Note : Théoriquement pilote_pro doit être lu avec cur_mode (mais le retour contient quand même mode
        if(isset($aDevice['attr']['mode'])) {

            // Créer les commandes en fonction du contenu de la réponse
            $this->CheckAndCreateCmd($aDevice) ;
          
            if( $aDevice['attr']['mode'] == 'cft' ) {        /// Confort
                $KeyMode = 'Confort';
            }
            else if( $aDevice['attr']['mode'] == 'cft1' ) {  /// Eco
                $KeyMode = 'Confort-1';
            }
            else if( $aDevice['attr']['mode'] == 'cft2' ) {  /// Eco
                $KeyMode = 'Confort-2';
            }
            else if( $aDevice['attr']['mode'] == 'eco' ) {   /// Eco
                $KeyMode = 'Eco';
            }
            else if( $aDevice['attr']['mode'] == 'fro' ) {   /// HorsGel
                $KeyMode = 'HorsGel';
            }
            else if( $aDevice['attr']['mode'] == 'stop' ) {  /// Off
                $KeyMode = 'Off';
            }
            else {       /// Premiere version du module pilote
                $mode1 = $mode2 = 0;
                $mode1=ord(substr($aDevice['attr']['mode'], 1,1));
                $mode2=ord(substr($aDevice['attr']['mode'], 2,1));
              
                if($mode1 == 136 && $mode2 == 146) {  /// Confort
                    $KeyMode = 'Confort';
                }
                else if($mode1 == 187 && $mode2 == 143) { /// Eco
                    $KeyMode = 'Eco';
                }
                else if($mode1 == 167 && $mode2 == 163) { /// HorsGel
                    $KeyMode = 'HorsGel';
                }
                else if($mode1 == 129 && $mode2 == 156) { /// Off
                    $KeyMode = 'Off';
                }
                else {
                    log::add('heatzy', 'debug',  __METHOD__.': '.$this->getLogicalId().' non connecte');
                    $this->setStatus('timeout','1');
                    $this->save(); /// Enregistre les info
                    return false;
                }
            }
          
			// Consigne de température du mode éco (eco_temp ou eco_tempH+eco_tempL selon type de module)
			// L : La température est exprimée en dixièmes de degrés
			// H : La température est exprimée en dizaines de degrés
			if( isset ($aDevice['attr']['eco_temp']) )
				$this->checkAndUpdateCmd('eco_temp', floatval( $aDevice['attr']['eco_temp'] / 10 ) );
			if( isset ($aDevice['attr']['eco_tempH']) && isset ($aDevice['attr']['eco_tempL']) )
				$this->checkAndUpdateCmd('eco_temp', floatval( bindec(str_pad(decbin($aDevice['attr']['eco_tempH']),  8, "0", STR_PAD_LEFT).str_pad(decbin($aDevice['attr']['eco_tempL']),  8, "0", STR_PAD_LEFT))) / 10 );

			// Consigne de température du mode confort (cft_temp ou cft_tempH+cft_tempL selon type de module)
			// L : La température est exprimée en dixièmes de degrés
			// H : La température est exprimée en dizaines de degrés
			if( isset ($aDevice['attr']['cft_temp']) )
				$this->checkAndUpdateCmd('cft_temp', floatval( $aDevice['attr']['cft_temp'] / 10 ) );
			if( isset ($aDevice['attr']['cft_tempH']) && isset ($aDevice['attr']['cft_tempL']) )
				$this->checkAndUpdateCmd('cft_temp', floatval( bindec(str_pad(decbin($aDevice['attr']['cft_tempH']),  8, "0", STR_PAD_LEFT).str_pad(decbin($aDevice['attr']['cft_tempL']),  8, "0", STR_PAD_LEFT))) / 10 );

			// cur_temp : Température de la pièce, lue par le capteur. La température est exprimée en dixièmes de degrés. (cur_temp ou cur_tempH+cur_tempL selon type de module)
			// L : La température est exprimée en dixièmes de degrés
			// H : La température est exprimée en dizaines de degrés
			if( isset ($aDevice['attr']['cur_temp']) )
				$this->checkAndUpdateCmd('cur_temp', floatval( $aDevice['attr']['cur_temp'] / 10 ) );
			if( isset ($aDevice['attr']['cur_tempH']) && isset ($aDevice['attr']['cur_tempL']) )
				$this->checkAndUpdateCmd('cur_temp', floatval( bindec(str_pad(decbin($aDevice['attr']['cur_tempH']),  8, "0", STR_PAD_LEFT).str_pad(decbin($aDevice['attr']['cur_tempL']),  8, "0", STR_PAD_LEFT))) / 10 );

			// Taux d’humidité de l’air dans la pièce (%). 
			if( isset ($aDevice['attr']['cur_humi']) )
				$this->checkAndUpdateCmd('cur_humi', $aDevice['attr']['cur_humi'] );

			// Allumage du radiateur
			if( isset ($aDevice['attr']['on_off']) )
				$this->checkAndUpdateCmd('plugzy', $aDevice['attr']['on_off'] );

			// Activation du mode programmation
			if( isset ($aDevice['attr']['timer_switch']) )
				$this->checkAndUpdateCmd('etatprog', $aDevice['attr']['timer_switch'] );

			// Activation du verrouillage (lock_switch)
			if( isset ($aDevice['attr']['lock_switch']) )
				$this->checkAndUpdateCmd('etatlock', $aDevice['attr']['lock_switch'] );

			// Activation du verrouillage (LOCK_C)
			if( isset ($aDevice['attr']['LOCK_C']) )//Lock_C_State
				$this->checkAndUpdateCmd('Lock_C_State', $aDevice['attr']['LOCK_C'] );
				
			// Activation de la détection de fenêtre ouverte 
			if( isset ($aDevice['attr']['window_switch']) )
				$this->checkAndUpdateCmd('WindowSwitch', $aDevice['attr']['window_switch'] );
        }
        else {                                             
			log::add('heatzy', 'debug',  __METHOD__.': '.$this->getLogicalId().' non connecte');
			$this->setStatus('timeout','1');
			$this->save(); /// Enregistre les info
			return false;
        }
		
        $this->save(); /// Enregistre les info
        /// Recherche la valeur de la clef du mode courant
        log::add('heatzy', 'debug',  $this->getLogicalId().' : Mode '.$KeyMode);
        $aKeyVal = array_keys(self::$_HeatzyMode, $KeyMode);
        $this->checkAndUpdateCmd('EtatConsigne', $aKeyVal[0]);
        $this->checkAndUpdateCmd('mode', $KeyMode);
        return true;
    }
  
      /**
     * @brief Fonction qui permet de savoir si le module gère 4 ou 6 ordres
     * 
     * @param tableau retour API
     */
  
    public function VerifNbOrdres($aDevice) {
        // Par test, l'envoi d'une consigne cft1 ou cft2 sur un module 4 ordres ne renvoit pas d'erreur (mais pas prise en compte)
        $NbOrdres = 0 ;
        if( $aDevice['attr']['mode'] == 'cft1' || $aDevice['attr']['mode'] == 'cft2' ){
            // SI l'ordre est cft1 ou cfrt2, c'est forcement un module 6 ordres
            $NbOrdres = 6 ;
        }
        else{
            // On tente de mettre le 6e ordre
            //    - Si OK : 6 ordres
            //    - Si KO : 4 ordres
            $Consigne = array( 'attrs' => array ( 'mode' => 'cft2' )  );
            $UserToken = config::byKey('UserToken','heatzy','none');
            // Appel API pour SET confort-2
            $ResultSet = HttpGizwits::SetConsigne($UserToken, $this->getLogicalId(), $Consigne);
            if( $ResultSet['error_code'] == ''){
                sleep(3); // Attente 3sec
                // Appel API pour analyser le changement ou non de consigne
                $ResultGet = HttpGizwits::GetConsigne($UserToken, $this->getLogicalId() ) ;
                if( $ResultGet['error_code'] == ''){
                    if( $ResultGet['attr']['mode'] == 'cft2'){
                        // L'ordre a bien été modifié (donc 6 ordres)
                        $NbOrdres = 6 ;
                    }
                    else if( $ResultGet['attr']['mode'] == 'cft' ){
                        // L'ordre n'a pas été modifié (donc 4 ordres)
                        $NbOrdres = 4 ;
                    }
                }
                else
                    log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code GET='.$ResultSet['error_code']);
            }
            else
                log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code SET='.$ResultSet['error_code']);
            // On remet l'ordre initial
            sleep(1);
            $Consigne = array( 'attrs' => array ( 'mode' => $aDevice['attr']['mode'] )  );
            $ResultSet = HttpGizwits::SetConsigne($UserToken, $this->getLogicalId(), $Consigne);
            if( $ResultSet['error_code'] != '' )
                log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code SET2='.$ResultSet['error_code']);
        }

        log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' Nombre d ordre='.$NbOrdres);
      	return $NbOrdres ;
    }
  
    /**
     * @brief Fonction qui permet de créer les commandes en fonction du retour de l'API
     * 
     * @param tableau retour API
     */
  
    public function CheckAndCreateCmd($aDevice) {
        
        
        if( isset($aDevice['attr']['mode']) && !is_object( $this->getCmd(null,'EtatConsigne')) ){

            // Verifie si module 4 ou 6 ordres
        	$NbOrdres = $this->VerifNbOrdres($aDevice) ;
          
          	if( $NbOrdres > 0 ){
                foreach (self::$_HeatzyMode as $Key => $Mode ) {
                    if($Key < $NbOrdres){ // On arrêt la création selon le nombre d'ordres du module
                        /// Creation de la commande action $Mode : $Key
                        $cmd = $this->getCmd(null, $Mode);
                        if (!is_object($cmd)) {
                            log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' creation commande :'.$Key.'=>'.$Mode);
                            $cmd = new heatzyCmd();
                            $cmd->setLogicalId($Mode);
                            $cmd->setIsVisible(1);
                            $cmd->setName(__($Mode, __FILE__));
                            $cmd->setType('action');
                            $cmd->setSubType('other');
                            $cmd->setConfiguration('infoName', 'EtatConsigne');
                            $cmd->setEqLogic_id($this->getId());
                            $cmd->setIsHistorized(0);
                            $cmd->setIsVisible(1);
                            $cmd->save();
                        }
                        else{
                            $cmd->setConfiguration('infoName', 'EtatConsigne');
                        }
                    }
                } // for

                /// Creation de la commande info Etat numeric
                $etat = $this->getCmd(null, 'EtatConsigne');
                if (!is_object($etat)) {
                    $etat = new heatzyCmd();
                    $etat->setName(__('Etat Consigne', __FILE__));
                    $etat->setLogicalId('EtatConsigne');
                    $etat->setType('info');
                    $etat->setSubType('numeric');
                    $etat->setEqLogic_id($this->getId());
                    $etat->setIsHistorized(0);
                    $etat->setIsVisible(1);
                    $etat->save();
                }
                
                /// Creation de la commande info mode (correspond à l'état sous forme d'une chaine de carcateres)
                $mode = $this->getCmd(null, 'mode');
                if (!is_object($mode)) {
                    $mode = new heatzyCmd();
                    $mode->setName(__('Mode', __FILE__));
                    $mode->setLogicalId('mode');
                    $mode->setType('info');
                    $mode->setSubType('string');
                    $mode->setEqLogic_id($this->getId());
                    $mode->setIsHistorized(0);
                    $mode->setIsVisible(1);
                    $mode->save();
                }
            } // if nb ordre > 0
          
        } // if mode 
        
		if( isset ($aDevice['attr']['cft_temp']) || isset ($aDevice['attr']['cft_tempH']) ){
      			/// Creation de la commande info de la temperature de confort
			$CftTemp = $this->getCmd(null, 'cft_temp'); 
			if (!is_object($CftTemp)) {
				$CftTemp = new heatzyCmd();
				$CftTemp->setName(__('Temp. confort', __FILE__));
				$CftTemp->setLogicalId('cft_temp');
				$CftTemp->setType('info');
				$CftTemp->setUnite('°C');
				$CftTemp->setSubType('numeric');
				$CftTemp->setEqLogic_id($this->getId());
				$CftTemp->setIsHistorized(0);
				$CftTemp->setIsVisible(1);
				$CftTemp->save();
			}
        } // cft_temp - cft_tempH
          
     	if( isset ($aDevice['attr']['eco_temp']) || isset ($aDevice['attr']['eco_tempH']) ){
			/// Creation de la commande info de la temperature eco
			$EcoTemp = $this->getCmd(null, 'eco_temp'); 
			if (!is_object($EcoTemp)) {
				$EcoTemp = new heatzyCmd();
				$EcoTemp->setName(__('Temp. eco', __FILE__));
				$EcoTemp->setLogicalId('eco_temp');
				$EcoTemp->setType('info');
				$EcoTemp->setUnite('°C');
				$EcoTemp->setSubType('numeric');
				$EcoTemp->setEqLogic_id($this->getId());
				$EcoTemp->setIsHistorized(0);
				$EcoTemp->setIsVisible(1);
				$EcoTemp->save();
			}
        } // if eco_temp - eco_tempH
          
        if( isset ($aDevice['attr']['cur_temp']) || isset ($aDevice['attr']['cur_tempH']) ){
			/// Creation de la commande info de la temperature courante
			$CurTemp = $this->getCmd(null, 'cur_temp'); 
			if (!is_object($CurTemp)) {
				$CurTemp = new heatzyCmd();
				$CurTemp->setName(__('Temperature', __FILE__));
				$CurTemp->setLogicalId('cur_temp');
				$CurTemp->setType('info');
				$CurTemp->setUnite('°C');
				$CurTemp->setSubType('numeric');
				$CurTemp->setEqLogic_id($this->getId());
				$CurTemp->setIsHistorized(0);
				$CurTemp->setIsVisible(1);
				$CurTemp->save();
			}
        } // if cur_temp - cur_tempH
            
        if( isset ($aDevice['attr']['timer_switch']) ){
            // Programmation On/Off
            $cmd = $this->getCmd(null, 'ProgOn');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('ProgOn');
                $cmd->setIsVisible(1);
                $cmd->setName(__('Activer Programmation', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'etatprog');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
            else{
                $cmd->setConfiguration('infoName', 'etatprog');
            }
                
            $cmd = $this->getCmd(null, 'ProgOff');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('ProgOff');
                $cmd->setIsVisible(1);
                $cmd->setName(__('Désactiver Programmation', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'etatprog');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
              else{
                $cmd->setConfiguration('infoName', 'etatprog');
            }
                
            /// Creation de la commande info etatprog binaire
            $etat = $this->getCmd(null, 'etatprog');
            if (!is_object($etat)) {
                $etat = new heatzyCmd();
                $etat->setName(__('Etat programmation', __FILE__));
                $etat->setLogicalId('etatprog');
                $etat->setType('info');
                $etat->setSubType('binary');
                $etat->setEqLogic_id($this->getId());
                $etat->setIsHistorized(0);
                $etat->setIsVisible(1);
                $etat->save();
            }
        } // if timer switch
      
        if( isset ($aDevice['attr']['lock_switch']) ){
            // Verouillage-lock On/Off
            $cmd = $this->getCmd(null, 'LockOn');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('LockOn');
                $cmd->setIsVisible(1);
                $cmd->setName(__('Activer Verrouillage', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'etatlock');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
              else{
                $cmd->setConfiguration('infoName', 'etatlock');
            }
                
            $cmd = $this->getCmd(null, 'LockOff');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('LockOff');
                $cmd->setIsVisible(1);
                $cmd->setName(__('Désactiver Verrouillage', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'etatlock');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
              else{
                $cmd->setConfiguration('infoName', 'etatlock');
            }
                
            /// Creation de la commande info etatlock binaire
            $etat = $this->getCmd(null, 'etatlock');
            if (!is_object($etat)) {
                $etat = new heatzyCmd();
                $etat->setName(__('Etat Verrouillage', __FILE__));
                $etat->setLogicalId('etatlock');
                $etat->setType('info');
                $etat->setSubType('binary');
                $etat->setEqLogic_id($this->getId());
                $etat->setIsHistorized(0);
                $etat->setIsVisible(1);
                $etat->save();
            }
        } // if lock_switch

        if( isset ($aDevice['attr']['LOCK_C']) ){
            // Verouillage-lock On/Off
            $cmd = $this->getCmd(null, 'Lock_C_On');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('Lock_C_On');
                $cmd->setIsVisible(1);
                $cmd->setName(__('Activer Verrouillage', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'Lock_C_State');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
              else{
                $cmd->setConfiguration('infoName', 'Lock_C_State');
            }
                
            $cmd = $this->getCmd(null, 'Lock_C_Off');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('Lock_C_Off');
                $cmd->setIsVisible(1);
                $cmd->setName(__('Désactiver Verrouillage', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'Lock_C_State');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
              else{
                $cmd->setConfiguration('infoName', 'Lock_C_State');
            }
                
            /// Creation de la commande info Lock_C_State binaire
            $etat = $this->getCmd(null, 'Lock_C_State');
            if (!is_object($etat)) {
                $etat = new heatzyCmd();
                $etat->setName(__('Etat Verrouillage', __FILE__));
                $etat->setLogicalId('Lock_C_State');
                $etat->setType('info');
                $etat->setSubType('binary');
                $etat->setEqLogic_id($this->getId());
                $etat->setIsHistorized(0);
                $etat->setIsVisible(1);
                $etat->save();
            }
        } // if lock_switch
      
        if( isset ($aDevice['attr']['cur_humi']) ){
            /// Creation de la commande humidité du pilote_pro
            $CurHumi = $this->getCmd(null, 'cur_humi'); 
            if (!is_object($CurHumi)) {
              $CurHumi = new heatzyCmd();
              $CurHumi->setName(__('Taux Humidité', __FILE__));
              $CurHumi->setLogicalId('cur_humi');
              $CurHumi->setType('info');
              $CurHumi->setUnite('%');
              $CurHumi->setSubType('numeric');
              $CurHumi->setEqLogic_id($this->getId());
              $CurHumi->setIsHistorized(0);
              $CurHumi->setIsVisible(1);
              $CurHumi->save();
            }
        } // cur_humi
      
        if( isset ($aDevice['attr']['window_switch']) ){
            /// Creation de la commande Activation de la détection de fenêtre ouverte du pilote_pro
            $CurWindow = $this->getCmd(null, 'WindowSwitch'); 
            if (!is_object($CurWindow)) {
                $CurWindow = new heatzyCmd();
                $CurWindow->setName(__('Etat fenêtre ouverte', __FILE__));
                $CurWindow->setLogicalId('WindowSwitch');
                $CurWindow->setType('info');
                $CurWindow->setSubType('binary');
                $CurWindow->setEqLogic_id($this->getId());
                $CurWindow->setIsHistorized(0);
                $CurWindow->setIsVisible(0);
                $CurWindow->save();
            }
            // window_switch On/Off
            $cmd = $this->getCmd(null, 'WindowSwitchOn');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('WindowSwitchOn');
                $cmd->setIsVisible(1);
                $cmd->setName(__('Activer Fenetre Ouverte', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'WindowSwitch');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
            else{
                $cmd->setConfiguration('infoName', 'WindowSwitch');
            }		        
            $cmd = $this->getCmd(null, 'WindowSwitchOff');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('WindowSwitchOff');
                $cmd->setIsVisible(1);
                $cmd->setName(__('Désactiver Fenetre Ouverte', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'WindowSwitch');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
            else{
                $cmd->setConfiguration('infoName', 'WindowSwitch');
            }     
        } // if window_switch    
      
        if( isset ($aDevice['attr']['on_off']) ){
			/// Creation de la commande info du plugzy
			$Plugzy = $this->getCmd(null, 'plugzy'); 
			if (!is_object($Plugzy)) {
				$Plugzy = new heatzyCmd();
				$Plugzy->setName(__('Plugzy', __FILE__));
				$Plugzy->setLogicalId('plugzy');
				$Plugzy->setType('info');
				$Plugzy->setSubType('binary');
				$Plugzy->setEqLogic_id($this->getId());
				$Plugzy->setIsHistorized(0);
				$Plugzy->setIsVisible(1);
				$Plugzy->save();
			}
	          
			/// Creation de la commande plugzy on
			$cmd = $this->getCmd(null, 'plugzyon');
			if (!is_object($cmd)) {
				$cmd = new heatzyCmd();
				$cmd->setLogicalId('plugzyon');
				$cmd->setIsVisible(1);
				$cmd->setName(__('Plugzy ON', __FILE__));
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setConfiguration('infoName', 'plugzy');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setIsHistorized(0);
				$cmd->setIsVisible(1);
				$cmd->save();
			}
			  
			/// Creation de la commande plugzy off
			$cmd = $this->getCmd(null, 'plugzyoff');
			if (!is_object($cmd)) {
				$cmd = new heatzyCmd();
				$cmd->setLogicalId('plugzyoff');
				$cmd->setIsVisible(1);
				$cmd->setName(__('Plugzy OFF', __FILE__));
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setConfiguration('infoName', 'plugzy');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setIsHistorized(0);
				$cmd->setIsVisible(1);
				$cmd->save();
			}
        } // if on_off
    }
    
    /**
     * @brief Fonction qui permet d'activer/désactiver la programmation
     * 
     * @param $EtatProg        true ou false
     */

    public function GestProg($EtatProg) {
        $Skip = 0;            /// Nombre d'element sauté
        $Limit = 100;        /// Limite du nombre de tache
        
        /// Lecture du token
        $UserToken = config::byKey('UserToken','heatzy','none');
        
        do {
            /// Lecture des taches par pas de $Limit
            $aTasks = HttpGizwits::GetSchedulerList($UserToken, $this->getLogicalId(), $Skip, $Limit);
            
            /// Boucle de mise à jour des taches
            foreach ($aTasks as $TaskNum => $aTask) {

                /// Sauvegarde de l'Id
                $Id = $aTask['id'];

                /// On envoie le minimum => suppression des données inutiles
                unset($aTask['remark']);
                unset($aTask['end_date']);
                unset($aTask['did']);
                unset($aTask['created_at']);
                unset($aTask['enabled']);
                unset($aTask['updated_at']);
                unset($aTask['product_key']);
                unset($aTask['days']);
                unset($aTask['raw']);
                unset($aTask['start_date']);
                unset($aTask['date']);
                unset($aTask['scene_id']);
                unset($aTask['group_id']);
                unset($aTask['id']);
                $aTask['enabled']=$EtatProg;
                
                /// Mise a jour de la tache
                $aTaskResul = HttpGizwits::SetScheduler($UserToken, $this->getLogicalId(), $Id, $aTask);
                if ($aTaskResul === false ) {
                    throw new Exception(__('Erreur : mise à jour de la tache', __FILE__));
                }
                if ($aTaskResul['id'] != $Id) {
                    throw new Exception(__('Erreur : identifiant de tache invalide', __FILE__));
                }
            }
            $Skip += count($aTasks);
        } while(!empty($aTasks) && count($aTasks) >= $Limit);
        
        log::add('heatzy', 'debug',   $this->getLogicalId() . ' : '.$Skip.' taches mise a jour');
    }

    /**
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
     * synchronisation
     */
      public static function cron() {
          
          foreach (eqLogic::byType('heatzy') as $heatzy) {
              if($heatzy->getIsEnable() == 1 ){ /// Execute la commande refresh des modules activés
                  
                  $Cmd =  heatzyCmd::byEqLogicIdCmdName($heatzy->getId(), 'Rafraichir' );
                  if (! is_object($Cmd)) {
                      log::add('heatzy', 'error',  ' La commande :refresh n\'a pas été trouvé' );
                      throw new Exception(__(' La commande refresh n\'a pas été trouvé ', __FILE__));
                  }
                  $Cmd->execCmd($_options);
                  
                  $mc = cache::byKey('heatzyWidgetmobile' . $heatzy->getId());
                  $mc->remove();
                  $mc = cache::byKey('heatzyWidgetdashboard' . $heatzy->getId());
                  $mc->remove();
                  
                  $heatzy->toHtml('mobile');
                  $heatzy->toHtml('dashboard');
                  $heatzy->refreshWidget();
              }
          }
      }
      
      /**
       * Fonction exécutée automatiquement toutes les 30minutes par Jeedom
       * seulement pour les modules Heatzy et Flam_Week2
       * */
       public static function cron30() {
           
           foreach (eqLogic::byType('heatzy') as $heatzy) {
           	
               if($heatzy->getIsEnable() != 1 )
               	continue;
               
               if($heatzy->getConfiguration('product', 'Heatzy') != 'Flam_Week2' &&
    							$heatzy->getConfiguration('product', 'Heatzy') != 'Heatzy' )
               	continue;
               
                $EtatProg='1'; /// Par defaut les taches sont actives
              
                /// Si le module est en timeout on ne verifie pas la programmation
								if ( $heatzy->getStatus('timeout', '0') == '1' ) {
                    /// Mise à jour de l'etat de la programmation désactivé
                    $EtatProg='0';
                }
                else {
                  /// Lecture des taches de ce module
                  $Skip = 0;            /// Nombre d'element sauté
                  $Limit = 100;        /// Limite du nombre de tache

                  /// Lecture du token
                  $UserToken = config::byKey('UserToken','heatzy','none');

                  do {
                      /// Lecture des taches par pas de $Limit
                      $aTasks = HttpGizwits::GetSchedulerList($UserToken, $heatzy->getLogicalId(), $Skip, $Limit);

                      /// Boucle des taches
                      foreach ($aTasks as $TaskNum => $aTask) {

                          if($aTask['enabled'] === false ) {    /// Sort de la boucle des taches à la premiere tache trouvée
                              $EtatProg='0';
                              break;
                          }
                      }
                      $Skip += count($aTasks);

                      if($EtatProg === '0' ) {/// Sort de la boucle des recherches des taches si au moins une est désactivée
                          break;
                      }

                  } while(!empty($aTasks) && count($aTasks) >= $Limit);
                  
                  if($Skip === 0 && empty($aTasks)) /// Si pas de saut c'est qu'il n'y a pas de programmation
                    $EtatProg = '0';
                }
                   /// Mise à jour de l'etat EtatProg
                   $heatzy->checkAndUpdateCmd('etatprog', $EtatProg);
                   
                   if( $EtatProg === '0' )
                       log::add('heatzy', 'debug',   $heatzy->getLogicalId() .  ' : programmation desactive');
                   else
                       log::add('heatzy', 'debug',   $heatzy->getLogicalId() . ' : programmation active');
                   
                   $mc = cache::byKey('heatzyWidgetmobile' . $heatzy->getId());
                   $mc->remove();
                   $mc = cache::byKey('heatzyWidgetdashboard' . $heatzy->getId());
                   $mc->remove();
                    
                   $heatzy->toHtml('mobile');
                   $heatzy->toHtml('dashboard');
                   $heatzy->refreshWidget();

               }/// Fin boucle des modules
       }

    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom*/
    public static function cronDaily() {
        //$aujourdhui =  strtotime( date(  "Y-m-d H:i:s" ) ) ;
        //$tmp = strtotime( "2025-06-20 20:20:20" ) ;
      	//$ExpireToken = strtotime( config::byKey('ExpireToken','heatzy','none') );
    	//message::add("Heatzy", '= '.$aujourdhui." - ".$tmp.' - '.$ExpireToken );
        /*
        foreach (update::all() as $update) {
            if ($update->getLogicalId() == 'heatzy' || $update->getLogicalId() == 'virtual'){
                log::add('heatzy', 'error', __METHOD__.'updates : '.$update->getLogicalId().' - '.$update->getSource().' - '.$update->getStatus().' - '.$update->getType().':'.$update->getLogicalId().' - '.$update->getLocalVersion().' - '.$update->getRemoteVersion());
        }*/
        
        heatzy::Login();
    }




    /*     * *********************Méthodes d'instance************************* */

    /**
     * @brief   Méthode appellée avant la création de votre objet
     */
    public function preInsert() {
        $this->setCategory('heating', 1);
    }

    public function postInsert() {
        
    }

    public function preSave() {
         
    }
    /**
     * @brief  Méthode appellée après la sauvegarde de votre objet
     *         Creation des 4 ordres : Off, Confort, Eco, HorsGel
     *         Creation de la commande refresh
     *         Creation de la commande info EtatConsigne
     */
    
    public function postSave() {
        /// Creation de la commande de rafraichissement
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new heatzyCmd();
            $refresh->setName(__('Rafraichir', __FILE__));
            $refresh->setLogicalId('refresh');
            $refresh->setType('action');
            $refresh->setSubType('other');
            $refresh->setEqLogic_id($this->getId());
            $refresh->setIsHistorized(0);
            $refresh->setIsVisible(1);
            $refresh->save();
        }
    }

    /**
     * Si le nom du module a changé, on le met à jour
     */
    public function preUpdate() {
        
        if ( $this->getConfiguration('dev_alias', '') != $this->getName() ) {
            
            $UserToken = config::byKey('UserToken','heatzy','');
        
            $aRes = HttpGizwits::SetBindingInformation($UserToken, $this->getLogicalId(), $this->getName());
            if($aRes === false)
                log::add('heatzy', 'error',  'Impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
            else                
                $this->setConfiguration('dev_alias', $this->getName());    
            }
        
    }

    public function postUpdate() {

    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

   
    public function toHtml($_version = 'dashboard') {
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $_version = jeedom::versionAlias($_version);
        $product = $this->getConfiguration('product', '');
    
        //log::add('heatzy', 'debug',  __METHOD__.' : Liste commandes - '.$this->getName());
        foreach ($this->getCmd() as $cmd) {	
            switch($cmd->getType()){
                case 'info':
                    //log::add('heatzy', 'debug',  __METHOD__.' : Name='.$this->getName().' - CmdId='.$cmd->getLogicalId().' - CmdName='.$cmd->getName().' - CmdType='.$cmd->getType());

                    $replace['#'.$cmd->getLogicalId().'_id#'] = $cmd->getId();
                    $replace['#'.$cmd->getLogicalId().'_cmd#'] = $cmd->execCmd();
                    $replace['#'.$cmd->getLogicalId().'_unite#'] = $cmd->getUnite();
                    $replace['#'.$cmd->getLogicalId().'_CollectDate#'] = $cmd->getCollectDate() ; 
                    $replace['#'.$cmd->getLogicalId().'_ValueDate#'] = $cmd->getValueDate() ; 
                    $replace['#'.$cmd->getLogicalId().'_history#'] = (is_object($cmd) && $cmd->getIsHistorized()) ? 'history cursor' : '';
                    $replace['none;#'.$cmd->getLogicalId().'_display#'] = (is_object($cmd) && $cmd->getIsVisible()) ? '#'.$cmd->getLogicalId().'_display#' : 'none;';
                    break;
                case 'action':
                    //log::add('heatzy', 'debug',  __METHOD__.' : Name='.$this->getName().' - CmdId='.$cmd->getLogicalId().' - CmdName='.$cmd->getName().' - CmdType='.$cmd->getType());

                    $replace['#'.$cmd->getLogicalId().'_id#'] = is_object($cmd) ? $cmd->getId() : '';
                    $replace['none;#'.$cmd->getLogicalId().'_display#'] = (is_object($cmd) && $cmd->getIsVisible()) ? '#'.$cmd->getLogicalId().'_display#' : 'none;';
                    break;
                default :
                    //log::add('heatzy', 'error',  __METHOD__.' : Type de commande ($cmd->getType()='.$cmd->getType().') inconnu');
                    break;
            }
        }
		
		//log::add('heatzy', 'debug',  __METHOD__.' : Name='.$this->getName().'-TypeTemplate='.$this->getConfiguration('TypeTemplate', '0'));
		switch( $this->getConfiguration('TypeTemplate', '') ){
			case '':
				$this->setConfiguration('TypeTemplate', '0');
                $this->save() ;
			case '0':
                $html = template_replace($replace, getTemplate('core', $_version, 'Dashboard','heatzy')); // template commun (bodbod)
            	break;
			case '1':
				$html = template_replace($replace, getTemplate('core', $_version, $product,'heatzy'));  // template d'origine (l3flo)
            	break;
			default :
				$html = template_replace($replace, getTemplate('core', $_version, 'xxx','heatzy')); // template jeedom
            	break;
		}
		//cache::set('heatzy' . $_version . $this->getId(), $html, 0);
        return $html;
    }


    /*     * **********************Getteur Setteur*************************** */
}

class heatzyCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes 
     * même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
     * public function dontRemoveCmd() {
     * return true;
     * }
     */

    public function execute($_options = array()) {
        $Result = array();
        
        if ($this->getLogicalId() == 'refresh') {
            $this->getEqLogic()->updateHeatzyDid($UserToken);
        }
        else if($this->getType() == 'info' ) {
              return $this->getValue();
        }
        else if($this->getType() == 'action' ) {
            
            $eqLogic = $this->getEqLogic();
            //log::add('heatzy', 'debug',  __METHOD__.' : '.$eqLogic->getName().' - LogicalId='.$this->getLogicalId().' ('.$this->getId().')');
            
            /// Lecture du token
            $UserToken = config::byKey('UserToken','heatzy','none');
            
            $Consigne = '' ;
            $ForUpdate = '' ;
            if ($this->getLogicalId() == 'plugzyon') {
        
                $Consigne = array( 'attrs' => array ( 'on_off' => 1 )  );
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'plugzyoff') {
              
                $Consigne = array( 'attrs' => array ( 'on_off' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'ProgOn') {
                if( $eqLogic->getConfiguration('product', '') == 'Heatzy' || $eqLogic->getConfiguration('product', '') == 'Flam_Week2'){
                    $eqLogic->GestProg(true);
                }
                else {
                    $Consigne = array( 'attrs' => array ( 'timer_switch' => 1 )  );
                }
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'ProgOff') {
				if( $eqLogic->getConfiguration('product', '') == 'Heatzy' || $eqLogic->getConfiguration('product', '') == 'Flam_Week2'){
					$eqLogic->GestProg(false);
                }
				else {
					$Consigne = array( 'attrs' => array ( 'timer_switch' => 0 )  );
				}
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'LockOn') {
                $Consigne = array( 'attrs' => array ( 'lock_switch' => 1 )  );
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'LockOff') {
                $Consigne = array( 'attrs' => array ( 'lock_switch' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'Lock_C_On') {
                $Consigne = array( 'attrs' => array ( 'LOCK_C' => 1 )  );
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'Lock_C_Off') {
                $Consigne = array( 'attrs' => array ( 'LOCK_C' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'WindowSwitchOn') {
                $Consigne = array( 'attrs' => array ( 'window_switch' => 1 )  );
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'WindowSwitchOff') {
                $Consigne = array( 'attrs' => array ( 'window_switch' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if( in_array($this->getLogicalId() , heatzy::$_HeatzyMode ) ) {
              
                $Mode = array_keys(heatzy::$_HeatzyMode, $this->getLogicalId());
              
                log::add('heatzy', 'debug', __METHOD__.' '.$this->getLogicalId() . ' mode = '. var_export($Mode, true));
              
                if( $eqLogic->getConfiguration('product', 'Heatzy') == 'Heatzy') {    /// Premiere version du module pilote
                    $Consigne = array( 'raw' => array(1, 1, $Mode[0]) ) ;
                }
                else {
					switch($Mode[0])
					{
					case 0:
					   $Mode = 'cft'; break;
					case 1:
					   $Mode = 'eco'; break;
					case 2:
					   $Mode = 'fro'; break;
					case 3:
					   $Mode = 'stop'; break;
					case 4:
					   $Mode = 'cft1'; break;
					case 5:
					   $Mode = 'cft2'; break;
					}
                  
                    $Consigne = array( 'attrs' => array ( 'mode' => $Mode )  );
                }
                $ForUpdate = '' ;
            }
            else{
                log::add('heatzy', 'error',  __METHOD__.' : Commande inconnue : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' ('.$this->getId().')');
            }/// Le mode
          
          	
            
            if( $Consigne != '' ){
                $Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
                if($Result === false) {
                    log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                    return false;
                }
                else{
                    /// Si une erreur de communication et token invalide on se re-synchronise
                    if(isset($Result['error_message']) && isset($Result['error_code'])) {
                        if($Result['error_code'] === '9004') {
                            log::add('heatzy', 'error',  __METHOD__.' : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' - '.$Result['error_code'].' - '.$Result['error_message']);
                            $Nb = $eqLogic->Synchronize();
                            if ($Nb == false) {
                                log::add('heatzy', 'error',  __METHOD__.' : erreur synchronisation');
                                return false;
                            }
                            else{
                                log::add('heatzy', 'info',  __METHOD__.' : '.$Nb. 'module(s) synchronise(s)');
                                $UserToken = config::byKey('UserToken','heatzy','none');
                                $Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
                                if(isset($Result['error_message']) && isset($Result['error_code'])) {
                                    log::add('heatzy', 'error',  __METHOD__.' : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' - '.$Result['error_code'].' - '.$Result['error_message']);
                                    return false;
                                }
                            }
                        }
                        else {
                            log::add('heatzy', 'error',  __METHOD__.' : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' - '.$Result['error_code'].' - '.$Result['error_message']);
                            return false;
                        }
                    }
                    else if($ForUpdate != '') {
                        $eqLogic->checkAndUpdateCmd($this->getConfiguration('infoName'), $ForUpdate);
                    }
                }
            }
            
            /// Mise à jour de l'état
            sleep(1); // tempo de 1sec pour laisser le temps a l'API de le prendre en compte et le restituer
            $this->getEqLogic()->updateHeatzyDid($UserToken);
            
        } /// Fin action
        $mc = cache::byKey('heatzyWidgetmobile' . $this->getEqLogic()->getId());
        $mc->remove();
        $mc = cache::byKey('heatzyWidgetdashboard' . $this->getEqLogic()->getId());
        $mc->remove();

        $this->getEqLogic()->toHtml('mobile');
        $this->getEqLogic()->toHtml('dashboard');
        $this->getEqLogic()->refreshWidget();
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>