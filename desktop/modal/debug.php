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
?>

<table class="table table-condensed tablesorter" id="table_debugrflink">
	<thead>
		<tr>
			<th>{{Module}}</th>
  		<th>{{Type}}</th>
			<th>{{DID}}</th>
			<th>{{MAC}}</th>
      <th>{{Connecté}}</th>
			<th>{{Dernière mise à jour}}</th>
			<th>{{Dernière communication}}</th>
			<th>{{Date création}}</th>
		</tr>
	</thead>
	<tbody>
	 <?php
foreach ($eqLogics as $eqLogic) {
	echo '<tr><td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $eqLogic->getHumanName(true) . '</a></td>';
  																															/// Pour heatzy flam mais par defaut le pilote
  	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('product', 'Heatzy') . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getLogicalId() . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('mac') . '</span></td>';
    if( $eqLogic->getStatus('timeout') == '1')
    	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">Non</span></td>';
  	else
      	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">Oui</span></td>';
    echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('updatetime') . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('lastCommunication') . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('createtime') . '</span></td></tr>';
}
?>
	</tbody>
</table>

