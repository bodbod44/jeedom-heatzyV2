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

require_once dirname(__FILE__) . '/../../core/class/heatzyHttpGizwits.class.php';


echo '<h2>Groupes HEATZY</h2>'."\n" ;
echo '<br>'."\n" ;

$resultGetGroups = HttpGizwits::GetGroups() ;

if( $resultGetGroups === false ){
    echo 'Problème lors de la récupération des données' ;
}
else if( !isset( $resultGetGroups['0']['group_name'] ) ){
    echo 'Aucun groupe trouvé<br>' ;
    //echo var_export( $resultGetGroups , true) ;
}
else{
    echo '<ul>'  ;
    foreach ($resultGetGroups as $group) {
        //array ( 0 => array ( 'remark' => '', 'gw_did' => NULL, 'multicast_id' => '', 'updated_at' => '2026-04-26T09:00:23Z', 'created_at' => '2026-04-26T09:00:23Z', 'verbose_name' => '', 'id' => '69edd4278fbe271c3357119c', 'product_key' => '', 'group_name' => 'Mon Groupe', ), )
        echo '<li><b>'.$group['group_name'].'</b> ('.$group['id'].') - updated_at:'.$group['updated_at'].'</li>'  ;

        $result_GetDidByGroup = HttpGizwits::GetDidByGroup( $group['id'] ) ;
        if( $resultGetGroups === false ){
            echo '<li>Problème de récupération des données</li>'  ;
        }
        else{
            echo '<ul>'  ;
            foreach ($result_GetDidByGroup as $did) {
                //"did": "Eq7LnhD4ajkzYpNMFYczVz","verbose_name": "Pilote_Soc","type": "normal","dev_alias": "Chauff Chambre4 Parents","product_key": "b9a67b6ce24b437d9794103fd317e627"
                echo '<li><b>'.$did['dev_alias'].'</b> ('.$did['did'].') - Type : '.$did['verbose_name'].'</li>'  ;
            }
            echo '</ul>'  ;
        }
    }
    echo '</ul>'  ;
}



?>