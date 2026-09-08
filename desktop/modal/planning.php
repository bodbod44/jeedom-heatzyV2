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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}


if( false ){
    foreach ($globals as $key => $value) echo '$GLOBALS['.$key.']='.$value.'<br>' ;
    foreach ($_SERVER as $key => $value) echo '$_SERVER['.$key.']='.$value.'<br>' ;
    foreach ($_GET as $key => $value)    echo '$_GET['.$key.']='.$value.'<br>' ;
    foreach ($_POST as $key => $value)   echo '$_POST['.$key.']='.$value.'<br>' ;
    foreach ($_ENV as $key => $value)    echo '$_ENV['.$key.']='.$value.'<br>' ;
    

    foreach (jeedom::health() as $datas) {
        echo 'name: '.$datas['name']. ', comment: ' .$datas['comment'].', state: '.$datas['state'].', result: '.$datas['result']."<br>\n";
    }
}



$MonEqLogic = eqLogic::byId( $_GET['id'] ) ;

echo '<h2>'.$MonEqLogic->getName().'</h2>'."\n" ;

$result = RecupDonnees( $MonEqLogic ) ;

if( is_string($result) ){
    echo '<br>'.$result ;  // Affichage du message d'erreur
}
else{
    echo '<style>'."\n" ;
    echo '.blink {' ;
    echo '  animation: blink 0.5s infinite;' ;
    echo '}'."\n" ;
    echo '@keyframes blink { ' ;
    echo '  0% { opacity:0; }' ;
    echo '  50% { opacity:1; } ' ;
    echo '  100% { opacity:0; }' ;
    echo '}' ;
    echo '.blink2 {' ;
    echo '  animation: blink2 1s infinite;' ;
    echo '}'."\n" ;
    echo '@keyframes blink2 { ' ;
    echo '  0% { background-color:#FFFFFF; }' ;
    echo '  50% { background-color:#062400; } ' ;
    echo '  100% { background-color:#FFFFFF; }' ;
    echo '}' ; 
    echo 'table {' ;
    echo '  font-size: 14px;' ;
    echo '}' ;
    echo '</style>'."\n" ;

    if( $MonEqLogic->getConfiguration('product_name', '') == "Heatzy"){
        echo '<br><p style="color:red;">'."ATTENTION : Sur ce type de module (Pilote_V1), le passage à l'heure d'hiver/été décalera le planning d'1h dans un sens ou l'autre".'</p>' ;
    }
    if( isset( $result['timer_switch'] ) ){
        echo '<br>Programmation : <b>'.($result['timer_switch'] == 0 ? '<FONT COLOR="red">Désactivée</FONT>' : '<FONT COLOR="green">Activée</FONT>').'</b>' ;
    }
    else
      log::add('heatzy', 'error', __METHOD__.'(ln '.__LINE__.') '.$MonEqLogic->getName().' - $result[timer_switch] non valorisé' );

    echo '<br><br><table border=1>'."\n" ;

    echo '  <tr>'."\n" ;
    echo '    <th style="text-align:center">Heures</th>'."\n" ;
    for ($j = 1; $j <= 7; $j++){
        $joursem = array('','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche')[$j] ;
        $class = ( date('N') == $j ? ' class="blink"' : '') ; // clignote si aujourd'hui
        echo '    <th style="text-align:center"><div'.$class.'>'.$joursem.'</div></th>'."\n" ;
    }
    echo '  </tr>'."\n" ;
    
    for ($h = 0; $h <= 23; $h++) {
        for ($m = 0; $m <= 30; $m = $m + 30) {
            echo '  <tr align="center">'."\n" ;
            $class = ( date('H') == $h && date('i') >= $m && date('i') < $m+30 ? ' class="blink"' : '') ; // clignote l'heure actuel
            echo '    <td width="70"><div'.$class.'>'.str_pad($h, 2, '0', STR_PAD_LEFT).'h'.str_pad($m, 2, '0', STR_PAD_LEFT).'</div></td>'."\n" ;
            for ($j = 1; $j <= 7; $j++) {
            $mode = FindModeByTab( $result , $j , $h , $m ) ;
            if( date('N') == $j && date('H') == $h && date('i') >= $m && date('i') < $m+30 )
                    echo '    <td style="color:'.Mode2Color($mode).';font-weight: bold;" width="70"><div class="blink">'.$mode.'</div></td>'."\n" ;
                else
                    echo '    <td style="background-color:'.Mode2Color($mode).';" width="70">'.$mode.'</td>'."\n" ;
            } // for $j
            echo '  </tr>'."\n" ;
        } // for $m 
    } // for $h 
    echo '</table>'."\n" ;
}


