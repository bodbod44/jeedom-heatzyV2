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
              case 's2c_noti': // Notification de changement de datapoint
                    //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): s2c_noti' );   
                    $eqLogic->updateHeatzyDid( $result2 , false) ;
                    //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): s2c_noti2' );  
                    break;
              case 's2c_raw': // Notification de changement de datapoint
                    //$eqLogic->updateHeatzyDid( $result , false) ;
                    // Ajouter des clés dans un tabl : function array_combine(array $keys, array $values): array
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): *** Décode raw pour pilote_pro ****' );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_prog(9)='.$result['data']['raw'][9] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_pX_dataX(10->93)='.$result['data']['raw'][10] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_derog(94)='.$result['data']['raw'][94] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_derog_time(95)='.$result['data']['raw'][95] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_lock(96)='.$result['data']['raw'][96] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_time_week(97)='.$result['data']['raw'][97] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_time_hour(98)='.$result['data']['raw'][98] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_com_temp(105)='.$result['data']['raw'][105] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_cft_temp(107)='.$result['data']['raw'][107] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_eco_temp(109)='.$result['data']['raw'][109] );
                    switch( $result['data']['raw'][110] ){
                        case '38' : $mode = 'vers off'; break ;
                        case '54' : $mode = 'off'; break ;
                        case '48' : $mode = 'vers confort'; break ;
                        case '32' : $mode = 'confort'; break ;
                        case '50' : $mode = 'vers eco'; break ;
                        case '34' : $mode = 'eco'; break ;
                        case '52' : $mode = 'vers HorsGel'; break ;
                        case '36' : $mode = 'HorsGel'; break ;
                        case '40' : $mode = 'Confort-1'; break ;
                        case '42' : $mode = 'Confort-2'; break ;
                        default: $mode = 'inconnu'; break ;
                    }
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_mode1(110)='.$result['data']['raw'][110].' ('.$mode.')' );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_cur_hum(111)='.$result['data']['raw'][111] );
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.'): raw_cur_temp(113)='.$result['data']['raw'][113] );
                    break;
                case 's2c_online_status': // Notification de changement de statut (online/offline)
                    //{"cmd":"s2c_online_status","data":{"did":"xxxxxxxxxxx","passcode":"xxxxxxxx","mac":"xxxxxxxxxx","online":true}}
                    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' Retour du demon : Notification A TRAITER = '.var_export($result, true) );
                    
                    $eqLogic->checkAndUpdateCmd('IsOnLine', $result['data']['online'] );
                    $eqLogic->save();
                    $eqLogic->setStatus('timeout', $result['data']['online'] ? '0' : '1' );
                    
                    break;
                default:
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