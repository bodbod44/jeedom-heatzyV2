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
    
    public static $Timeout = 10 ;
    
    public static $_MaxError = 200 ;

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
            CURLOPT_TIMEOUT => self::$Timeout,
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode );
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
            CURLOPT_TIMEOUT => self::$Timeout
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
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
            CURLOPT_TIMEOUT => self::$Timeout
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode );
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
            CURLOPT_TIMEOUT => self::$Timeout
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
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
            CURLOPT_TIMEOUT => self::$Timeout,
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
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
            CURLOPT_TIMEOUT => self::$Timeout,
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
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
            CURLOPT_TIMEOUT => self::$Timeout,
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
            return false;
        }

        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        //   if(isset($aRep['error_message'])) {
        //       throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        //   }
     
     
        if( $aRep['error_code'] == '9004' || $aRep['error_code'] == '9006' ) { 
            // erreur token invalide, alors on va en chercher un nouveau
            if( heatzy::Login() ){
              
                $UserToken = config::byKey('UserToken','heatzy','none');
                
                log::add('heatzy', 'debug',  __METHOD__.': Login() OK - Nouveau Token ('.$UserToken.')');
              
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
                    CURLOPT_TIMEOUT => self::$Timeout,
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
                    log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
                    return false;
                }

                ///Décodage de la réponse
                $aRep = json_decode($result, true);
            }
            else{
                log::add('heatzy', 'debug',  __METHOD__.': Login() KO');
                return false;
            }
        }
        return $aRep;
    }
    
    /**
     * @brief Fonction qui permet de récuperer le dernier status du device did
     * 
     * @param $Did         Identifiant du module dans le cloud
     * 
     * @return Un tableau associatif ou false en cas d'erreur
     */
    public static function GetConsigne($UserToken, $Did ) {
              
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
            CURLOPT_TIMEOUT => self::$Timeout
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
            return false;
        }
      
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
        //if(isset($aRep['error_message'])) {
        //    throw new Exception(__('Gizwits erreur : ', __FILE__) . $aRep['error_code'].' '.$aRep['error_message'] . __(', detail :  ', __FILE__) .$aRep['detail_message']);
        // }
      
        if( $aRep['error_code'] == '9004' || $aRep['error_code'] == '9006' ) {
            // erreur token invalide, alors on va en chercher un nouveau
            if( heatzy::Login() ){
              
                $UserToken = config::byKey('UserToken','heatzy','none');
                
                log::add('heatzy', 'debug',  __METHOD__.': Login() OK - Nouveau Token ('.$UserToken.')');
              
                /// Parametres cUrl
                $params = array(
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'X-Gizwits-Application-Id: '.self::$HeatzyAppId,
                        'X-Gizwits-User-token: '.$UserToken
                    ),
                    CURLOPT_URL => self::$UrlGizwits.'/app/devdata/'.$Did.'/latest',
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_TIMEOUT => self::$Timeout
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
                    log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
                    return false;
                }

                ///Décodage de la réponse
                $aRep = json_decode($result, true);
            }
            else{
                log::add('heatzy', 'debug',  __METHOD__.': Login() KO');
                return false;
            }
        }
      
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
            CURLOPT_TIMEOUT => self::$Timeout
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
            log::add('heatzy', 'debug',  __METHOD__.': erreur http '.$httpcode);
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
            log::add('heatzy', 'warning', __METHOD__.' : impossible de se connecter a: '.HttpGizwits::$UrlGizwits);
            return false;
        }
        log::add('heatzy', 'debug',  '$aResult :'.var_export($aResult, true));
         
        $TokenExpire = date('Y-m-d H:i:s', $aResult['expire_at']);
        $UserToken = $aResult['token'];
        
        if( config::byKey('UserToken', 'heatzy', '') != $UserToken || config::byKey('ExpireToken', 'heatzy', '') != $TokenExpire)
            message::add("Heatzy", 'Génération du token heatzy : '.config::byKey('ExpireToken', 'heatzy', '').'/'.config::byKey('UserToken', 'heatzy', '').' -> '.$TokenExpire.'/'.$UserToken);
        //else
        //    message::add("Heatzy", 'Génération du token heatzy -> Pas de changement');
        
        config::save('UserToken', $UserToken, 'heatzy'); /// => Sauvegarde du token utilisateur
        config::save('ExpireToken', $TokenExpire, 'heatzy'); /// => Sauvegarde de l'expiration du token
        
        /*
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
        */
        
        return true ;
    }
    
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
    public static function Synchronize() {
            
        if( !cache::exist('Heatzy_Synchronize') ) cache::set( 'Heatzy_Synchronize' , 1) ;
        cache::set( 'Heatzy_Synchronize' , strtotime(date("Y-m-d H:i:s")) ) ;
      
        /// Login + creation du cron
        if( heatzy::Login() === false ){
            log::add('heatzy', 'warning',  __METHOD__.' : heatzy::Login - impossible de se connecter à : '.HttpGizwits::$UrlGizwits);
            return false;
        }
            
        $UserToken = config::byKey('UserToken','heatzy','none');   
      
        /// Bindings
        $aDevices = HttpGizwits::Bindings($UserToken);      
      
        if($aDevices === false) {
            log::add('heatzy', 'warning',  __METHOD__.' : HttpGizwits::Bindings - impossible de se connecter à : '.HttpGizwits::$UrlGizwits);
            return false;
        }
        
      	log::add('heatzy', 'debug', __METHOD__.' '.count($aDevices ['devices']).'  module(s) trouvé');
        //log::add('heatzy', 'debug', __METHOD__.' $aDevice :'.var_export($aDevices, true));
      
      	$Nb_Add = 0;
      	$aSearchDid = [] ; //Va stocker les DID trouvé (pour vérifier ceux qui ont disparus)
        foreach ($aDevices ['devices'] as $DeviceNum => $aDevice) {
            $aSearchDid[] = $aDevice['did'] ;
            $eqLogic = self::byLogicalId( $aDevice['did'] , 'heatzy', false);
            if (! is_object($eqLogic)) {   /// Creation des dids inexistants
                $eqLogic = new heatzy();
              	$Nb_Add++ ;
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

            if(isset($aDevice['product_name']))
                $eqLogic->setConfiguration('product',$aDevice['product_name']);

            if(isset($aDevice['product_key']))
                $eqLogic->setConfiguration('product_key',$aDevice['product_key']);
          
            /*
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
                 $eqLogic->setConfiguration('heatzytype','pilote');*/
          
            /// Si connecté ou pas
            if( $aDevice['is_online'] == 'true')
                $eqLogic->checkAndUpdateCmd('IsOnLine', 1 );
            else
                $eqLogic->checkAndUpdateCmd('IsOnLine', 0 );
          
          	$eqLogic->save();
          
          	// A mettre après le save (car le save met le statut à 0)
            if( $aDevice['is_online'] == 'true' ){
                $eqLogic->setStatus('timeout','0');
            }
            else{
                $eqLogic->setStatus('timeout','1');
            }
                          
            // mise à jour du did
            if ($eqLogic->getIsEnable() == 1 && $eqLogic->getStatus('timeout') == 0 ) { // Ne pas faire si timout (car l'update va remettre reinit le timeout=
                $eqLogic->updateHeatzyDid($UserToken,$aStatus);
            }
          
        } // foreach
        
        //log::add('heatzy', 'info', 'Synchronistation de '. count($aDevices ['devices']).' module(s) Heatzy');
      	if( $Nb_Add > 0)
        	log::add('heatzy', 'info', $Nb_Add.' module(s) Heatzy ajouté(s) - '.count($aDevices ['devices']).'  module(s) Heatzy rattaché(s) au compte');
      	log::add('heatzy', 'debug', __METHOD__.' '.$Nb_Add.' module(s) Heatzy ajouté(s) - '.count($aDevices ['devices']).'  module(s)');
        //message::add("Heatzy", 'Synchronistation de '. count($aDevices ['devices']).' module(s) Heatzy');
      
      	// Recherche des équipements qui ne sont plus rattachés au compte
      	foreach (eqLogic::byType('heatzy') as $heatzy) {
            if( !in_array($heatzy->getLogicalId() , $aSearchDid ) ){
                if( $heatzy->getIsEnable() == 1 ){
                    $heatzy->setIsEnable(0);
                    $heatzy->setIsVisible(0) ;
                    $heatzy->save();
                    $heatzy->checkAndUpdateCmd('IsOnLine', 0 );   
                    $heatzy->setStatus('timeout','1');
                    log::add('heatzy', 'error', 'Le module -'.$heatzy->getName().'- ('.$heatzy->getLogicalId().') n est plus rattaché au compte. Il est maintenant désactivé et non visible (mais pas supprimé)' );   
                }
            }
        }
      	//log::add('heatzy', 'debug', 'array_diff='.var_export( array_diff(eqLogic::byType('heatzy'), $aDevices ['devices']), true) );
      
      	cache::set( 'Heatzy_Synchronize' , 0) ;
      
        return count($aDevices['devices']);    
    }
    /**
     * @brief Fonction de mise à jour du device did
     */
    public function updateHeatzyDid($UserToken = null, $aDevice = array()) {
      
        if( !cache::exist('Heatzy_CptError') ){
            cache::set( 'Heatzy_CptError' , 0) ;
            log::add('heatzy', 'debug',  __METHOD__.': INIT cache' );
        }
        //log::add('heatzy', 'debug',  __METHOD__.' : cache='.cache::byKey('Heatzy_CptError')->getValue() );
      
        if(empty($aDevice)) {
            /// Lecture de l'etat
            if( $UserToken == null )
              $UserToken = config::byKey('UserToken','heatzy','none');
            $aDevice = HttpGizwits::GetConsigne($UserToken, $this->getLogicalId());
            if($aDevice === false) {
                log::add('heatzy', 'warning',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                $this->setStatus('timeout','1');
                $this->save();
              	cache::set('Heatzy_CptError', cache::byKey('Heatzy_CptError')->getValue() + 1 );
                return false;
            }
            else if(isset($aDevice['error_message']) && isset($aDevice['error_code'])) {
                log::add('heatzy', 'error',  __METHOD__.' : '.$this->getLogicalId().' - '.$aDevice['error_code'].' - '.$aDevice['error_message'].' - '.$aDevice['detail_message']);
              	cache::set('Heatzy_CptError', cache::byKey('Heatzy_CptError')->getValue() + 1 );
                return false;
            }
          
          	// Si pas d'erreur, on réinitialise le garde fou
      		cache::set('Heatzy_CptError', 0 );
        } // if empty
      
        /// Mise à jour de la derniere communication
		if(isset($aDevice['updated_at']) && $aDevice['updated_at'] != 0 ) {
            //$this->setStatus('timeout','0');
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
            else if( $aDevice['attr']['mode'] == 'cft1' ) {  /// Confort-1
                $KeyMode = 'Confort-1';
            }
            else if( $aDevice['attr']['mode'] == 'cft2' ) {  /// Confort-2
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
                  	$this->checkAndUpdateCmd('IsOnLine', 0 ); 
                  	$this->save(); /// Enregistre les info  
                  	$this->setStatus('timeout','1');
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
            
            // Mode dérogation
                // 0 : pas de dérogation
                // 1 : mode vacances
                // 2 : mode boost
                // 3 : détection de presence
			if( isset ($aDevice['attr']['derog_mode']) ){
				$this->checkAndUpdateCmd('derog_mode', $aDevice['attr']['derog_mode'] );
              
                // Temps de dérogation
                //  vacances en jours (j)
                //  boost en minutes (min)
              	isset ($aDevice['attr']['derog_time']) ? $derog_time = $aDevice['attr']['derog_time'] : $derog_time = '0' ;
                $this->checkAndUpdateCmd('derog_time_vacances', $aDevice['attr']['derog_mode'] == '1' ? $derog_time : 0 );
              	$this->checkAndUpdateCmd('derog_time_boost'   , $aDevice['attr']['derog_mode'] == '2' ? $derog_time : 0 );              
            }

			// Détéction de présence 
			if( $aDevice['attr']['derog_mode'] == 3 && array_keys(self::$_HeatzyMode, $KeyMode)[0] == 0){
				$this->checkAndUpdateCmd('detect_presence', 1 );
            }
            else{
                $this->checkAndUpdateCmd('detect_presence', 0 );
            }
            
            $this->CalculExterne( $aDevice ) ;
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
     * @brief Fonction qui permet de gerer les parametres et calcul externe a heatzy
     * 
     * @param tableau retour API
     */
    public function CalculExterne($aDevice) {
                
        $CapteurExtHumi = $this->getConfiguration('CapteurExtHumi', '');
      	$humi = -99 ;
        if( $CapteurExtHumi != '' ){
            preg_match_all("/#([0-9]*)#/", $CapteurExtHumi, $matches);
            if (count($matches[1]) == 1) {
                $res = cmd::byId( $matches[1][0] )->execCmd() ;
                if( is_numeric($res) ){
                  $humi = $res ;
                  $this->CheckAndCreateCmd( array( "attr" => array( "cur_humi" => "0" ) ) ) ; // $aDevice['attr']['cur_humi']
                  $this->checkAndUpdateCmd('cur_humi', $humi );
                }
            }
        }
        if( !isset($aDevice['attr']['cur_humi']) && $humi == -99 )
        	$this->checkAndUpdateCmd('cur_humi', $humi );
        
      
        $CapteurExtTemp = $this->getConfiguration('CapteurExtTemp', '');
      	$Temp = -99 ;
        if( $CapteurExtTemp != '' ){
            preg_match_all("/#([0-9]*)#/", $CapteurExtTemp, $matches);
            if (count($matches[1]) == 1) {
                $res = cmd::byId( $matches[1][0] )->execCmd() ;
                if( is_numeric($Temp) ){
                  $Temp = $res ;
                  $this->CheckAndCreateCmd( array( "attr" => array( "cur_temp" => "0" ) ) ) ; // $aDevice['attr']['cur_temp']
                  $this->checkAndUpdateCmd('cur_temp', $Temp );
                }
            }
        }
        if( !isset($aDevice['attr']['cur_temp']) && !isset($aDevice['attr']['cur_tempH']) && $Temp == -99 )
        	$this->checkAndUpdateCmd('cur_temp', $Temp );
      
        
        //return false ;
        $TendanceDegre = $this->getConfiguration('TendanceDegre', '2');
        $TendanceDuree = $this->getConfiguration('TendanceDuree', '5');
        $cur_temp = $this->getCmd(null, 'cur_temp');

        if ( is_object($cur_temp) && is_numeric($TendanceDegre) && is_numeric($TendanceDuree) ){
            $Temp = $cur_temp->execCmd() ;
            if( $Temp > -50 && $Temp < 100 ){
                $this->CheckAndCreateCmd( array( "attr" => array( "WindowOpened" => "0" ) ) ) ; // $aDevice['attr']['WindowOpened']
                $this->CheckAndCreateCmd( array( "attr" => array( "Tendance" => "0" ) ) ) ; // $aDevice['attr']['Tendance']
                $startTendance = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') .' - '.$TendanceDuree.' min'));
                $tendance = Round( $cur_temp->getTendance( $startTendance , date('Y-m-d H:i:s') ) , 4 ) ;
                $this->checkAndUpdateCmd('Tendance', $tendance );
                //log::add('heatzy', 'debug',  $this->getLogicalId().' : WindowOpened : tendance='.$tendance );
                if( $tendance <= -($TendanceDegre / $TendanceDuree ) ) {
                    $this->checkAndUpdateCmd('WindowOpened', true );
                }
                else{
                    $WindowOpened = $this->getCmd(null, 'WindowOpened');
                    $ValueDate = strtotime( $WindowOpened->getValueDate() ) ;
                    $now = strtotime(date('Y-m-d H:i:s') ) ;
                    //log::add('heatzy', 'debug',  $this->getLogicalId().' : WindowOpened : tendance='.$tendance.' - '.$ValueDate.' - '.$now );
                    if( $tendance >= ($TendanceDegre / $TendanceDuree ) || ($now - $ValueDate) >= 1800 ){  // tendance inverse ou 30min
                        $this->checkAndUpdateCmd('WindowOpened', false );    
                    }
                }
            }
        }
    }

      /**
     * @brief Fonction qui permet de savoir si le module gère 4 ou 6 ordres
     * 
     * @param tableau retour API
     */
  /*
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
            if( $ResultSet['error_code'] == '' || $ResultSet['error_code'] == '9025'){
                sleep(3); // Attente 3sec
                // Appel API pour analyser le changement ou non de consigne
                $ResultGet = HttpGizwits::GetConsigne($UserToken, $this->getLogicalId() ) ;
                if( $ResultGet['error_code'] == ''){
                    if( $ResultGet['attr']['mode'] == 'cft2'){
                        // L'ordre a bien été modifié (donc 6 ordres)
                        $NbOrdres = 6 ;
                    }
                    else if( $ResultGet['attr']['mode'] == 'cft' || $ResultGet['attr']['mode'] == $aDevice['attr']['mode'] ){
                        // L'ordre n'a pas été modifié (donc 4 ordres)
                        //  - Les heatzy ne renvoit pas d'erreur mais se positionne sur cft
                        //  - Les Glow et Shine renvoit une erreur 9025 (GIZ_OPENAPI_ATTR_INVALID) et reste sur le mode précédent
                        $NbOrdres = 4 ;
                    }
                }
                else
                    log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code1 GET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
            }
            else
                log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code2 SET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
            // On remet l'ordre initial
            sleep(1);
            $Consigne = array( 'attrs' => array ( 'mode' => $aDevice['attr']['mode'] )  );
            $ResultSet = HttpGizwits::SetConsigne($UserToken, $this->getLogicalId(), $Consigne);
            if( $ResultSet['error_code'] != '' )
                log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code3 SET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
        }

        log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' Nombre d ordre='.$NbOrdres);
      	return $NbOrdres ;
    }*/
    
      /**
     * @brief Fonction qui permet de savoir si le module gère 4 ou 6 ordres
     * 
     * @param tableau retour API
     */
  
    public function VerifOrdreAccepte( $aDevice , $attr , $valeur , $attr2 , $valeur2 , $verif_result , $valeurInit = false) {
        if( $aDevice['attr'][$attr] == $valeur  ){
            log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' - '.$attr.'=>'.$valeur.' - SET deja valorisé');
            return true ;
        }
        else{
            // On tente de mettre la valeur
          	if( $attr2 != '' )
            	$Consigne = array( 'attrs' => array ( $attr => $valeur , $attr2 => $valeur2 )  );
          	else
              	$Consigne = array( 'attrs' => array ( $attr => $valeur )  );
            $UserToken = config::byKey('UserToken','heatzy','none');
            // Appel API pour SET $valeur
            sleep(3); // Attente 2sec  
            $ResultSet = HttpGizwits::SetConsigne($UserToken, $this->getLogicalId(), $Consigne);
            
            if( $ResultSet['error_code'] == '9025' ){
                log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' - '.$attr.'=>'.$valeur.' SET error 9025 attribut invalide');
                return false;
            }
                
            if( $ResultSet['error_code'] == ''){
                if( !$verif_result ){
					return true ;
                }
                else{       
                    sleep(2); // Attente 3sec
                    // Appel API pour analyser le changement ou non de consigne
                    $ResultGet = HttpGizwits::GetConsigne($UserToken, $this->getLogicalId() ) ;
                    if( $ResultGet['error_code'] == ''){
                        if( $ResultGet['attr'][$attr] == $valeur ){
                            log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' - '.$attr.'=>'.$valeur.' - SET valorisé avec succes');
                            return true ;
                        }
                        else{
                            log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' - '.$attr.'=>'.$valeur.' - SET valeur autre (KO-'.$ResultGet['attr'][$attr].')');
                        }
                    }
                    else
                        log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code1 GET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
                }
            }
            else
                log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code2 SET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
            
            if( $valeurInit == true ){
                // On remet l'ordre initial
                sleep(2);
                $Consigne = array( 'attrs' => array ( $attr => $aDevice['attr'][$attr] )  );
                $ResultSet = HttpGizwits::SetConsigne($UserToken, $this->getLogicalId(), $Consigne);
                if( $ResultSet['error_code'] != '' )
                    log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' error_code3 SET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
            }
        }

      	return false ;
    }
    
  
    /**
     * @brief Fonction qui permet de créer les commandes en fonction du retour de l'API
     * 
     * @param tableau retour API
     */
  
    public function CheckAndCreateCmd($aDevice) {
               
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
              	$CurTemp->setConfiguration('tempHL', isset ($aDevice['attr']['cur_tempH']) ) ;
				$CurTemp->setEqLogic_id($this->getId());
				$CurTemp->setOrder(10);
				$CurTemp->setIsHistorized(0);
				$CurTemp->setIsVisible(1);
				$CurTemp->save();
			}
            else{
				$CurTemp->setConfiguration('tempHL', isset ($aDevice['attr']['cur_tempH']) ) ;
                $CurTemp->save();
            }
        } // if cur_temp - cur_tempH
      
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
				$CurHumi->setOrder(11);
                $CurHumi->setIsHistorized(0);
                $CurHumi->setIsVisible(1);
                $CurHumi->save();
            }
        } // cur_humi
      
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
				$EcoTemp->setOrder(12);
				$EcoTemp->setIsHistorized(0);
				$EcoTemp->setIsVisible(1);
				$EcoTemp->save();
			}
          
            $cmd = $this->getCmd(null, 'eco_temp_consigne');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('eco_temp_consigne');
                $cmd->setName(__('Consigne Temp. eco', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('slider');
                $cmd->setConfiguration('infoName', 'eco_temp');
                $cmd->setConfiguration('value', "#slider#");
                $cmd->setConfiguration('minValue', 10);  // valeur minimale
                $cmd->setConfiguration('maxValue', 20); // valeur maximale
                $cmd->setConfiguration('tempHL', isset ($aDevice['attr']['eco_tempH']) ) ;
                $cmd->setValue($EcoTemp ->getId());  
                $cmd->setDisplay('parameters', ['step' => 0.5]);
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(13);
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(0);
                $cmd->save();
            }
            else{
                $cmd->setConfiguration('tempHL', isset ($aDevice['attr']['eco_tempH']) ) ;
                $cmd->save();
          }
          
        } // if eco_temp - eco_tempH
        
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
				$CftTemp->setOrder(14);
				$CftTemp->setIsHistorized(0);
				$CftTemp->setIsVisible(1);
				$CftTemp->save();
			}
          
            $cmd = $this->getCmd(null, 'cft_temp_consigne');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('cft_temp_consigne');
                $cmd->setName(__('Consigne Temp. confort', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('slider');
				//$cmd->setUnite('°C');
                $cmd->setConfiguration('infoName', 'cft_temp');
                $cmd->setConfiguration('value', "#slider#");
                $cmd->setConfiguration('minValue', 15);  // valeur minimale
                $cmd->setConfiguration('maxValue', 25); // valeur maximale
              	$cmd->setConfiguration('tempHL', isset ($aDevice['attr']['cft_tempH']) ) ;
                $cmd->setValue($CftTemp ->getId());  
                $cmd->setDisplay('parameters', ['step' => 0.5]);
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(15);
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(0);
                $cmd->save();
            }
            else{
				$cmd->setConfiguration('tempHL', isset ($aDevice['attr']['cft_tempH']) ) ;
                $cmd->save();
            }    
        } // cft_temp - cft_tempH
      
        /// Creation de la commande window_opened
      	if( isset ($aDevice['attr']['Tendance']) ){
            $cmd = $this->getCmd(null, 'Tendance'); 
            if (!is_object($cmd) ) {
                $cmd = new heatzyCmd();
                $cmd->setName(__('Tendance Température', __FILE__));
                $cmd->setLogicalId('Tendance');
                $cmd->setType('info');
                $cmd->setSubType('numeric');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(16);
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(0);
                $cmd->save();
            }    
        }

        /// Creation de la commande window_opened
      	if( isset ($aDevice['attr']['WindowOpened']) ){
            $cmd = $this->getCmd(null, 'WindowOpened'); 
            if (!is_object($cmd) ) {
                $cmd = new heatzyCmd();
                $cmd->setName(__('Fenetre Ouverte', __FILE__));
                $cmd->setLogicalId('WindowOpened');
                $cmd->setType('info');
                $cmd->setSubType('binary');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(17);
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
        }
      
        // Rattrapage 4 ordres 6 ordres
        // Detection des pilote_pro / shine / glow qui n'auraient que 4 ordres (creation avant la prise en charge des 6 ordres)
        //$Heatzy6Ordres = array("Pilote_Pro","Shine_ble","Glow_Simple_ble");
        $Heatzy6Ordres = array("Pilote_Pro");
      	if( in_array( $this->getConfiguration('product', '') , $Heatzy6Ordres) && is_object( $this->getCmd(null,'Confort')) && !is_object( $this->getCmd(null,'Confort-2')) ){
            $rattrapage = true ;
            log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' rattrapage 6 ordres');
        }
        else
            $rattrapage = false ;
      
        if( (isset($aDevice['attr']['mode']) && !is_object( $this->getCmd(null,'EtatConsigne'))) || $rattrapage ){

            // Verifie si module 4 ou 6 ordres
        	//$NbOrdres = $this->VerifNbOrdres($aDevice) ;
            $NbOrdres = $this->VerifOrdreAccepte( $aDevice , 'mode' , 'cft2' , '' , '' , true , true ) ? 6 : 4 ; 
          	log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' VerifOrdreAccepte='.$NbOrdres.' ordres');
          
          	if( $NbOrdres > 0 ){
              
                /// Creation de la commande info Etat numeric
                $etat = $this->getCmd(null, 'EtatConsigne');
                if (!is_object($etat)) {
                    $etat = new heatzyCmd();
                    $etat->setName(__('Etat Consigne', __FILE__));
                    $etat->setLogicalId('EtatConsigne');
                    $etat->setType('info');
                    $etat->setSubType('numeric');
                    $etat->setEqLogic_id($this->getId());
					$etat->setOrder(20);
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
					$mode->setOrder(21);
                    $mode->setIsHistorized(0);
                    $mode->setIsVisible(1);
                    $mode->save();
                }
              
                foreach (self::$_HeatzyMode as $Key => $Mode ) {
                    if($Key < $NbOrdres){ // On arrêt la création selon le nombre d'ordres du module
                        /// Creation de la commande action $Mode : $Key
                        $cmd = $this->getCmd(null, $Mode);
                        if (!is_object($cmd)) {
                            log::add('heatzy', 'debug',  __METHOD__.': '.$this->getName().' creation commande :'.$Key.'=>'.$Mode);
                            $cmd = new heatzyCmd();
                            $cmd->setLogicalId($Mode);
                            $cmd->setName(__('Mode '.$Mode, __FILE__));
                            $cmd->setType('action');
                            $cmd->setSubType('other');
                            $cmd->setConfiguration('infoName', 'EtatConsigne');
                            $cmd->setEqLogic_id($this->getId());
							$cmd->setOrder(22 + $Key);
                            $cmd->setIsHistorized(0);
                            $cmd->setIsVisible(1);
                            $cmd->save();
                        }
                        else{
                            $cmd->setConfiguration('infoName', 'EtatConsigne');
							$cmd->save();
                        }
                    }
                } // for
            } // if nb ordre > 0
        } // if mode 
            
        if( isset ($aDevice['attr']['timer_switch']) ){            
            /// Creation de la commande info etatprog binaire
            $etat = $this->getCmd(null, 'etatprog');
            if (!is_object($etat)) {
                $etat = new heatzyCmd();
                $etat->setName(__('Etat programmation', __FILE__));
                $etat->setLogicalId('etatprog');
                $etat->setType('info');
                $etat->setSubType('binary');
                $etat->setEqLogic_id($this->getId());
				$etat->setOrder(30);
                $etat->setIsHistorized(0);
                $etat->setIsVisible(1);
                $etat->save();
            }
            
            // Programmation On/Off
            $cmd = $this->getCmd(null, 'ProgOn');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('ProgOn');
                $cmd->setName(__('Activer Programmation', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'etatprog');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(31);
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
                $cmd->setName(__('Désactiver Programmation', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'etatprog');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(32);
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
            else{
                $cmd->setConfiguration('infoName', 'etatprog');
            }
        } // if timer switch
      
        if( isset ($aDevice['attr']['lock_switch']) ){
            
            /// Creation de la commande info etatlock binaire
            $etat = $this->getCmd(null, 'etatlock');
            if (!is_object($etat)) {
                $etat = new heatzyCmd();
                $etat->setName(__('Etat Verrouillage', __FILE__));
                $etat->setLogicalId('etatlock');
                $etat->setType('info');
                $etat->setSubType('binary');
                $etat->setEqLogic_id($this->getId());
				$etat->setOrder(33);
                $etat->setIsHistorized(0);
                $etat->setIsVisible(1);
                $etat->save();
            }
            
            // Verouillage-lock On/Off
            $cmd = $this->getCmd(null, 'LockOn');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('LockOn');
                $cmd->setName(__('Activer Verrouillage', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'etatlock');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(34);
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
                $cmd->setName(__('Désactiver Verrouillage', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'etatlock');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(35);
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
            else{
                $cmd->setConfiguration('infoName', 'etatlock');
            }
        } // if lock_switch

        if( isset ($aDevice['attr']['LOCK_C']) ){

            /// Creation de la commande info Lock_C_State binaire
            $etat = $this->getCmd(null, 'Lock_C_State');
            if (!is_object($etat)) {
                $etat = new heatzyCmd();
                $etat->setName(__('Etat Verrouillage', __FILE__));
                $etat->setLogicalId('Lock_C_State');
                $etat->setType('info');
                $etat->setSubType('binary');
                $etat->setEqLogic_id($this->getId());
				$etat->setOrder(36);
                $etat->setIsHistorized(0);
                $etat->setIsVisible(1);
                $etat->save();
            }
            
            // Verouillage-lock On/Off
            $cmd = $this->getCmd(null, 'Lock_C_On');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('Lock_C_On');
                $cmd->setName(__('Activer Verrouillage', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'Lock_C_State');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(37);
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
                $cmd->setName(__('Désactiver Verrouillage', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'Lock_C_State');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(38);
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
            else{
                $cmd->setConfiguration('infoName', 'Lock_C_State');
            }
        } // if lock_switch
          
        if( isset ($aDevice['attr']['window_switch']) ){
            /// Creation de la commande Activation de la détection de fenêtre ouverte du pilote_pro
            $CurWindow = $this->getCmd(null, 'WindowSwitch'); 
            if (!is_object($CurWindow)) {
                $CurWindow = new heatzyCmd();
                $CurWindow->setName(__('Etat Activation fenêtre ouverte', __FILE__));
                $CurWindow->setLogicalId('WindowSwitch');
                $CurWindow->setType('info');
                $CurWindow->setSubType('binary');
                $CurWindow->setEqLogic_id($this->getId());
				$CurWindow->setOrder(39);
                $CurWindow->setIsHistorized(0);
                $CurWindow->setIsVisible(1);
                $CurWindow->save();
            }
            else{
                $CurWindow->setName(__('Etat Activation fenêtre ouverte', __FILE__));
                $CurWindow->save();
            } 
          
            // window_switch On/Off
            $cmd = $this->getCmd(null, 'WindowSwitchOn');
            if (!is_object($cmd)) {
                $cmd = new heatzyCmd();
                $cmd->setLogicalId('WindowSwitchOn');
                $cmd->setName(__('Activer Fenetre Ouverte', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'WindowSwitch');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(40);
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
                $cmd->setName(__('Désactiver Fenetre Ouverte', __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'WindowSwitch');
                $cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(41);
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
				$Plugzy->setOrder(42);
				$Plugzy->setIsHistorized(0);
				$Plugzy->setIsVisible(1);
				$Plugzy->save();
			}
	          
			/// Creation de la commande plugzy on
			$cmd = $this->getCmd(null, 'plugzyon');
			if (!is_object($cmd)) {
				$cmd = new heatzyCmd();
				$cmd->setLogicalId('plugzyon');
				$cmd->setName(__('Plugzy ON', __FILE__));
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setConfiguration('infoName', 'plugzy');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(43);
				$cmd->setIsHistorized(0);
				$cmd->setIsVisible(1);
				$cmd->save();
			}
			  
			/// Creation de la commande plugzy off
			$cmd = $this->getCmd(null, 'plugzyoff');
			if (!is_object($cmd)) {
				$cmd = new heatzyCmd();
				$cmd->setLogicalId('plugzyoff');
				$cmd->setName(__('Plugzy OFF', __FILE__));
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setConfiguration('infoName', 'plugzy');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setOrder(44);
				$cmd->setIsHistorized(0);
				$cmd->setIsVisible(1);
				$cmd->save();
			}
        } // if on_off
 
        
        /// Creation de la commande derog_mode / derog_time
      	if( isset ($aDevice['attr']['derog_mode']) && isset ($aDevice['attr']['derog_time']) ){
            $cmd = $this->getCmd(null, 'derog_mode'); 
            if (!is_object($cmd) ) {
                $cmd = new heatzyCmd();
                $cmd->setName(__('Mode dérogation', __FILE__));
                $cmd->setLogicalId('derog_mode');
                $cmd->setType('info');
                $cmd->setSubType('numeric');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
          		$cmd->setOrder(60);
                $cmd->save();
            //}

                /*
                $cmd = $this->getCmd(null, 'derog_time'); 
                if (!is_object($cmd) ) {
                    $cmd = new heatzyCmd();
                    $cmd->setName(__('Délai dérogation', __FILE__));
                    $cmd->setLogicalId('derog_time');
                    $cmd->setType('info');
                    //$cmd->setUnite('Min');
                    $cmd->setSubType('numeric');
                    $cmd->setEqLogic_id($this->getId());
					$cmd->setOrder(61);
                    $cmd->setIsHistorized(0);
                    $cmd->setIsVisible(1);
                    $cmd->save();
                }
                */
                
                /// Creation de la commande derog_off (0)
                $cmd = $this->getCmd(null, 'derog_off');
                if (!is_object($cmd)) {
                    $cmd = new heatzyCmd();
                    $cmd->setLogicalId('derog_off');
                    $cmd->setName(__('Derogation OFF', __FILE__));
                    $cmd->setType('action');
                    $cmd->setSubType('other');
                    $cmd->setConfiguration('infoName', 'derog_mode');
                    $cmd->setEqLogic_id($this->getId());
					$cmd->setOrder(62);
                    $cmd->setIsHistorized(0);
                    $cmd->setIsVisible(1);
                    $cmd->save();
                }
                
                /// Creation de la commande derog_vacances (1)
                $cmd = $this->getCmd(null, 'derog_vacances');
                if (!is_object($cmd)) {
                    if( $this->VerifOrdreAccepte( $aDevice , 'derog_mode' , 1 , 'derog_time' , 1 , false ) ){
                      
                        $infoCmd = $this->getCmd(null, 'derog_time_vacances'); 
                        if (!is_object($infoCmd) ) {
                            $infoCmd = new heatzyCmd();
                            $infoCmd->setName(__('Délai dérogation Vacances', __FILE__));
                            $infoCmd->setLogicalId('derog_time_vacances');
                            $infoCmd->setType('info');
                            $infoCmd->setUnite('Jours');
                            $infoCmd->setSubType('numeric');
                            $infoCmd->setEqLogic_id($this->getId());
							$infoCmd->setOrder(63);
                            $infoCmd->setIsHistorized(0);
                            $infoCmd->setIsVisible(0);
                            $infoCmd->save();
                        }
            
                        $cmd = $this->getCmd(null, 'derog_vacances'); 
                        if (!is_object($cmd) ) {
                            $cmd = new heatzyCmd();
                            $cmd->setLogicalId('derog_vacances');
                            $cmd->setName(__('Derogation Vacances', __FILE__));
                            $cmd->setType('action');
                            $cmd->setSubType('slider');
                            $cmd->setConfiguration('infoName', 'derog_time_vacances');
                            $cmd->setConfiguration('value', "#slider#");
                            $cmd->setConfiguration('minValue', 0);  // valeur minimale
                            $cmd->setConfiguration('maxValue', 30); // valeur maximale
                            $cmd->setValue($infoCmd ->getId());  
                          	$cmd->setDisplay('parameters', ['step' => 1]);
                            $cmd->setEqLogic_id($this->getId());
							$cmd->setOrder(64);
                            $cmd->setIsHistorized(0);
                            $cmd->setIsVisible(1);
                            $cmd->save();
                        }
                    } // if VerifOrdreAccepte
                }
                
                /// Creation de la commande derog_boost (2)
                $cmd = $this->getCmd(null, 'derog_boost');
                if (!is_object($cmd)) {
                    if( $this->VerifOrdreAccepte( $aDevice , 'derog_mode' , 2 , 'derog_time' , 30 , false ) ){
                      
                        $infoCmd = $this->getCmd(null, 'derog_time_boost'); 
                        if (!is_object($infoCmd) ) {
                            $infoCmd = new heatzyCmd();
                            $infoCmd->setName(__('Délai dérogation Boost', __FILE__));
                            $infoCmd->setLogicalId('derog_time_boost');
                            $infoCmd->setType('info');
                            $infoCmd->setUnite('Min');
                            $infoCmd->setSubType('numeric');
                            $infoCmd->setEqLogic_id($this->getId());
							$infoCmd->setOrder(65);
                            $infoCmd->setIsHistorized(0);
                            $infoCmd->setIsVisible(0);
                            $infoCmd->save();
                        }
                      
                        $cmd = $this->getCmd(null, 'derog_boost');
                        if (!is_object($cmd)) {
                            $cmd = new heatzyCmd();
                            $cmd->setLogicalId('derog_boost');
                            $cmd->setName(__('Derogation Boost', __FILE__));
                            $cmd->setType('action');
                            $cmd->setSubType('slider');
                            $cmd->setConfiguration('infoName', 'derog_time_boost');
                            $cmd->setConfiguration('value', "#slider#");
                            $cmd->setConfiguration('minValue', 0);  // valeur minimale
                            $cmd->setConfiguration('maxValue', 120); // valeur maximale
                            $cmd->setValue($infoCmd ->getId());  
							$cmd->setOrder(66);
                          	$cmd->setDisplay('parameters', ['step' => 30]);
                            $cmd->setEqLogic_id($this->getId());
                            $cmd->setIsHistorized(0);
                            $cmd->setIsVisible(1);
                            $cmd->save();
                        }
                    } // if VerifOrdreAccepte
                }
              
                /// Creation de la commande derog_presence (3)
                $cmd = $this->getCmd(null, 'derog_presence');
                if (!is_object($cmd)) {
                    if( $this->VerifOrdreAccepte( $aDevice , 'derog_mode' , 3 , '' , '' , false , true ) ){
                      
                        $cmd = $this->getCmd(null, 'derog_presence');
                        if (!is_object($cmd)) {                      
                            $cmd = new heatzyCmd();
                            $cmd->setLogicalId('derog_presence');
                            $cmd->setName(__('Derogation Présence', __FILE__));
                            $cmd->setType('action');
                            $cmd->setSubType('other');
                            $cmd->setConfiguration('infoName', 'derog_mode');
                            $cmd->setEqLogic_id($this->getId());
							$cmd->setOrder(67);
                            $cmd->setIsHistorized(0);
                            $cmd->setIsVisible(1);
                            $cmd->save();
                        }
                    
                        // Creation de la commande derog_presence
                        // uniquement si mode derog_presence actif
                        $cmd = $this->getCmd(null, 'detect_presence');
                        if (!is_object($cmd)) {
                            $cmd = new heatzyCmd();
                            $cmd->setName(__('Détéction Présence', __FILE__));
                            $cmd->setLogicalId('detect_presence');
                            $cmd->setType('info');
                            $cmd->setSubType('binary');
                            $cmd->setEqLogic_id($this->getId());
							$cmd->setOrder(68);
                            $cmd->setIsHistorized(0);
                            $cmd->setIsVisible(1);
                            $cmd->save();
                        }
                    } // if VerifOrdreAccepte
                } // if !derog_presence
            } // if !derog_mode
        } // if derog_mode
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

        if( !cache::exist('Heatzy_Synchronize') ) cache::set( 'Heatzy_Synchronize' , 0) ;
      
      	// Si Synchronise depuis plus de 15min, on rénit (peut arriver si plantage dans synchronize)
      	if( (strtotime(date("Y-m-d H:i:s")) - cache::byKey('Heatzy_Synchronize')->getValue()) > 900 && cache::byKey('Heatzy_Synchronize')->getValue() > 0 ){
            log::add('heatzy', 'debug',  __METHOD__.': Réinit du cache Heatzy_Synchronize car > 600s (='.cache::byKey('Heatzy_Synchronize')->getValue().')' ) ;
            cache::set( 'Heatzy_Synchronize' , 0) ;
        }
      
      	//Si synchro en cours, on arrête
        if( cache::byKey('Heatzy_Synchronize')->getValue() > 0){
            log::add('heatzy', 'debug',  __METHOD__.': Arret du cron car Synchronize en cours ...' ) ;
            return false ;
        }
      
        $ExpireToken = config::byKey('ExpireToken','heatzy','none') ;
        $ExpireTokenTime = strtotime( $ExpireToken ) ;

        if( $ExpireTokenTime <= time() ){ // Si token expiré
            log::add('heatzy', 'debug',  __METHOD__.': Token expiré ('.$ExpireToken.') - Récupération d un nouveau token' );

            if ( heatzy::Login() ){
                $ExpireToken = config::byKey('ExpireToken','heatzy','none') ;
                $ExpireTokenTime = strtotime( $ExpireToken ) ;
                log::add('heatzy', 'debug',  __METHOD__.': Récupération du token OK ('.$ExpireToken.')' );
            }
            else{
                log::add('heatzy', 'debug',  __METHOD__.': Récupération token KO' );	
                return false ;
            }
        }         
        
        // Mise à jour du statut (Online/Offline + ajout noueau modules)
        // Toutes les 5 min par défaut
        if( (date("i") % config::byKey('Freq_status','heatzy','5') ) == 0 ){
          	// Le synchronize permet d'aouter les nouveaux modules rattachés et de vérifier le statut online/offline
            $res = heatzy::Synchronize() ;
            log::add('heatzy', 'debug',  __METHOD__.': Synchronize cron5 = '.$res );
            
            // Le synchronize contient déjà un update (donc pas la peine d'aller plus loin)
          	return true ;
        }
      
      	// Gestion du max erreur (passage en mode dégradé en cas de cumul d'erreur d'appel API)
        if( !cache::exist('Heatzy_CptError') ){ // Init si non existant
            cache::set( 'Heatzy_CptError' , 0) ;
            log::add('heatzy', 'debug',  __METHOD__.': INIT cache' );
        }
        if( cache::byKey('Heatzy_CptError')->getValue() > HttpGizwits::$_MaxError){
          	// Dépassement de la limite
            // Le retour à létat normal se fait via l'updateHeatzyDid après la mise à jour du statut (Synchronize)          	
            log::add('heatzy', 'debug',  __METHOD__.': cache::Compteur erreur > '.HttpGizwits::$_MaxError.' - Mode dégradé' );
            return false ;      	
        } 
      
        // Mise à jour des commandes infos
        // Toutes les 1 min par défaut
        if( (date("i") % config::byKey('Freq_value','heatzy','1') ) == 0 ){ // Toutes les 1 min par défaut
            
            foreach (eqLogic::byType('heatzy') as $heatzy) {

                $Cmd = $heatzy->getCmd('info', 'IsOnLine') ;
                $IsOnLine = (is_object($Cmd)) ? $Cmd->execCmd() : 1 ;
              
              	// Execute le refresh si
                //  - Eqlogic actif
                //  - Eqlogic connecté (online)
                //  - Token pas prêt d'expirer
                //  - Pas de synchro en cours
                if($heatzy->getIsEnable() == 1 && $IsOnLine == 1 && ($ExpireTokenTime - 120) > time() && cache::byKey('Heatzy_Synchronize')->getValue() == 0){
                    $Cmd = heatzyCmd::byEqLogicIdCmdName($heatzy->getId(), 'Rafraichir' );
                    if (! is_object($Cmd)) {
                        log::add('heatzy', 'error',  ' La commande :refresh n\'a pas été trouvé' );
                        throw new Exception(__(' La commande refresh n\'a pas été trouvé ', __FILE__));
                    }

                    $Cmd->execCmd();

                    $mc = cache::byKey('heatzyWidgetmobile' . $heatzy->getId());
                    $mc->remove();
                    $mc = cache::byKey('heatzyWidgetdashboard' . $heatzy->getId());
                    $mc->remove();

                    $heatzy->toHtml('mobile');
                    $heatzy->toHtml('dashboard');
                    $heatzy->refreshWidget();
                }
            } // foreach
        }
    }

    /**
    * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
    * synchronisation
    */
    /*
    public static function cron5() {        
        $res = heatzy::Synchronize() ;
        log::add('heatzy', 'debug',  __METHOD__.': Synchronize cron5 = '.$res );
    }*/
  
  
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
        $aujourdhui =  strtotime( date(  "Y-m-d H:i:s" ) ) ;
        $cible = $tmp = strtotime( "2025-06-25 00:00:00" ) ; 
        //message::add("Heatzy", '= '.($cible-(7*86400)).' - '.date('w', $aujourdhui).' - '.$aujourdhui.' - '.date('d/m', $aujourdhui) );
        if( $aujourdhui > $cible ){
            message::add("Heatzy", 'cronDaily heatzy du '.date('d/m w', $aujourdhui).' : date cible dépassée 25/06' );
        }
        else if( $aujourdhui > ($cible - (7 * 86400) ) ){
            message::add("Heatzy", 'cronDaily heatzy du '.date('d/m w', $aujourdhui).' : tous les jours 25/06-7j' );
        }
        else if( $aujourdhui > ($cible - (15 * 86400) ) ){
            if( (date('w', $aujourdhui )) == '4' )
                message::add("Heatzy", 'cronDaily heatzy du '.date('d/m w', $aujourdhui)." : 4 - =Jeudi - 25/06-15j" );
            else
                message::add("Heatzy", 'cronDaily heatzy du '.date('d/m w', $aujourdhui)." : != Jeudi - 25/06-15j" );
        }
        else if( $aujourdhui > $cible ){
            message::add("Heatzy", 'cronDaily heatzy du '.date('d/m w', $aujourdhui).' : date cible dépassée 25/06' );
        }
        else
            message::add("Heatzy", 'cronDaily heatzy du '.date('d/m w', $aujourdhui).' : Pas encore' );
        */
        
        $aujourdhui =  strtotime( date(  "Y-m-d H:i:s" ) ) ;
        $cible = strtotime( "2025-07-01 00:00:00" ) ;         
        if( $aujourdhui > $cible && (date('w', $aujourdhui )) == '0' ){
          	// Vérification de l'utlisation des anciens templates
          	$eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
			foreach ($eqLogics as $eqLogic) {
              //log::add('heatzy', 'debug',  __METHOD__.' : '.$eqLogic->getName().' - TypeTemplate='.$eqLogic->getConfiguration('TypeTemplate', '') );
              if( $eqLogic->getConfiguration('TypeTemplate', '') == 1){
                message::add("Heatzy", 'Pour certains équipements heatzy, vous utilisez d ancien template -l3flo-. Ce dernier n est plus maintenu et sera supprimé dans une prochaine version. Je vous suggère de basculer vos équipements sur le template bodbod ou jeedom (parametre accessible dans l onglet -parametres- de chaque équipement)' );
              	break;
              }
            } // foreach

          
          	// On cherche leplugin heatzy pour vérifier l'origine de l'installation
            foreach (update::all() as $update) {
                if ($update->getLogicalId() == 'heatzy'){
            		if( $update->getSource()  != 'market' ){
                        message::add("Heatzy", 'Votre plugin HEATZY a été installé depuis une version autre que le market (github ou fichier). La version officielle du plugin HEATZY a été mise à jour sur le market il y a peu. Je vous invite à aller sur le market et réinstaller le pugin HEATZY. Votre configuration (compte, appareils et commandes) sera conservée. Merci' );
                    	break;
                	} //if
            	} //if
            } //foreach
        } //if
        
        //heatzy::Login();
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
     *         Creation de la commande refresh
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
            $refresh->setOrder(0);
            $refresh->setIsHistorized(0);
            $refresh->setIsVisible(1);
            $refresh->save();
        }
      
        /// Creation de la commande OnLine (pour savoir si le omdule est toujours connecté ou rattaché au compte
        $cmd = $this->getCmd(null, 'IsOnLine'); 
        if (!is_object($cmd)) {
            $cmd = new heatzyCmd();
            $cmd->setName(__('Online', __FILE__));
            $cmd->setLogicalId('IsOnLine');
            $cmd->setType('info');
            $cmd->setSubType('binary');
            $cmd->setEqLogic_id($this->getId());
			$cmd->setOrder(1);
            $cmd->setIsHistorized(0);
            $cmd->setIsVisible(0);
            $cmd->save();
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
                    $replace['#'.$cmd->getLogicalId().'_history#'] = ($cmd->getIsHistorized()) ? 'history cursor' : '';
                    $replace['none;#'.$cmd->getLogicalId().'_display#'] = ($cmd->getIsVisible() && $cmd->execCmd() != -99) ? '#'.$cmd->getLogicalId().'_display#' : 'none;';
                    break;
                case 'action':
                    //log::add('heatzy', 'debug',  __METHOD__.' : Name='.$this->getName().' - CmdId='.$cmd->getLogicalId().' - CmdName='.$cmd->getName().' - CmdType='.$cmd->getType());
				
                	//$replace['#'.$cmd->getLogicalId().'_cmd#'] = $cmd->getConfiguration('Value') ;
                	//$replace['#'.$cmd->getLogicalId().'_unite#'] = $cmd->getUnite();
                
                    $replace['#'.$cmd->getLogicalId().'_id#'] = is_object($cmd) ? $cmd->getId() : '';
                    $replace['none;#'.$cmd->getLogicalId().'_display#'] = (is_object($cmd) && $cmd->getIsVisible()) ? '#'.$cmd->getLogicalId().'_display#' : 'none;';
                
                	$replace['#'.$cmd->getLogicalId().'_minValue#'] = $cmd->getConfiguration('minValue') ;
                	$replace['#'.$cmd->getLogicalId().'_maxValue#'] = $cmd->getConfiguration('maxValue') ;
                	 
                	$t_parameters = $cmd->getDisplay('parameters') ;
                	if( is_array( $t_parameters ) ){
                		$replace['#'.$cmd->getLogicalId().'_step#'] = $t_parameters['step'] ;
                	}

                    break;
                default :
                    log::add('heatzy', 'error',  __METHOD__.' : Type de commande ($cmd->getType()='.$cmd->getType().') inconnu');
                    break;
            } // switch
        } //foreach cmd
      
      	// Pour paliere aux erreurs jquery (jquery.min.js?md5=7c…38ccd3edd840d82ee:2 Uncaught Error: Syntax error, unrecognized expression) liés aux id multiples (#xxx# non remplacés)
      	if( $replace['#eco_temp_consigne_id#'] == '' ) $replace['#eco_temp_consigne_id#'] = $this->getId() ;
      	if( $replace['#cft_temp_consigne_id#'] == '' ) $replace['#cft_temp_consigne_id#'] = $this->getId() ;
      	//if( $replace['#derog_vacances_id#'] == '' ) $replace['#derog_vacances_id#'] = $this->getId() ;
		
		//log::add('heatzy', 'debug',  __METHOD__.' : Name='.$this->getName().'- '.var_export($replace, true) );
      
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
      log::add('heatzy', 'debug',  __METHOD__.' : Commande execute : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' ('.$this->getId().')');  
      //var_export($col, true)
      log::add('heatzy', 'debug',  __METHOD__.' : $_options1 : '.$_options ); 
      //log::add('heatzy', 'debug',  __METHOD__.' : $_options2 : '.json_decode($_options, true) ); 
      log::add('heatzy', 'debug',  __METHOD__.' : $_options3 : '.var_export($_options, true) );  
      
      
      $Result = array();
        
        /// Lecture du token
        $UserToken = config::byKey('UserToken','heatzy','none');
      
        if ($this->getLogicalId() == 'refresh') {
            $this->getEqLogic()->updateHeatzyDid($UserToken);
        }
        else if($this->getType() == 'info' ) {
              return $this->getValue();
        }
        else if($this->getType() == 'action' ) {
            
            $eqLogic = $this->getEqLogic();
            //log::add('heatzy', 'debug',  __METHOD__.' : '.$eqLogic->getName().' - LogicalId='.$this->getLogicalId().' ('.$this->getId().')');
            
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
            else if ($this->getLogicalId() == 'derog_off') {
                $Consigne = array( 'attrs' => array ( 'derog_mode' => 0 )  ); // 0 : pas de dérogation
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'derog_vacances') {
              	isset( $_options['slider'] ) ? $delai = intval( $_options['slider'] ) : $delai = 0 ;
                $Consigne = array( 'attrs' => array ( 'derog_mode' => 1 , 'derog_time' => $delai , 'mode' => 'fro' )  ); // 1 : mode vacances
                $ForUpdate = $delai ;
            }
            else if ($this->getLogicalId() == 'derog_boost') {
              	isset( $_options['slider'] ) ? $delai = intval( $_options['slider'] ) : $delai = 0 ;
                $Consigne = array( 'attrs' => array ( 'derog_mode' => 2 , 'derog_time' => $delai )  ); // 2 : mode boost
                $ForUpdate = $delai ;
            }
            else if ($this->getLogicalId() == 'derog_presence') {
                $Consigne = array( 'attrs' => array ( 'derog_mode' => 3 )  ); // 3 : détection de presence
                $ForUpdate = 3 ;
            }
            else if ($this->getLogicalId() == 'derog_time') {
                $Consigne = array( 'attrs' => array ( 'derog_time' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'cft_temp_consigne') {
                //log::add('heatzy', 'debug', __METHOD__.' '.$this->getLogicalId() . ' ForUpdate - '.$this->getConfiguration('infoName').'=>'.$ForUpdate );
                //$this->getConfiguration('tempHL',false)
              	isset( $_options['slider'] ) ? $consigne = floatval( $_options['slider'] ) : $consigne = 0 ;

              	log::add('heatzy', 'debug', __METHOD__.' '.$this->getLogicalId() . ' $consigne='.$consigne );     
              	if( $this->getConfiguration('tempHL',false) ){
                    $tempBIN = str_pad( decbin($consigne * 10),  16, "0", STR_PAD_LEFT) ;
                    $tempH = bindec(substr( $tempBIN , 0 , 8 )) ;
                    $tempL = bindec(substr( $tempBIN , 8 )) ;
                    //log::add('heatzy', 'debug', __METHOD__.' '.$this->getLogicalId() . ' $tempBIN='.$tempBIN.'-'.$tempH.'-'.$tempL.'-'.bindec($tempH).'-'.bindec($tempL) );                  
                    $Consigne = array( 'attrs' => array ( 'cft_tempH' => $tempH , 'cft_tempL' => $tempL )  );
                }
                else{
                    $Consigne = array( 'attrs' => array ( 'cft_temp' => $consigne * 10 )  );
                }
                $ForUpdate = $consigne ;
            }
            else if ($this->getLogicalId() == 'eco_temp_consigne') {
              	isset( $_options['slider'] ) ? $consigne = floatval( $_options['slider'] ) : $consigne = 0 ;
              
              	if( $this->getConfiguration('tempHL',false) ){
                    $tempBIN = str_pad( decbin($consigne * 10),  16, "0", STR_PAD_LEFT) ;
                    $tempH = bindec(substr( $tempBIN , 0 , 8 )) ;
                    $tempL = bindec(substr( $tempBIN , 8 )) ;
                    //log::add('heatzy', 'debug', __METHOD__.' '.$this->getLogicalId() . ' $tempBIN='.$tempBIN.'-'.$tempH.'-'.$tempL.'-'.bindec($tempH).'-'.bindec($tempL) );                  
                    $Consigne = array( 'attrs' => array ( 'eco_tempH' => $tempH , 'eco_tempL' => $tempL )  );
                }
                else{
                    $Consigne = array( 'attrs' => array ( 'eco_temp' => $consigne * 10 )  );
                }
                $ForUpdate = $consigne ;
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
              	log::add('heatzy', 'debug',  __METHOD__.' :$Consigne != null : ');
                $Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
                if($Result === false) {
                    log::add('heatzy', 'error',  __METHOD__.' : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' - impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                    return false;
                }
                else{
                    /// Si une erreur de communication
                    if(isset($Result['error_message']) && isset($Result['error_code'])) {
                        log::add('heatzy', 'error',  __METHOD__.' : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' - '.$Result['error_code'].' - '.$Result['error_message'].' - '.$Result['detail_message']);
                      
                      	if( $Result['error_code'] == '9017' || $Result['error_code'] == '9042' ){
                          	// 9017 = Détaché du compte
                          	// 9042 = Offline
                          	$eqLogic->setStatus('timeout','1');
                            $eqLogic->checkAndUpdateCmd('IsOnLine', 0 );
                        }
                        return false;
                    }
                    else if($ForUpdate != ''){
                      	log::add('heatzy', 'debug', __METHOD__.' '.$this->getLogicalId() . ' ForUpdate - '.$this->getConfiguration('infoName').'=>'.$ForUpdate );
                        $eqLogic->checkAndUpdateCmd( $this->getConfiguration('infoName') , $ForUpdate ) ;
                        $eqLogic->checkAndUpdateCmd('IsOnLine', 1 );
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
		
  		return true;
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>