function FindModeByAttrs( $tab_attr , $j , $h , $m ){
    $data = floor( $h / 2 ) + 1 ; // Créneau de 2h. Démarre à px_data1
    $bin = str_pad( decbin($tab_attr['p'.$j.'_data'.$data]) , 8 , '0' , STR_PAD_LEFT) ;  // Convertis en binaire (01010101) et ajoute des 0 au début
    // 01 01 01 01 => x+1h30 x+1 x+0h30 x (premier créneau à droite)
    $deb = ( floor( $h / 2 ) == ( $h / 2 ) ? 4 : 0) + ( $m == 0 ? 2 : 0) ;
    switch ( substr( $bin , $deb , 2 ) ) {
        case '00': return 'confort'  ; break;
        case '01': return 'eco'      ; break;
        case '10': return 'hors gel' ; break;
        default:   return 'xxxx' ;
    }  
}

function FindModeByTab( $tab , $j , $h , $m ){
    return $tab[ $h * 2 + $m / 30 ][ $j - 1 ] ;
    //return ($h * 2 + $m / 30).'-'.($j - 1) ;
}

function Mode2Color( $mode ){
    switch ( $mode ) {
        case 'confort':  return '#700702' ; break;
        case 'eco':      return '#062400' ; break;
        case 'hors gel': return '#101459' ; break;
        default:   return '#FFFFFF' ;
    }
}

function RecupDonnees( $MonEqLogic ){
  
    if( $MonEqLogic->getCmd(null, 'IsOnLine')->execCmd() == false or $MonEqLogic->getStatus('timeout', '0') == '1' ){
        return '<p style="color:red;">Problème : équipement hors ligne (impossible de récupérer les informations)</p>' ;
    }
  
    if( $MonEqLogic->getConfiguration('product_name', '') == "Heatzy"){
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : Type Heatzy');
        return RecupDonnees_PiloteV1( $MonEqLogic ) ;
    }
    else{
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : Diff de Heatzy');
        return RecupDonnees_pX_dataY( $MonEqLogic ) ;
    }
    return false ;
}

