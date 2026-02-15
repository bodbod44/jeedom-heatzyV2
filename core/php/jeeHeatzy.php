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
  	//log::add('heatzy', 'debug', '$mess_recu='.$mess_recu);
    //log::add('heatzy', 'debug', '$result='.$result);
    if (!is_array($result)) {
      	if( $result != null )
			log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': die...'.var_export($result, true) );
        die();
    }
    
	log::add('heatzy', 'debug', 'Retour du demon... : '.var_export($result, true) );
    
    if( isset($result['data']['did']) ){
      	//log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): $result[data][did] : -'.$result['data']['did'].'-' );
        $eqLogic = eqLogic::byLogicalId($result['data']['did'] , 'heatzy' , false); // récup tous l'équipement heatzy
      	if( $eqLogic ){
          //log::add('heatzy', 'debug', '$eqLogics : '.var_export($eqLogic, true) );
          $result2['did'] = $result['data']['did'] ;
          $result2['updated_at'] = time() ;
          $result2['attr'] = $result['data']['attrs'] ;
          //log::add('heatzy', 'debug', '$result2 : '.var_export($result2, true) );
          $eqLogic->updateHeatzyDid2( '' , $result2 , false) ;
        }
      	else
      		log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): $eqLogic non trouve ' );
    }
    else
		log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): Tableau sans $result[data][did]' );
    
    /*if (isset($result['key1'])) {
        log::add('heatzy', 'error', 'J ai recu key1...');
    } elseif (isset($result['key2'])) {
        log::add('heatzy', 'error', 'J ai recu key2...');
    } else {
        log::add('heatzy', 'error', 'je ne sais pas quoi faire'); //remplacez template par l'id de votre plugin
    }*/
} catch (Exception $e) {
    log::add('heatzy', 'error', 'catch '.displayException($e)); //remplacez template par l'id de votre plugin
}