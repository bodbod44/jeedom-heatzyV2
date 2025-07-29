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
	/*
   log::add('heatzy', 'debug',  __METHOD__.': heatzy_install');
   $cron = cron::byClassAndFunction('heatzy', 'Login');
   if (is_object($cron)) {
	  $cron->remove();
   } */
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
      
      	// Mise à jour des setOrder pour trier les commandes (pour plus de clareté)
        $cmd = $eqLogic->getCmd(null, 'refresh');              if (is_object($cmd)){ $cmd->setOrder(0);  $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'IsOnLine');             if (is_object($cmd)){ $cmd->setOrder(1);  $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'cur_temp');             if (is_object($cmd)){ $cmd->setOrder(10); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'cur_humi');             if (is_object($cmd)){ $cmd->setOrder(11); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'eco_temp');             if (is_object($cmd)){ $cmd->setOrder(12); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'eco_temp_consigne');    if (is_object($cmd)){ $cmd->setOrder(13); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'cft_temp');             if (is_object($cmd)){ $cmd->setOrder(14); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'cft_temp_consigne');    if (is_object($cmd)){ $cmd->setOrder(15); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'Tendance');             if (is_object($cmd)){ $cmd->setOrder(16); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'WindowOpened');         if (is_object($cmd)){ $cmd->setOrder(17); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'EtatConsigne');         if (is_object($cmd)){ $cmd->setOrder(20); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'mode');                 if (is_object($cmd)){ $cmd->setOrder(21); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'Confort');              if (is_object($cmd)){ $cmd->setOrder(22); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'Eco');                  if (is_object($cmd)){ $cmd->setOrder(23); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'HorsGel');              if (is_object($cmd)){ $cmd->setOrder(24); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'Off');                  if (is_object($cmd)){ $cmd->setOrder(25); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'Confort-1');            if (is_object($cmd)){ $cmd->setOrder(26); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'Confort-2');            if (is_object($cmd)){ $cmd->setOrder(27); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'etatprog');             if (is_object($cmd)){ $cmd->setOrder(30); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'ProgOn');               if (is_object($cmd)){ $cmd->setOrder(31); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'ProgOff');              if (is_object($cmd)){ $cmd->setOrder(32); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'etatlock');             if (is_object($cmd)){ $cmd->setOrder(33); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'LockOn');               if (is_object($cmd)){ $cmd->setOrder(34); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'LockOff');              if (is_object($cmd)){ $cmd->setOrder(35); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'WindowSwitch');         if (is_object($cmd)){ $cmd->setOrder(39); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'WindowSwitchOn');       if (is_object($cmd)){ $cmd->setOrder(40); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'WindowSwitchOff');      if (is_object($cmd)){ $cmd->setOrder(41); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'derog_mode');           if (is_object($cmd)){ $cmd->setOrder(60); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'derog_off');            if (is_object($cmd)){ $cmd->setOrder(62); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'derog_time_vacances');  if (is_object($cmd)){ $cmd->setOrder(63); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'derog_vacances');       if (is_object($cmd)){ $cmd->setOrder(64); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'derog_time_boost');     if (is_object($cmd)){ $cmd->setOrder(65); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'derog_boost');          if (is_object($cmd)){ $cmd->setOrder(66); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'derog_presence');       if (is_object($cmd)){ $cmd->setOrder(67); $cmd->save(); }
        $cmd = $eqLogic->getCmd(null, 'detect_presence');      if (is_object($cmd)){ $cmd->setOrder(68); $cmd->save(); }
		
        $cmd = $eqLogic->getCmd(null, 'Confort');   if (is_object($cmd)){ $cmd->setName(__('Mode Confort'  , __FILE__)) ; $cmd->save(); }
		$cmd = $eqLogic->getCmd(null, 'Eco');       if (is_object($cmd)){ $cmd->setName(__('Mode Eco'      , __FILE__)) ; $cmd->save(); }
		$cmd = $eqLogic->getCmd(null, 'HorsGel');   if (is_object($cmd)){ $cmd->setName(__('Mode HorsGel'  , __FILE__)) ; $cmd->save(); }
		$cmd = $eqLogic->getCmd(null, 'Off');       if (is_object($cmd)){ $cmd->setName(__('Mode Off'      , __FILE__)) ; $cmd->save(); }
		$cmd = $eqLogic->getCmd(null, 'Confort-1'); if (is_object($cmd)){ $cmd->setName(__('Mode Confort-1', __FILE__)) ; $cmd->save(); }
		$cmd = $eqLogic->getCmd(null, 'Confort-2'); if (is_object($cmd)){ $cmd->setName(__('Mode Confort-2', __FILE__)) ; $cmd->save(); }
      
	} // foreach
	
   log::add('heatzy', 'debug',  __METHOD__.': heatzy_remove');
   $cron = cron::byClassAndFunction('heatzy', 'Login');
   if (is_object($cron)) {
	  $cron->remove();
   }
}

function heatzy_remove() {
   log::add('heatzy', 'debug',  __METHOD__.': heatzy_remove');
   $cron = cron::byClassAndFunction('heatzy', 'Login');
   if (is_object($cron)) {
	  $cron->remove();
   }
}

?>