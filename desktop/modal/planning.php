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
}


$MonEqLogic = eqLogic::byId( $_GET['id'] ) ;

echo '<h2>'.$MonEqLogic->getName().'</h2>'."\n" ;

$result = HttpGizwits::GetConsigne( $MonEqLogic->getLogicalId() ) ;

if( $result === false ){
    echo 'Problème lors de la récupération des données' ;
}
else if( !isset( $result['attr']['p1_data1'] ) ){
    echo 'Attributs du planning non trouvés' ;
}
else{
    echo '<br>Programmation : <b>'.($result['attr']['timer_switch'] == 0 ? '<FONT COLOR="red">Désactivé</FONT>' : '<FONT COLOR="green">Activé</FONT>').'</b><br><br>' ;

	
	echo '<table border=1>'."\n" ;
    
	echo '  <tr>'."\n" ;
    echo '    <th style="text-align:center">Heures</th>'."\n" ;
    for ($j = 1; $j <= 7; $j++)
        echo '    <th style="text-align:center">'.array('','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche')[$j].'</th>'."\n" ;
    echo '  </tr>'."\n" ;

    for ($h = 0; $h <= 23; $h++) {
        for ($m = 0; $m <= 30; $m = $m + 30) {
            echo '  <tr align="center">'."\n" ;
            echo '    <td width="70">'.str_pad($h, 2, '0', STR_PAD_LEFT).'h'.str_pad($m, 2, '0', STR_PAD_LEFT).'</td>'."\n" ;
            for ($j = 1; $j <= 7; $j++) {
                $mode = FindMode( $result['attr'] , $j , $h , $m ) ;
                echo '    <td style="background-color:'.Mode2Color($mode).';" width="70">'.$mode.'</td>'."\n" ;
            } // for $j
            echo '  </tr>'."\n" ;
        } // for $m 
    } // for $h 
    echo '</table>'."\n" ;
}


function FindMode( $tab_attr , $j , $h , $m ){
    $data = floor( $h / 2 ) + 1 ;  
    $bin = str_pad( decbin($tab_attr['p'.$j.'_data'.$data]) , 8 , '0' , STR_PAD_LEFT) ;  
    // 01 01 01 01 => x+1h30 x+1 x+0h30 x
    $deb = ( floor( $h / 2 ) == ( $h / 2 ) ? 4 : 0) + ( $m == 0 ? 2 : 0) ;
    switch ( substr( $bin , $deb , 2 ) ) {
        case '00': return 'confort'  ; break;
        case '01': return 'eco'      ; break;
        case '10': return 'hors gel' ; break;
        default:   return 'xxxx' ;
    }  
}

function Mode2Color( $mode ){
    switch ( $mode ) {
        case 'confort':  return '#700702' ; break;
        case 'eco':      return '#062400' ; break;
        case 'hors gel': return '#101459' ; break;
        default:   return 'xxxx' ;
    }
}

?>