<?php

try {
    require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";

    if (!jeedom::apiAccess(init('apikey'), 'heatzy')) { //remplacez template par l'id de votre plugin
        echo __('Vous n\'êtes pas autorisé à effectuer cette action', __FILE__);
        die();
    }
    if (init('test') != '') {
        echo 'OK';
        die();
    }
  
    $mess_recu = trim( stripslashes( file_get_contents("php://input") ) , '"') ; // enleve les \" et les " de début/fin
    $result = json_decode($mess_recu, true); // JSON->Tableau

    if (!is_array($result)) {
      	if( $result != null )
			log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': die...'.var_export($result, true) );
        die();
    }
    
	//log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' Retour du demon : '.var_export($result, true) );
    
    if( isset($result['data']['did']) ){
      	//log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): $result[data][did] : -'.$result['data']['did'].'-' );
        $eqLogic = eqLogic::byLogicalId($result['data']['did'] , 'heatzy' , false); // récup tous l'équipement heatzy
      	if( $eqLogic ){
            //log::add('heatzy', 'debug', '$eqLogics : '.var_export($eqLogic, true) );
            $result2['did'] = $result['data']['did'] ;
            $result2['updated_at'] = time() ;
            $result2['attr'] = $result['data']['attrs'] ;
            
            switch( $result['cmd'] ){
                case 's2c_noti'; // Notification de changement de datapoint
                    $eqLogic->updateHeatzyDid( '' , $result2 , false) ;
                    break;
                case 's2c_online_status'; // Notification de changement de statut (online/offline)
                    //{"cmd":"s2c_online_status","data":{"did":"xxxxxxxxxxx","passcode":"xxxxxxxx","mac":"xxxxxxxxxx","online":true}}
                    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' Retour du demon : Notification A TRAITER = '.var_export($result, true) );
                    
                    $eqLogic->checkAndUpdateCmd('IsOnLine', $result['data']['online'] );
                    $eqLogic->save();
                    $eqLogic->setStatus('timeout', $result['data']['online'] ? '0' : '1' );
                    
                    break;
                default;
                    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' Retour du demon : cmd gizwits inconnu = '.var_export($result, true) );
                    break;
            }
        }
      	else
      		log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): $eqLogic non trouve ' );
    }
    else
		log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): Tableau sans $result[data][did]' );
    
} catch (Exception $e) {
    log::add('heatzy', 'error', 'catch '.displayException($e)); //remplacez template par l'id de votre plugin
}