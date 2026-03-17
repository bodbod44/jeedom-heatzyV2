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

/* xxxxxxxxxxx bodbod */
$s = require_once('heatzyHttpGizwits.class.php');
if( $s != 1 ) 
	log::add('heatzy', 'error', __METHOD__.'(ln '.__LINE__.')'.' : error require_once='.$s);

/* xxxxxxxxxxx bodbod */
$s = require_once('heatzySynchro.class.php');
if( $s != 1 ) 
	log::add('heatzy', 'error', __METHOD__.'(ln '.__LINE__.')'.' : error require_once='.$s);

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
//class heatzy extends eqLogic
    public static $_HeatzyMode = array('Confort', 'Eco', 'HorsGel', 'Off','Confort-1','Confort-2');

    /**
     * @brief La fonction deamon_info() sera appelée par le core lors de l’affichage du cadre suivant dans la page de configuration de votre plugin
     */
//class heatzy extends eqLogic
    public static function deamon_info() {
        $return = array();
        $return['log'] = __CLASS__;
        $return['state'] = 'nok';
        $pid_file = jeedom::getTmpFolder(__CLASS__) . '/deamon.pid';
        if (file_exists($pid_file)) {
            if (@posix_getsid(trim(file_get_contents($pid_file)))) {
                $return['state'] = 'ok';
            } else {
                shell_exec(system::getCmdSudo() . 'rm -rf ' . $pid_file . ' 2>&1 > /dev/null');
            }
        }
        $return['launchable'] = 'ok';

        $email = config::byKey('email', __CLASS__ , '');
        $password = config::byKey('password', __CLASS__ , '');
        $UserToken = config::byKey('UserToken', __CLASS__ , '');
        $ExpireToken = config::byKey('ExpireToken', __CLASS__ , '');
        $socketport = config::byKey('socketport', __CLASS__ , '55099');
        $uid = config::byKey('uid', __CLASS__ , '');

        if( config::byKey('API_Type','heatzy','REST') == 'REST') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le type de conexion n\'est pas Websocket (donc pas besoin du demon)', __FILE__);
        } elseif( config::byKey('email', __CLASS__ , '') == '') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('L\'email du compte Heatzy n\'est pas configuré', __FILE__);
        } elseif( config::byKey('password', __CLASS__ , '') == '') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le mot de passe du compte n\'est pas configuré', __FILE__);
        } elseif( config::byKey('UserToken', __CLASS__ , '') == '') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le token n\'a pas été récupéré (relancez une synchronisation)', __FILE__);
        } elseif( strtotime( config::byKey('ExpireToken', __CLASS__ , '') ) <= time() ) {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le token est expiré (relancez une synchronisation)', __FILE__);
        } elseif( !is_numeric( config::byKey('socketport', __CLASS__ , '55099') ) ) {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Port demon vide ou mal alimenté (essayer un port numérique comme 550xx)', __FILE__);
        } elseif( config::byKey('uid', __CLASS__ , '') == '' ) {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('uid non valorisé (relancez une synchronisation)', __FILE__);
        }
      
		//log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' : '.$return['state'].'-'.$return['launchable'] );
      
      	if( $return['state'] == 'ok'){
            if( $return['launchable'] == 'nok' ){
                // Si le demon est déjà lancé alors que les parametres ne sont plus reunis (modif après lancement)
                log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' : envoi stop au demon...');
                self::sendToDaemon( 'stop' , '' , array() ) ;
                //sleep(1);
            }
            else if(  $return['state'] == 'ok' && log::convertLogLevel(log::getLogLevel(__CLASS__)) != config::byKey('DemonLogLevel', __CLASS__ , '') ){
                // Si niveau de log changé depuis lancement du demon
                log::add('heatzy', 'info', __METHOD__.'(ln '.__LINE__.')'.' : Niveau de log different entre plugin et demon. Relance du demon');
                self::sendToDaemon( 'stop' , '' , array() ) ;
            }
        }
      
        return $return;
    }
    
    /**
     * @brief La fonction deamon_start() est comme son nom l’indique la méthode qui sera appelée par le core pour démarrer votre démon
     */
