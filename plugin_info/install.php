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
   log::add('heatzy', 'debug',  __METHOD__.': heatzy_install');
   $cron = cron::byClassAndFunction('heatzy', 'Login');
   if (is_object($cron)) {
	  $cron->remove();
   } 
}

function heatzy_update() {
	log::add('heatzy', 'debug',  __METHOD__.': heatzy_update');
	$eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
	foreach ($eqLogics as $eqLogic) {

		$cmd = $eqLogic->getCmd(null, 'window_switch');
		if (is_object($cmd)) {
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove window_switch');
			$cmd->remove(); // pas de save sur un remove
		}

		$cmdAvant = $eqLogic->getCmd(null, 'etat');
		$cmdApres = $eqLogic->getCmd(null, 'EtatConsigne');
		if (is_object($cmdAvant) && is_object($cmdApres) ) {
			// Si les deux existent, supprimer l'ancienne
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove etat');
			$cmdAvant->remove(); // pas de save sur un remove
		}
		if (is_object($cmdAvant) && !is_object($cmdApres) ) {
			// Si la nouvelle n'existe pas, je renomme
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' rename etat-EtatConsigne');
			$cmdAvant->setLogicalId('EtatConsigne');
			$cmdAvant->setName(__('Etat Consigne', __FILE__));
			$cmdAvant->save();
		}

		$cmdAvant = $eqLogic->getCmd(null, 'EtatWindow');
		$cmdApres = $eqLogic->getCmd(null, 'WindowSwitch');
		if (is_object($cmdAvant) && is_object($cmdApres) ) {
			// Si les deux existent, supprimer l'ancienne
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove EtatWindow');
			$cmdAvant->remove(); // pas de save sur un remove
		}
		if (is_object($cmdAvant) && !is_object($cmdApres) ) {
			// Si la nouvelle n'existe pas, je renomme
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' rename EtatWindow-WindowSwitch');
			$cmdAvant->setLogicalId('WindowSwitch');
			$cmdAvant->setName(__('Etat Activation Fenêtre Ouverte', __FILE__));
			$cmdAvant->save();
		}

		$cmdAvant = $eqLogic->getCmd(null, 'WindowOn');
		$cmdApres = $eqLogic->getCmd(null, 'WindowSwitchOn');
		if (is_object($cmdAvant) && is_object($cmdApres) ) {
			// Si les deux existent, supprimer l'ancienne
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove WindowOn');
			$cmdAvant->remove(); // pas de save sur un remove
		}
		if (is_object($cmdAvant) && !is_object($cmdApres) ) {
			// Si la nouvelle n'existe pas, je renomme
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' rename WindowOn-WindowSwitchOn');
			$cmdAvant->setLogicalId('WindowSwitchOn');
			$cmdAvant->setConfiguration('infoName', 'WindowSwitch');
			$cmdAvant->save();
		}

		$cmdAvant = $eqLogic->getCmd(null, 'WindowOff');
		$cmdApres = $eqLogic->getCmd(null, 'WindowSwitchOff');
		if (is_object($cmdAvant) && is_object($cmdApres) ) {
			// Si les deux existent, supprimer l'ancienne
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove WindowOff');
			$cmdAvant->remove(); // pas de save sur un remove
		}
		if (is_object($cmdAvant) && !is_object($cmdApres) ) {
			// Si la nouvelle n'existe pas, je renomme
			log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' rename WindowOff-WindowSwitchOff');
			$cmdAvant->setLogicalId('WindowSwitchOff');
			$cmdAvant->setConfiguration('infoName', 'WindowSwitch');
			$cmdAvant->save();
		}

		// Pour lse Glow et Shine, on bascule les commandes créées par les anciennes verions
		if( $eqLogic->getConfiguration('product', '') == 'Shine_ble' || $eqLogic->getConfiguration('product', '') == 'Glow_Simple_ble' ){
			
			$cmdAvant = $eqLogic->getCmd(null, 'etatlock');
			$cmdApres = $eqLogic->getCmd(null, 'Lock_C_State');
			if (is_object($cmdAvant) && is_object($cmdApres) ) {
				// Si les deux existent, supprimer l'ancienne
				log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove etatlock');
				$cmdAvant->remove(); // pas de save sur un remove
			}
			if (is_object($cmdAvant) && !is_object($cmdApres) ) {
				// Si la nouvelle n'existe pas, je renomme
				log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' rename etatlock-Lock_C_State');
				$cmdAvant->setLogicalId('Lock_C_State');
				$cmdAvant->save();
			}
			$cmdAvant = $eqLogic->getCmd(null, 'LockOn');
			$cmdApres = $eqLogic->getCmd(null, 'Lock_C_On');
			if (is_object($cmdAvant) && is_object($cmdApres) ) {
				// Si les deux existent, supprimer l'ancienne
				log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove LockOn');
				$cmdAvant->remove(); // pas de save sur un remove
			}
			if (is_object($cmdAvant) && !is_object($cmdApres) ) {
				// Si la nouvelle n'existe pas, je renomme
				log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' rename LockOn-Lock_C_On');
				$cmdAvant->setLogicalId('Lock_C_On');
				$cmdAvant->setConfiguration('infoName', 'Lock_C_State');
				$cmdAvant->save();
			}
			$cmdAvant = $eqLogic->getCmd(null, 'LockOff');
			$cmdApres = $eqLogic->getCmd(null, 'Lock_C_Off');
			if (is_object($cmdAvant) && is_object($cmdApres) ) {
				// Si les deux existent, supprimer l'ancienne
				log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' remove LockOff');
				$cmdAvant->remove(); // pas de save sur un remove
			}
			if (is_object($cmdAvant) && !is_object($cmdApres) ) {
				// Si la nouvelle n'existe pas, je renomme
				log::add('heatzy', 'debug',  __METHOD__.': '.$eqLogic->getName().' rename LockOff-Lock_C_Off');
				$cmdAvant->setLogicalId('Lock_C_Off');
				$cmdAvant->setConfiguration('infoName', 'Lock_C_State');
				$cmdAvant->save();
			}
		} // if Glow et Shine	  
	} // foreach
}

function heatzy_remove() {
   log::add('heatzy', 'debug',  __METHOD__.': heatzy_remove');
   $cron = cron::byClassAndFunction('heatzy', 'Login');
   if (is_object($cron)) {
	  $cron->remove();
   }
}

?>
