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
    $result = json_decode(file_get_contents("php://input"), true);
    if (!is_array($result) ) {
      	if( !is_null($result) )	
      		log::add('heatzy', 'error', 'die...('.var_export($result, true).')');
        die();
    }
	log::add('heatzy', 'error', 'On avance...');
    if (isset($result['key1'])) {
        log::add('heatzy', 'error', 'J ai recu key1...');
    } elseif (isset($result['key2'])) {
        log::add('heatzy', 'error', 'J ai recu key2...');
    } else {
        log::add('heatzy', 'error', 'je ne sais pas quoi faire'); //remplacez template par l'id de votre plugin
    }
} catch (Exception $e) {
    log::add('heatzy', 'error', 'catch '.displayException($e)); //remplacez template par l'id de votre plugin
}