//class heatzy extends eqLogic
    public static function deamon_start() {
        self::deamon_stop();
      	sleep(1) ;
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }
        $path = realpath(dirname(__FILE__) . '/../../resources/heatzyd');
        $cmd = system::getCmdPython3(__CLASS__) . " {$path}/heatzyd.py";
        $cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel(__CLASS__));
        $cmd .= ' --socketport ' . config::byKey('socketport', __CLASS__, '55099'); // port par défaut à modifier
        $cmd .= ' --callback ' . network::getNetworkAccess('internal', 'http:127.0.0.1:port:comp') . '/plugins/heatzy/core/php/jeeHeatzy.php'; // chemin de la callback url à modifier (voir ci-dessous)
        $cmd .= ' --apikey ' . jeedom::getApiKey(__CLASS__); // l'apikey pour authentifier les échanges suivants
        $cmd .= ' --pid ' . jeedom::getTmpFolder(__CLASS__) . '/deamon.pid';
        $cmd .= ' --appid '.HttpGizwits::$HeatzyAppId ;
        $cmd .= ' --token '.config::byKey('UserToken', __CLASS__, 'xxx-token-xxx');
        $cmd .= ' --uid '.config::byKey('uid', __CLASS__, 'xxx-uid-xxx');
      
        log::add(__CLASS__, 'info', 'Lancement démon');
        log::add(__CLASS__, 'debug', '$cmd='.$cmd);
        $result = exec($cmd . ' >> ' . log::getPathToLog('heatzy_daemon') . ' 2>&1 &'); // 'template_daemon' est le nom du log pour votre démon
        $i = 0;
        while ($i < 20) {
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                break;
            }
            sleep(1);
            $i++;
        }
        if ($i >= 20) {
            log::add(__CLASS__, 'error', __('Impossible de lancer le démon Heatzy, vérifiez le log', __FILE__), 'unableStartDeamon');
            return false;
        }
        else{
            // Demon démarré
            config::save('DemonLogLevel' , log::convertLogLevel(log::getLogLevel(__CLASS__)) , 'heatzy'); 
        }
        
        message::removeAll(__CLASS__, 'unableStartDeamon');
        return true;
    }

    /**
     * @brief ette méthode sera utilisée pour stopper le démon: on récupère le pid du démon, qui a été écrit dans le “pid_file” et on envoi le kill système au process.
     */
//class heatzy extends eqLogic
    public static function deamon_stop() {
      
        $pid_file = jeedom::getTmpFolder(__CLASS__) . '/deamon.pid'; // ne pas modifier
        if (file_exists($pid_file)) {
            $pid = intval(trim(file_get_contents($pid_file)));
            system::kill($pid);
        }
        system::kill('heatzyd.py'); // nom du démon à modifier
        sleep(1);
      	log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' : demon stoppé');
    }
    /**
     * @Elle reçoit donc en paramètre un tableau de valeur et se charge de l’envoyer au socket du démon qui pourra donc lire ce tableau dans la méthode read_socket().
     */
