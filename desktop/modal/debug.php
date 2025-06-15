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
$eqLogics = heatzy::byType('heatzy');

foreach ($eqLogics as $eqLogic) {
  	echo '</br>&nbsp;';
    echo '<h3>'.$eqLogic->getHumanName(true).'</h3>';
  	//echo '</br>&nbsp;';
  	echo '<span class="label label-info" style="font-size:1em; cursor : default; white-space: normal;">'.var_export( eqLogic::byId($eqLogic->getId(), null) , true ).'</span>';
    echo '</br>&nbsp;';
    echo '<span class="label label-info" style="font-size:1em; cursor:default; white-space:normal;">'.var_export( cmd::byEqLogicId($eqLogic->getId(), null) , true ).'</span>';
  	echo '</br>&nbsp;';
}
?>