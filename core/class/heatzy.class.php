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
         
            if(empty($User) || empty($Passwd))
                {
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

            if( $httpcode == 500 )
              {
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
        
        if(empty($ProductKey))
            {
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

        if( $httpcode == 500 )
              {
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
        
        if(empty($UserToken))
            {
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

        if( $httpcode == 500 )
              {
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
    
        if(empty($UserToken) || empty($Did))
            {
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

        if( $httpcode == 500 )
              {
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
    
        if(empty($UserToken) || empty($Did) || empty($Id) || empty($Param))
            {
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

        if( $httpcode == 500 )
              {
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
    
        if(empty($UserToken) || empty($Did) || empty($DevAlias))
            {
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

        if( $httpcode == 500 )
              {
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
        
        if(empty($UserToken) || empty($Did) || empty($Consigne))
            {
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

        if( $httpcode == 500 )
              {
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
        
        if(empty($Did))
            {
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

        if( $httpcode == 500 )
              {
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
    
        if(empty($Did))
            {
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

        if( $httpcode == 500 )
              {
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
    public static $_HeatzyMode = array('Confort', 'Eco', 'HorsGel', 'Off');
    
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
                if($aDevice['error_code'] === '9004') {
                    log::add('heatzy', 'error',  __METHOD__.' : '.$aDevice['error_code'].' '.$aDevice['error_message']);
                    $Nb = $eqLogic->Synchronize();
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

        if(isset($aDevice['attr']['mode'])) {
          
            if( $aDevice['attr']['mode'] == 'cft' ) {  /// Confort
                $KeyMode = 'Confort';
            }
            else if( $aDevice['attr']['mode'] == 'eco' ) { /// Eco
                $KeyMode = 'Eco';
            }
            else if( $aDevice['attr']['mode'] == 'fro' ) { /// HorsGel
                $KeyMode = 'HorsGel';
            }
            else if( $aDevice['attr']['mode'] == 'stop' ) { /// Off
                $KeyMode = 'Off';
            }
            else {                                            /// Premiere version du module pilote
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
          
          if( isset ($aDevice['attr']['on_off']) && $this->getConfiguration('product', '') == 'Flam_Week2')
              $this->checkAndUpdateCmd('plugzy', $aDevice['attr']['on_off'] );
          
          if( isset ($aDevice['attr']['eco_tempH']) && isset ($aDevice['attr']['eco_tempL']) )
              $this->checkAndUpdateCmd('eco_temp', floatval( bindec(str_pad(decbin($aDevice['attr']['eco_tempH']),  8, "0", STR_PAD_LEFT).str_pad(decbin($aDevice['attr']['eco_tempL']),  8, "0", STR_PAD_LEFT))) / 10 );
          
          if( isset ($aDevice['attr']['cft_tempH']) && isset ($aDevice['attr']['cft_tempL']) )
              $this->checkAndUpdateCmd('cft_temp', floatval( bindec(str_pad(decbin($aDevice['attr']['cft_tempH']),  8, "0", STR_PAD_LEFT).str_pad(decbin($aDevice['attr']['cft_tempL']),  8, "0", STR_PAD_LEFT))) / 10 );
          
          if( isset ($aDevice['attr']['cur_tempH']) && isset ($aDevice['attr']['cur_tempL']) )
              $this->checkAndUpdateCmd('cur_temp', floatval( bindec(str_pad(decbin($aDevice['attr']['cur_tempH']),  8, "0", STR_PAD_LEFT).str_pad(decbin($aDevice['attr']['cur_tempL']),  8, "0", STR_PAD_LEFT))) / 10 );
          
          if( isset ($aDevice['attr']['timer_switch']) )
          		$this->checkAndUpdateCmd('etatprog', $aDevice['attr']['timer_switch'] );

	  if( isset ($aDevice['attr']['lock_switch']) )
          		$this->checkAndUpdateCmd('etatlock', $aDevice['attr']['lock_switch'] );
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
        $this->checkAndUpdateCmd('etat', $aKeyVal[0]);
        $this->checkAndUpdateCmd('mode', $KeyMode);
        return true;
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
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDayly() {

      }
     */



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
     *         Creation de la commande info Etat
     */
    
    public function postSave() {
        
        foreach (self::$_HeatzyMode as $Key => $Mode ) {
            /// Creation de la commande action $Mode : $Key
            $cmd = $this->getCmd(null, $Mode);
            if (!is_object($cmd)) {
                log::add('heatzy', 'debug',  $this->getLogicalId().' creation commande :'.$Key.'=>'.$Mode);
                $cmd = new heatzyCmd();
                $cmd->setLogicalId($Mode);
                $cmd->setIsVisible(1);
                $cmd->setName(__($Mode, __FILE__));
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setConfiguration('infoName', 'Etat');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setIsHistorized(0);
                $cmd->setIsVisible(1);
                $cmd->save();
            }
        }
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

        /// Creation de la commande info Etat numeric
        $etat = $this->getCmd(null, 'Etat');
        if (!is_object($etat)) {
            $etat = new heatzyCmd();
            $etat->setName(__('Etat', __FILE__));
            $etat->setLogicalId('etat');
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
        
    		if ( $this->getConfiguration('product', '') == 'Flam_Week2' ) {
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
          }
        
        if( $this->getConfiguration('product', '') == 'Flam_Week2' ||
            $this->getConfiguration('product', '') == 'INEA') {    /// Pour heatzy flam ou inea
          
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
        
        $replace['#collectDate#'] = $this->getConfiguration('updatetime', '');
     
        $refresh = $this->getCmd(null, 'refresh');
        $replace['#refresh_id#'] = is_object($refresh) ? $refresh->getId() : '';
        
        $Etat = $this->getCmd(null,'Etat');
        $replace['#Etat#'] = (is_object($Etat)) ? $Etat->execCmd() : '';
        $replace['#Etatid#'] = (is_object($Etat)) ? $Etat->getId() : '';
        $replace['#Etat_display#'] = (is_object($Etat) && $Etat->getIsVisible()) ? '#Etat_display#' : 'none';
        $replace['#history#'] = (is_object($Etat) && $Etat->getIsHistorized())? 'history cursor' : '';
        
        $Confort = $this->getCmd(null,'Confort');
        $replace['#cmd_confort_id#'] = (is_object($Confort)) ? $Confort->getId() : '';
        
        $Eco = $this->getCmd(null,'Eco');
        $replace['#cmd_eco_id#'] = (is_object($Eco)) ? $Eco->getId() : '';
        
        $HorsGel = $this->getCmd(null,'HorsGel');
        $replace['#cmd_hg_id#'] = (is_object($HorsGel)) ? $HorsGel->getId() : '';
        
        $Off = $this->getCmd(null,'Off');
        $replace['#cmd_off_id#'] = (is_object($Off)) ? $Off->getId() : '';

        $Etat = $this->getCmd(null,'etatprog');
        $replace['#info_prog#'] = (is_object($Etat)) ? $Etat->execCmd() : '';
        $replace['#cmd_prog_id#'] = (is_object($Etat)) ? $Etat->getId() : '';
      
        $ProgOff = $this->getCmd(null,'ProgOff');
        $replace['#cmd_progoff_id#'] = (is_object($ProgOff)) ? $ProgOff->getId() : '';
      
        $ProgOn = $this->getCmd(null,'ProgOn');
        $replace['#cmd_progon_id#'] = (is_object($ProgOn)) ? $ProgOn->getId() : '';

        $Etat = $this->getCmd(null,'etatlock');
        $replace['#info_lock#'] = (is_object($Etat)) ? $Etat->execCmd() : '';
        $replace['#cmd_lock_id#'] = (is_object($Etat)) ? $Etat->getId() : '';
      
        $LockOff = $this->getCmd(null,'LockOff');
        $replace['#cmd_lockoff_id#'] = (is_object($LockOff)) ? $LockOff->getId() : '';
      
        $LockOn = $this->getCmd(null,'LockOn');
        $replace['#cmd_lockon_id#'] = (is_object($LockOn)) ? $LockOn->getId() : '';    
      
        if( $product == 'Flam_Week2' 
         || $product == 'INEA') {     /// Pour heatzy flam ou inea mais par defaut le pilote

            if($product == 'Flam_Week2') {
	            $plugzy = $this->getCmd(null,'plugzy');
	            $replace['#info_plugzy#'] = (is_object($plugzy)) ? $plugzy->execCmd() : '';
	            $replace['#cmd_plugzy_id#'] = (is_object($plugzy)) ? $plugzy->getId() : '';
	
	            $plugzyon = $this->getCmd(null,'plugzyon');
	            $replace['#cmd_plugzyon_id#'] = (is_object($plugzyon)) ? $plugzyon->getId() : '';
	
	            $plugzyoff = $this->getCmd(null,'plugzyoff');
	            $replace['#cmd_plugzyoff_id#'] = (is_object($plugzyoff)) ? $plugzyoff->getId() : '';
 			     }
            $CurTemp = $this->getCmd(null,'cur_temp');
            if( is_object($CurTemp)) {
                $replace['#history_cur_temp#'] = ($CurTemp->getIsHistorized())? 'history cursor' : '';
              
                $replace['#cur_temp_id#'] = $CurTemp->getId();
                $replace['#cur_temp#'] = $CurTemp->execCmd();
                $replace['#unite_cur_temp#'] = $CurTemp->getUnite();
                }
          
            $EcoTemp = $this->getCmd(null,'eco_temp');
            if( is_object($EcoTemp)) {
              $replace['#history_eco_temp#'] = ($CurTemp->getIsHistorized())? 'history cursor' : '';

              $replace['#eco_temp_id#'] = $EcoTemp->getId();
              $replace['#eco_temp#'] = $EcoTemp->execCmd();
              $replace['#unite_eco_temp#'] = $EcoTemp->getUnite();
            }

            $CftTemp = $this->getCmd(null,'cft_temp');
            if( is_object($CftTemp)) {
              $replace['#history_cft_temp#'] = ($CurTemp->getIsHistorized())? 'history cursor' : '';

              $replace['#cft_temp_id#'] = $CftTemp->getId();
              $replace['#cft_temp#'] = $CftTemp->execCmd();
              $replace['#unite_cft_temp#'] = $CftTemp->getUnite();
            }    
        }
        $html = template_replace($replace, getTemplate('core', $_version, $product,'heatzy'));
       // cache::set('heatzy' . $_version . $this->getId(), $html, 0);
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
            
            /// Lecture du token
            $UserToken = config::byKey('UserToken','heatzy','none');
          
            if ($this->getLogicalId() == 'plugzyon') {
        
                $Consigne = array( 'attrs' => array ( 'on_off' => 1 )  );

                $Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
                if($Result === false) {
                    log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                    return false;
                }
                else
                	$eqLogic->checkAndUpdateCmd($this->getConfiguration('infoName'), 1);
            }
            else if ($this->getLogicalId() == 'plugzyoff') {
              
                $Consigne = array( 'attrs' => array ( 'on_off' => 0 )  );
              
                $Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
                if($Result === false) {
                    log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                    return false;
                }
              	else 
                	$eqLogic->checkAndUpdateCmd($this->getConfiguration('infoName'), 0);
            }
            else if ($this->getLogicalId() == 'ProgOn') {
            	
            	if( $eqLogic->getConfiguration('product', '') == 'Heatzy' ||
            			$eqLogic->getConfiguration('product', '') == 'Flam_Week2')
                $eqLogic->GestProg(true);
              else {
              	$Consigne = array( 'attrs' => array ( 'timer_switch' => 1 )  );
              	
              	$Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
              	if($Result === false) {
              		log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
              		return false;
              	}
              }
              $eqLogic->checkAndUpdateCmd($this->getConfiguration('infoName'), 1);
            }
            else if ($this->getLogicalId() == 'ProgOff') {
            	
							if( $eqLogic->getConfiguration('product', '') == 'Heatzy' ||
            			$eqLogic->getConfiguration('product', '') == 'Flam_Week2')
                $eqLogic->GestProg(false);
              else {
              	$Consigne = array( 'attrs' => array ( 'timer_switch' => 0 )  );
              	
              	$Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
              	if($Result === false) {
              		log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
              		return false;
              	}
              }
              $eqLogic->checkAndUpdateCmd($this->getConfiguration('infoName'), 0);
            }
	    else if ($this->getLogicalId() == 'LockOn') {
            	
            	if( $eqLogic->getConfiguration('product', '') == 'Heatzy' ||
            			$eqLogic->getConfiguration('product', '') == 'Flam_Week2')
                $eqLogic->GestProg(true);
              else {
              	$Consigne = array( 'attrs' => array ( 'lock_switch' => 1 )  );
              	
              	$Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
              	if($Result === false) {
              		log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
              		return false;
              	}
              }
              $eqLogic->checkAndUpdateCmd($this->getConfiguration('infoName'), 1);
            }
            else if ($this->getLogicalId() == 'LockOff') {
            	
							if( $eqLogic->getConfiguration('product', '') == 'Heatzy' ||
            			$eqLogic->getConfiguration('product', '') == 'Flam_Week2')
                $eqLogic->GestProg(false);
              else {
              	$Consigne = array( 'attrs' => array ( 'lock_switch' => 0 )  );
              	
              	$Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
              	if($Result === false) {
              		log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
              		return false;
              	}
              }
              $eqLogic->checkAndUpdateCmd($this->getConfiguration('infoName'), 0);
            }
            else {

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
                        }
                  
                    $Consigne = array( 'attrs' => array ( 'mode' => $Mode )  );
                }
                $Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
                if($Result === false) {
                    log::add('heatzy', 'error',  __METHOD__.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                    return false;
                }
            }/// Le mode
            
            /// Si une erreur de communication et token invalide on se re-synchronise
            if(isset($Result['error_message']) && isset($Result['error_code'])) {
            	if($Result['error_code'] === '9004') {
            		log::add('heatzy', 'error',  __METHOD__.' : '.$Result['error_code'].' '.$Result['error_message']);
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
            				log::add('heatzy', 'error',  __METHOD__.' : '.$Result['error_code'].' - '.$Result['error_message']);
            				return false;
            			}
            		}
            	}
            	else {
            		log::add('heatzy', 'error',  __METHOD__.' : '.$Result['error_code'].' - '.$Result['error_message']);
            		return false;
            	}
            }
            
            /// Mise à jour de l'état
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