//class heatzy extends eqLogic
    public static function sendToDaemon( $action , $did , $params) {
        if($action != 'stop'){ // sinon risque de boucle avec info
            $deamon_info = self::deamon_info();
          if ($deamon_info['state'] != 'ok') {
              throw new Exception("Le démon n'est pas démarré");
          }
          //else
          //  log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' : Le démon est démarre');
        }
  
        $message['apikey'] = jeedom::getApiKey(__CLASS__);
        $message['utc_mess']   = strtotime(date('Y-m-d H:i:s') ) ;
        $message['date_mess']  = date('Y-m-d H:i:s') ;
      
        $payLoad = json_encode($params);
        //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' : $payLoad='.$payLoad);
        
		if( $action == 'login' ){
			$params['cmd'] = 'login_req' ;
        } else if( $action == 'read' ){
			$params['cmd'] = 'c2s_read' ;
		} else if( $action == 'execute' ){
          	//{ "cmd": "c2s_write", "data": { "did": "xxxxxxxxxx", "attrs": { "name1": "", "name2": <value2>, } } }
			//$message = '{ "cmd": "c2s_write", "data": { "did": "'.$did.'", "attrs": '.$payLoad.' } }' ;
          	$message['message']['cmd'] = 'c2s_write' ;
          	$message['message']['data']['did'] = $did ;
            $message['message']['data']['attrs'] = $params['attrs'] ;
              //= '{ "cmd": "c2s_write", "data": { "did": "'.$did.'", "attrs": '.$payLoad.' } }' ;
		} else if( $action == 'stop' ){
          	$message['message']['cmd'] = 'stop' ;
          	$message['message']['data']['did'] = $did ;
            $message['message']['data']['attrs'] = $params['attrs'] ;
      	} else if( $action == 'log_level' ){
          	$message['message']['cmd'] = 'log_level' ;
            $message['message']['log_level'] = log::convertLogLevel(log::getLogLevel(__CLASS__)) ;
          	//$message['message']['data']['did'] = $did ;
            //$message['message']['data']['attrs'] = $params['attrs'] ;
      	} else{
          	log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' : $action non trouvé ('.$action.')');
          	return ;
      	}
      
      	log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' : $mess ('.json_encode($message).')');
            
      	$payLoad = json_encode($message) ;
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($socket, '127.0.0.1', config::byKey('socketport', __CLASS__, '55099')); //port par défaut de votre plugin à modifier
        socket_write($socket, $payLoad, strlen($payLoad));
        socket_close($socket);
    }
     
    /**
     * @brief Fonction qui permet de tirer un nouveau token utilisateur
     */