function RecupDonnees_PiloteV1( $MonEqLogic ){
    
    //$Tasks = array(0 => array('remark' => '','repeat' => 'mon, wed','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:47','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '21:30','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),1 =>array ('remark' => '','repeat' => 'sun','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:47','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '22:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),2 =>array ('remark' => '','repeat' => 'fri','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:47','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 2,),'time' => '22:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),3 =>array ('remark' => '','repeat' => 'fri','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:47','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '12:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),4 =>array ('remark' => '','repeat' => 'fri','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:47','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 2,),'time' => '09:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),5 =>array ('remark' => '','repeat' => 'thu','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '12:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),6 =>array ('remark' => '','repeat' => 'thu','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 2,),'time' => '09:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),7 =>array ('remark' => '','repeat' => 'wed','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '08:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),8 =>array ('remark' => '','repeat' => 'wed','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 0,),'time' => '05:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),9 =>array ('remark' => '','repeat' => 'tue','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '08:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),10 =>array ('remark' => '','repeat' => 'tue','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 0,),'time' => '05:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),11 =>array ('remark' => '','repeat' => 'mon','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '13:30','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),12 =>array ('remark' => '','repeat' => 'mon','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 2,),'time' => '09:30','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),13 =>array ('remark' => '','repeat' => 'mon','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:46','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '06:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),14 =>array ('remark' => '','repeat' => 'mon','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:45','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 0,),'time' => '02:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),15 =>array ('remark' => '','repeat' => 'mon','did' => 'didxxxxxxdid','created_at' => '2026-09-02T21:16:45','enabled' => true,'days' =>array (),'product_key' => '9420ae048da545c88fc6274d204dd25f','raw' => '','attrs' =>array ('mode' => 1,),'time' => '00:00','date' => '','attrs_config' =>array (),'scene_id' => '','group_id' => '','id' => 'xxxxxxxxxxxxxx',),) ;

    $tab ;

    // Récupère toutes les tâches
    $Tasks = HttpGizwits::GetSchedulerListFull( $MonEqLogic->getLogicalId() );
    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.') '.$MonEqLogic->getLogicalId().' : count($Tasks)='.count($Tasks ?? array()) );
    
    // Verification de l'activation de la programmation
    $time_switch = heatzy::CheckAndUpdateActivProg( $Tasks , $MonEqLogic->getLogicalId() ) ;
    $tab['timer_switch'] = $time_switch ;
    
    // Parcours toutes les tâches trouvées
    foreach ($Tasks as $aTask){
        
        // Récupère les hh:mm
        if( isset( $aTask['time'] ) && strlen($aTask['time'] ?? '') === 5 ){
            //$heure = explode(":", $aTask['time'] )[0] ;
            $heure = substr( $aTask['time'] , 0 , 2) ;
            //$minute = explode(":", $aTask['time'] )[1] ;
            $minute = substr( $aTask['time'] , -2) ;
            if( $heure  < 0 or $heure > 23  ) $heure = null ;
            if( $minute < 0 or $minute > 59 ) $minute = null ;
        }
        else{
            $heure = null ;
            $minute = null ;
        }

        // récupère et traduit le mode de chauffe
        if( isset( $aTask['attrs']['mode'] ) ){
            switch ( $aTask['attrs']['mode'] ) {
                // "cft;[1,1,0]" "eco;[1,1,1]" "fro;[1,1,2]"
                case 0: $mode = 'confort'  ; break;
                case 1: $mode = 'eco'      ; break;
                case 2: $mode = 'hors gel' ; break;
                default:$mode = null ;
            }
        }
        else{
            $mode = null ;
        }

        log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.') repeat='.$aTask['repeat'].' - time='.$aTask['time'].' - enabled='.($aTask['enabled'] ? '1' : '0').' - mode='.$mode  );

        // S'l s'agit d'un tâched e type planninf ( présence repeat + hh:00 ou hh:30
        if( isset( $aTask['repeat'] ) && $heure !== null && in_array( $minute , array( '00' ,'30' ) ) ) {    /// Sort de la boucle des taches à la premiere tache trouvée
            //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.') enabled' );

            foreach ( explode(",", $aTask['repeat'] ) as $jours) {
                switch ( trim($jours) ) {
                    case 'mon': $jour = 0 ; break; // mon, tue, wed, thu, fri, sat, sun
                    case 'tue': $jour = 1 ; break;
                    case 'wed': $jour = 2 ; break;
                    case 'thu': $jour = 3 ; break;
                    case 'fri': $jour = 4 ; break;
                    case 'sat': $jour = 5 ; break;
                    case 'sun': $jour = 6 ; break;
                    default:  $jour = null ; break; ;
                } // switch

                if( $jour !== null ){
                    // Décalage horaire
                    $decalage = 1 + date('I') ;
                    if( ($heure + $decalage) >= 24 ){
                      $jour = ($jour + 1) % 7 ; // le modulo sert a gerer la bascule dim->lun
                    }
                    $heure = ($heure + $decalage) % 24 ;
                  
                    //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.') indice='.($heure * 2 + $minute / 30).'-'.$jour  );
                    $tab[ $heure * 2 + $minute / 30 ][ $jour ] = $mode ;
                } // if $jour !== null
            } // foreach
        } // if != null
        //} // if enabled
    } // foreach

    //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.') $tab='.var_export( $tab , true )  );

    // Remplissage des zones vides
    $mode = null ;
    for ($j = 0; $j < 7; $j++) {
        for ($h = 0; $h < 48; $h++) {
            if( $tab[$h][$j] !== null )
                $mode = $tab[$h][$j] ;
            else
                $tab[$h][$j] = $mode ;
        }
    }
  
    // Si le lundi minuit est null, on reprend celui du dimanche soir
    $dernier = $tab[47][6] ;
    $h = 0 ;
    while( $tab[$h][0] == null && $h < 48 ){
        //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.') $dernier=*'.$dernier.'*'  );
        $tab[$h][0] = $dernier ;
        $h++ ;
    }
  
    return $tab ;
    //return '<p style="color:red;">Fonctionnalité non prise en charge pour ce type de module</p>' ; 
}

function RecupDonnees_pX_dataY( $MonEqLogic ){
    $result = HttpGizwits::GetConsigne( $MonEqLogic->getLogicalId() ) ;
    //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.') '.var_export( $result , true ) );
    if( $result === false ){
        return '<p style="color:red;">Problème lors de la récupération des données</p>' ;
    }
    else if( !isset( $result['attr']['p1_data1'] ) ){
        return '<p style="color:red;">Problème : Attributs pX_dataY non trouvés</p>' ;
    }
    
    $tab = array() ;
    $l = 0 ;
    for ($h = 0; $h <= 23; $h++) {
        for ($m = 0; $m <= 30; $m = $m + 30) {
            $c = 0 ;
            for ($j = 1; $j <= 7; $j++) {
                $tab[$l][$c++] = FindModeByAttrs( $result['attr'] , $j , $h , $m ) ;
            }
            $l++ ;
        }
    }
    
    $tab['timer_switch'] = $result['attr']['timer_switch'] ;
    
    return $tab ;
}

?>