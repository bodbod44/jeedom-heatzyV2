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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function heatzy_install() {
   //log::add('heatzy', 'debug',  __METHOD__.': heatzy_install');
   $cron = cron::byClassAndFunction('heatzy', 'Login');
   if (is_object($cron)) {
	  $cron->remove();
   } 
}

function heatzy_update() {

   $eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
   foreach ($eqLogics as $eqLogic) {

	  $cmd = $eqLogic->getCmd(null, 'window_switch');
	  if (is_object($cmd)) {
		 log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove window_switch');
		 $cmd->remove(); // pas de save sur un remove
	  }

	  $cmdetat = $eqLogic->getCmd(null, 'etat');
	  $cmdEtatConsigne = $eqLogic->getCmd(null, 'EtatConsigne');
	  if (is_object($cmdetat) && is_object($cmdEtatConsigne) ) {
		 // Si les deux existent, supprimer l'ancienne
		 log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove etat');
		 $cmdetat->remove(); // pas de save sur un remove
	  }
	  if (is_object($cmdetat) && !is_object($cmdEtatConsigne) ) {
		 // Si la nouvelle n'existe pas, je renomme
		 log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' rename etat-EtatConsigne');
		 $cmdetat->setLogicalId('EtatConsigne');
		 $cmdetat->setName(__('Etat Consigne', __FILE__));
		 $cmdetat->save();
	  }
   } // foreach
}

function heatzy_remove() {
   //log::add('heatzy', 'debug',  __METHOD__.': heatzy_remove');
   $cron = cron::byClassAndFunction('heatzy', 'Login');
   if (is_object($cron)) {
	  $cron->remove();
   }
}

?>