//class heatzy extends eqLogic
    public static function Login() {

        $email = config::byKey('email', 'heatzy', '');
        $password = config::byKey('password', 'heatzy', '');
        
        /// Login
        $aResult = HttpGizwits::Login($email, $password );
        if ($aResult === false) {
            log::add('heatzy', 'warning', __METHOD__.'(ln '.__LINE__.')'.' : impossible de se connecter a: '.HttpGizwits::$UrlGizwits);
            return false;
        }
        log::add('heatzy', 'debug',  '$aResult :'.var_export($aResult, true));
         
        $TokenExpire = date('Y-m-d H:i:s', $aResult['expire_at']);
        $UserToken = $aResult['token'];
        $uid = $aResult['uid'];
        
        if( config::byKey('UserToken', 'heatzy', '') != $UserToken || config::byKey('ExpireToken', 'heatzy', '') != $TokenExpire){
            self::sendToDaemon( 'stop' , '' , array() ) ; // Force le demon a s'eteindre pour un redémarrage avec le nouveau token
            message::add("Heatzy", 'Génération du token heatzy : '.config::byKey('ExpireToken', 'heatzy', '').'/'.config::byKey('UserToken', 'heatzy', '').' -> '.$TokenExpire.'/'.$UserToken);
        }
        //else
        //    message::add("Heatzy", 'Génération du token heatzy -> Pas de changement');
        
        config::save('UserToken'  , $UserToken  , 'heatzy'); /// => Sauvegarde du token utilisateur
        config::save('uid'        , $uid        , 'heatzy'); /// => uid
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
     * @brief Fonction de mise à jour du device did
     */
//class heatzy extends eqLogic
    public function updateHeatzyDid( $aDevice = array() , $force = false ) {

        if( !cache::exist('Heatzy_CptError') ){
            cache::set( 'Heatzy_CptError' , 0) ;
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': INIT cache' );
        }
        //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : cache='.cache::byKey('Heatzy_CptError')->getValue() );
        //if( config::byKey('API_Type','heatzy','REST') != 'REST' ){
        //if(empty($aDevice) && config::byKey('API_Type','heatzy','REST') == 'REST' ) {
        if( empty($aDevice) ) {
            /// Lecture de l'etat
            $aDevice = HttpGizwits::GetConsigne( $this->getLogicalId());
            if($aDevice === false) {
                log::add('heatzy', 'warning',  __METHOD__.'(ln '.__LINE__.')'.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                $this->setStatus('timeout','1');
                $this->save();
                cache::set('Heatzy_CptError', cache::byKey('Heatzy_CptError')->getValue() + 1 );
                return false;
            }
            else if(isset($aDevice['error_message']) && isset($aDevice['error_code'])) {
                log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.')'.' : '.$this->getLogicalId().' - '.$aDevice['error_code'].' - '.$aDevice['error_message'].' - '.$aDevice['detail_message']);
                cache::set('Heatzy_CptError', cache::byKey('Heatzy_CptError')->getValue() + 1 );
                return false;
            }

              // Si pas d'erreur, on réinitialise le garde fou
              cache::set('Heatzy_CptError', 0 );
        } // if empty
      
        /// Mise à jour de la derniere communication
        if(isset($aDevice['updated_at']) && $aDevice['updated_at'] != 0 ) {
            //$this->setStatus('timeout','0');
            $this->setConfiguration('lastCommunication', date('Y-m-d H:i:s', $aDevice['updated_at']));
        }
      
        // Modes de chauffe
        // Note : Théoriquement pilote_pro doit être lu avec cur_mode (mais le retour contient quand même mode)
        if(isset($aDevice['attr']['mode']) || isset($aDevice['attr']['cur_temp']) || isset($aDevice['attr']['derog_mode']) ) {

            // Créer les commandes en fonction du contenu de la réponse
            //$this->CheckAndCreateCmd($aDevice , $force) ;

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
                }/*
                else {
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$this->getLogicalId().' non connecte');
                      $this->checkAndUpdateCmd('IsOnLine', 0 ); 
                      $this->save(); /// Enregistre les info  
                      $this->setStatus('timeout','1');
                    return false;
                }*/
            }
          
        	// Recherche la valeur de la clef du mode courant
        	//log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): Mode '.$KeyMode);
        	$aKeyVal = array_keys(self::$_HeatzyMode, $KeyMode);
        	$this->checkAndUpdateCmd('EtatConsigne', $aKeyVal[0]);
        	$this->checkAndUpdateCmd('mode', $KeyMode);
          
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
                $derog_time = isset ($aDevice['attr']['derog_time']) ? $aDevice['attr']['derog_time'] : 0 ;
                $this->checkAndUpdateCmd('derog_time_vacances', $aDevice['attr']['derog_mode'] == '1' ? $derog_time : 0 );
                $this->checkAndUpdateCmd('derog_time_boost'   , $aDevice['attr']['derog_mode'] == '2' ? $derog_time : 0 );
            }

            // Détéction de présence 
          	// A retravailler pour intégrer la consigne (pour éviter les faux positifs)
            if( $aDevice['attr']['derog_mode'] == 3 && array_keys(self::$_HeatzyMode, $KeyMode)[0] == 0){
                $this->checkAndUpdateCmd('detect_presence', 1 );
            }
            else{
                $this->checkAndUpdateCmd('detect_presence', 0 );
            }
            /*
            // Puissance consommée (en W) 
            if( isset ($aDevice['attr']['cur_power']) )
                $this->checkAndUpdateCmd('cur_power', $aDevice['attr']['cur_power'] );
            
            // Intensité consommée (en A)  
            if( isset ($aDevice['attr']['cur_current']) )
                $this->checkAndUpdateCmd('cur_current', $aDevice['attr']['cur_current'] );
            
            // Tension du réseau (en V) 
            if( isset ($aDevice['attr']['cur_voltage']) )
                $this->checkAndUpdateCmd('cur_voltage', $aDevice['attr']['cur_voltage'] );

            // Puissance du signal 
            if( isset ($aDevice['attr']['signal_power']) )
                $this->checkAndUpdateCmd('signal_power', $aDevice['attr']['signal_power'] );

            // energy_saving 
            if( isset ($aDevice['attr']['energy_saving']) )
                $this->checkAndUpdateCmd('energy_saving', $aDevice['attr']['energy_saving'] );
            
            // kill_switch 
            if( isset ($aDevice['attr']['kill_switch']) )
                $this->checkAndUpdateCmd('kill_switch', $aDevice['attr']['kill_switch'] );
            */
            //TODO $this->CalculExterne( $aDevice ) ;
        }
        else {                                             
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$this->getLogicalId().' non connecte');
            $this->setStatus('timeout','1');
            $this->save(); /// Enregistre les info
            return false;
        }
        
        $this->save(); /// Enregistre les info
      
        $this->toHtml('mobile');
        $this->toHtml('dashboard');
        $this->refreshWidget();
      
        return true;
    }


      /**
     * @brief Fonction qui permet de gerer les parametres et calcul externe a heatzy
     * 
     * @param tableau retour API
     */
//class heatzy extends eqLogic
    public function CalculExterne($aDevice) {
                
        // Prise en compte d'un capteur d'humidité externe
        $CapteurExtHumi = $this->getConfiguration('CapteurExtHumi', '');
          $humi = -99 ;
        if( $CapteurExtHumi != '' ){ // Si capteur externe est bien parametré
            preg_match_all("/#([0-9]*)#/", $CapteurExtHumi, $matches);
            if (count($matches[1]) == 1) { // Si numéro de commande numérique
                $res = cmd::byId( $matches[1][0] )->execCmd() ;
                if( is_numeric($res) ){
                  $humi = $res ;
                  if (!is_object($this->getCmd(null, 'cur_humi')) )
                      // TODO $this->CheckAndCreateCmd( array( "attr" => array( "cur_humi" => "0" ) ) ) ; // Simule la présence de $aDevice['attr']['cur_humi']
                  $this->checkAndUpdateCmd('cur_humi', $humi );
                }
            }
        }
        if( !isset($aDevice['attr']['cur_humi']) && $humi == -99 )
            $this->checkAndUpdateCmd('cur_humi', $humi ); // Sert a valoriser une commande qui serait présente mais obselete (pour montrer un probleme
        
        // Prise en compte d'un capteur de température externe
        $CapteurExtTemp = $this->getConfiguration('CapteurExtTemp', '');
          $Temp = -99 ;
        if( $CapteurExtTemp != '' ){ // Si capteur externe est bien parametré
            preg_match_all("/#([0-9]*)#/", $CapteurExtTemp, $matches);
            if (count($matches[1]) == 1) { // Si numéro de commande numérique
                $res = cmd::byId( $matches[1][0] )->execCmd() ;
                if( is_numeric($Temp) ){
                  $Temp = $res ;
                  if (!is_object($this->getCmd(null, 'cur_temp')) )
                      // TODO $this->CheckAndCreateCmd( array( "attr" => array( "cur_temp" => "0" ) ) ) ; // Simule la présence de $aDevice['attr']['cur_temp']
                  $this->checkAndUpdateCmd('cur_temp', $Temp );
                }
            }
        }
        if( !isset($aDevice['attr']['cur_temp']) && !isset($aDevice['attr']['cur_tempH']) && $Temp == -99 )
            $this->checkAndUpdateCmd('cur_temp', $Temp ); // Sert a valoriser une commande qui serait présente mais obselete (pour montrer un probleme
      
        
        // Calcul de la tendance de température
          // Detecte si une fenetre est ouverte (basé sur la tendance (chute de température)
        $TendanceDegre = $this->getConfiguration('TendanceDegre', '2');
        $TendanceDuree = $this->getConfiguration('TendanceDuree', '5');
        $cur_temp = $this->getCmd(null, 'cur_temp');

          
        if ( is_object($cur_temp) && is_numeric($TendanceDegre) && is_numeric($TendanceDuree) ){
            $Temp = $cur_temp->execCmd() ;
            if( $Temp > -50 && $Temp < 100 ){
                  $CurTemp = $this->getCmd(null, 'cur_temp'); 
                if (!is_object($this->getCmd(null, 'WindowOpened')) || !is_object($this->getCmd(null, 'Tendance')))
                    // TODO $this->CheckAndCreateCmd( array( "attr" => array( "WindowOpened" => "0" , "Tendance" => "0" ) ) ) ; // $aDevice['attr']['WindowOpened']
                //$this->CheckAndCreateCmd( array( "attr" => array( "Tendance" => "0" ) ) ) ; // $aDevice['attr']['Tendance']
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
     * @brief Fonction qui permet de créer les commandes en fonction du retour de l'API
     * 
     * @param tableau retour API
     */
//class heatzy extends eqLogic
    public function InitCmds() {  

        $tab_Devices = Synchro::LireJSON( '_Devices' ) ;
        if( $tab_Devices === false){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') Lecture json KO' ) ;
            return false ;
        }

        $product_key  = $this->getConfiguration('product_key', '') ;

        // Si le product_key n'est pas danqs le JSON, on cherche par nom (a defaut, on considère le product_key xxxxx par défaut)
        if( $tab_Devices[$product_key]['cmds'] === null){
        $product_name = $this->getConfiguration('product'    , '') ;
            $product_key = 'xxxxxx' ;
            foreach ($tab_Devices as $key => $cmd) {
                if( $cmd['product_name']  == $product_name ){
                    $product_key = $key ;
                    break ;
                }
            }
        }

        //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') $product_key='.$product_key ) ;
        foreach ($tab_Devices[$product_key]['cmds'] as $cmd) {
            //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') Cmds='.$cmd ) ;
            $this->CreateCmd( $cmd ) ;
        }

    }
  
    /**
     * @brief Fonction qui permet de créer les commandes en fonction du retour de l'API
     * 
     * @param tableau retour API
     */
//class heatzy extends eqLogic
    public function CreateCmd( $commande = '' , $MajOrder = false , $MajName = false ) {  
      
        if( $commande == '' Or $commande == null){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Commande vide' );
            return false ;
        }
      
        $json = file_get_contents(__DIR__.'/_Commands.json');
        if ( $json === false ){
            log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.')'.': JSON _Commands.json non trouvé' );
            return false ;
        }
      
      $tab_cmds = json_decode($json, true);
      if( $tab_cmds === false ) return false ;

      //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Recherche de la commande '.$commande.'...' );
      $cmd = $this->getCmd( null, $tab_cmds[$commande]['LogicalId'] );
      if (!is_object($cmd)) {
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Commande '.$commande.' non trouvé. On va créer' );
        $cmd = new heatzyCmd();
        
        $cmd->setLogicalId( $tab_cmds[$commande]['LogicalId'] );
        $cmd->setName(  __( $tab_cmds[$commande]['Name'] , __FILE__));
        $cmd->setOrder(     $tab_cmds[$commande]['Order'] );
        $cmd->setType(      $tab_cmds[$commande]['Type'] );
        $cmd->setSubType(   $tab_cmds[$commande]['SubType'] );
        
        if($tab_cmds[$commande]['Unite']              !== null) $cmd->setUnite( $tab_cmds[$commande]['Unite'] );
        if($tab_cmds[$commande]['Config_infoName']    !== null) $cmd->setConfiguration('infoName', $tab_cmds[$commande]['Config_infoName']);
        if($tab_cmds[$commande]['Config_value']       !== null) $cmd->setConfiguration('value'   , $tab_cmds[$commande]['Config_value']   );
        if($tab_cmds[$commande]['Config_minValue']    !== null) $cmd->setConfiguration('minValue', $tab_cmds[$commande]['Config_minValue']);
        if($tab_cmds[$commande]['Config_maxValue']    !== null) $cmd->setConfiguration('maxValue', $tab_cmds[$commande]['Config_maxValue']);
        if($tab_cmds[$commande]['Config_tempHL']      !== null) $cmd->setConfiguration('tempHL'  , $tab_cmds[$commande]['Config_tempHL']  );
        if($tab_cmds[$commande]['setValue']           !== null) $cmd->setValue( $this->getCmd( null, $tab_cmds[$commande]['setValue'] )->getId() ) ;
        if($tab_cmds[$commande]['Display_param_step'] !== null) $cmd->setDisplay('parameters', ['step' => $tab_cmds[$commande]['Display_param_step'] ]);
        
        //$cmd->setTemplate('dashboard', 'default');
        //$cmd->setTemplate('mobile', 'default');
        //$cmd->setGeneric_type('GENERIC_INFO');
        //$cmd->setEventOnly(1);
                
        $cmd->setEqLogic_id(   $this->getId() );
        $cmd->setIsHistorized( $tab_cmds[$commande]['IsHistorized'] );
        $cmd->setIsVisible(    $tab_cmds[$commande]['IsVisible'] );
        $cmd->save();
        
        return true ;
      } // !is_object($cmd)
      else{
        if( $MajOrder ){
            $cmd->setOrder( $tab_cmds[$commande]['Order'] );
            $cmd->save();
        }
        if( $MajName ){
            $cmd->setName(  __( $tab_cmds[$commande]['Name'] , __FILE__));
            $cmd->save();
        }
      }
      
      return '' ;
    }
     
    /**
     * @brief Fonction qui permet d'activer/désactiver la programmation
     * 
     * @param $EtatProg        true ou false
     */
//class heatzy extends eqLogic
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
                $aTaskResul = HttpGizwits::UpdateScheduler($UserToken, $this->getLogicalId(), $Id, $aTask);
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
//class heatzy extends eqLogic
    public static function cron() {
        /*log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': cron...');
        $tab = HttpGizwits::GetProduitInfo('aa85e43fc4464e4d0000000000000000') ;
        
        foreach($tab['entities'] as $element) {
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$element['display_name']);
        }
        
        return false ;*/
      
      	sleep(30); // Ne pas interférer avec les appels à hh:mm:00

        if( !cache::exist('Heatzy_Synchronize') ) cache::set( 'Heatzy_Synchronize' , 0) ;
      
        // Si Synchronise depuis plus de 15min, on rénit (peut arriver si plantage dans synchronize)
        if( (strtotime(date("Y-m-d H:i:s")) - cache::byKey('Heatzy_Synchronize')->getValue()) > 900 && cache::byKey('Heatzy_Synchronize')->getValue() > 0 ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Réinit du cache Heatzy_Synchronize car > 600s (='.cache::byKey('Heatzy_Synchronize')->getValue().')' ) ;
            cache::set( 'Heatzy_Synchronize' , 0) ;
        }
      
        //Si synchro en cours, on arrête
        if( cache::byKey('Heatzy_Synchronize')->getValue() > 0){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Arret du cron car Synchronize en cours ...' ) ;
            return false ;
        }
      
        $ExpireToken = config::byKey('ExpireToken','heatzy','none') ;
        $ExpireTokenTime = strtotime( $ExpireToken ) ;

        if( $ExpireTokenTime <= time() ){ // Si token expiré
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Token expiré ('.$ExpireToken.') - Récupération d un nouveau token' );

            if ( heatzy::Login() ){
                $ExpireToken = config::byKey('ExpireToken','heatzy','none') ;
                $ExpireTokenTime = strtotime( $ExpireToken ) ;
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Récupération du token OK ('.$ExpireToken.')' );
            }
            else{
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Récupération token KO' );
                return false ;
            }
        }
      
        if( config::byKey('API_Type','heatzy','REST') != 'REST' ){ // Si WebSocket
            /*if(  ( date("i") % 5 ) == 0 ){
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Envoi du niveau de log' ) ;
                self::sendToDaemon( 'log_level' , '' , array() ) ;
            }*/
            return ;
        }
        
        // Mise à jour du statut (Online/Offline + ajout noueau modules)        
        $Freq_status = config::byKey('Freq_status','heatzy','30') ; // Toutes les 30 min par défaut
        if( $Freq_status > 0  ){ // Si param != off
            if( ( date("i") % $Freq_status ) == 0 ){ // Si on tombe bien sur le x minute
                // Le synchronize permet d'aouter les nouveaux modules rattachés et de vérifier le statut online/offline
                $res = Synchro::SynchronizeHeatzy() ;
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Synchronize cron5 = '.$res );
                
                // Le synchronize contient déjà un update (donc pas la peine d'aller plus loin)
                return true ;
            } // ( date("i") % $Freq_status ) == 0
        } //if $Freq_status > 0
      
        // Gestion du max erreur (passage en mode dégradé en cas de cumul d'erreur d'appel API)
        if( !cache::exist('Heatzy_CptError') ){ // Init si non existant
            cache::set( 'Heatzy_CptError' , 0) ;
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': INIT cache' );
        }
        if( cache::byKey('Heatzy_CptError')->getValue() > HttpGizwits::$_MaxError){
              // Dépassement de la limite
            // Le retour à létat normal se fait via l'updateHeatzyDid après la mise à jour du statut (Synchronize)              
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': cache::Compteur erreur > '.HttpGizwits::$_MaxError.' - Mode dégradé' );
            return false ;          
        } 

        // Mise à jour des commandes infos
        $Freq_value = config::byKey('Freq_value','heatzy','2') ; // Toutes les 2 min par défaut si non parametré
        if( $Freq_value > 0 ){ // Si param != off
            if( (date("i") % $Freq_value ) == 0 ){ // Si on tombe bien sur le x minute

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
            } // (date("i") % $Freq_value ) == 0
        } // if $Freq_value > 0
    }

    /**
    * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
    * synchronisation
    */
    /*
    public static function cron5() {        
        $res = heatzy::SynchronizeHeatzy() ;
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Synchronize cron5 = '.$res );
    }*/
  
  
    /**
    * Fonction exécutée automatiquement toutes les 30minutes par Jeedom
    * seulement pour les modules Heatzy et Flam_Week2
    * */
//class heatzy extends eqLogic
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
//class heatzy extends eqLogic
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
              //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : '.$eqLogic->getName().' - TypeTemplate='.$eqLogic->getConfiguration('TypeTemplate', '') );
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
//class heatzy extends eqLogic
    public function preInsert() {
        $this->setCategory('heating', 1);
    }

//class heatzy extends eqLogic
    public function postInsert() {
        
    }

//class heatzy extends eqLogic
    public function preSave() {
         
    }
    /**
     * @brief  Méthode appellée après la sauvegarde de votre objet
     *         Creation de la commande refresh
     */
//class heatzy extends eqLogic
    public function postSave() {

    }

    /**
     * Si le nom du module a changé, on le met à jour
     */
//class heatzy extends eqLogic
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

//class heatzy extends eqLogic
    public function postUpdate() {
    }
  
//class heatzy extends eqLogic
    public function preRemove() {
        
    }
  
//class heatzy extends eqLogic
    public function postRemove() {
        
    }

//class heatzy extends eqLogic
    public function toHtml($_version = 'dashboard') {
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $_version = jeedom::versionAlias($_version);
        $product = $this->getConfiguration('product', '');
    
        //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : Liste commandes - '.$this->getName());
        foreach ($this->getCmd() as $cmd) {    
            switch($cmd->getType()){
                case 'info':
                    //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : Name='.$this->getName().' - CmdId='.$cmd->getLogicalId().' - CmdName='.$cmd->getName().' - CmdType='.$cmd->getType());

                    $replace['#'.$cmd->getLogicalId().'_id#'] = $cmd->getId();
                    $replace['#'.$cmd->getLogicalId().'_cmd#'] = $cmd->execCmd();
                    $replace['#'.$cmd->getLogicalId().'_unite#'] = $cmd->getUnite();
                    $replace['#'.$cmd->getLogicalId().'_CollectDate#'] = $cmd->getCollectDate() ; 
                    $replace['#'.$cmd->getLogicalId().'_ValueDate#'] = $cmd->getValueDate() ; 
                    $replace['#'.$cmd->getLogicalId().'_history#'] = ($cmd->getIsHistorized()) ? 'history cursor' : '';
                    $replace['none;#'.$cmd->getLogicalId().'_display#'] = ($cmd->getIsVisible() && $cmd->execCmd() != -99) ? '#'.$cmd->getLogicalId().'_display#' : 'none;';
                    break;
                case 'action':
                    //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : Name='.$this->getName().' - CmdId='.$cmd->getLogicalId().' - CmdName='.$cmd->getName().' - CmdType='.$cmd->getType());
                
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
                    log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.')'.' : Type de commande ($cmd->getType()='.$cmd->getType().') inconnu');
                    break;
            } // switch
        } //foreach cmd

        //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : Name='.$this->getName().'- '.var_export($replace, true) );
      
        switch( $this->getConfiguration('TypeTemplate', '') ){
            case '':
                $this->setConfiguration('TypeTemplate', '0');
                $this->save() ;
            case '0':
                $html = template_replace($replace, getTemplate('core', $_version, 'Dashboard','heatzy')); // template commun (bodbod)
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

/* xxxxxxxxxxx bodbod */
$s = require_once('heatzyCmd.class.php');
if( $s != 1 ) 
	log::add('heatzy', 'error', __METHOD__.'(ln '.__LINE__.')'.' : error require_once heatzyCmd.class.php ='.$s);

?>