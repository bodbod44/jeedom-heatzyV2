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

try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (!isConnect('admin')) {
		throw new Exception(__('401 - Accès non autorisé', __FILE__));
	}

	ajax::init();
  
  	require_once dirname(__FILE__) . '/../class/heatzy.class.php';

	if (init('action') == 'deamon_start') {
		ajax::success( heatzy::deamon_start() );
	}

	if (init('action') == 'deamon_stop') {
		ajax::success( heatzy::deamon_stop() );
	} 
  
	if (init('action') == 'deamon_ss') {
		ajax::success( heatzy::deamon_stop() );	
      	ajax::success( heatzy::deamon_start() );
	}
  
	if (init('action') == 'getPort') {
		ajax::success( heatzy::getPort() );	
	} 
  
	if (init('action') == 'getUsedPort') {
		ajax::success( heatzy::getUsedPort() );	
	} 
  
	if (init('action') == 'SyncHeatzy') {
		ajax::success( Synchro::SynchronizeHeatzy( true ) );
	}
  
	if (init('action') == 'SyncheatzyByLearning') {
		ajax::success( Synchro::SynchronizeByLearning() );
	}
  
	if (init('action') == 'SyncheatzyUpdate') {
		ajax::success( Synchro::MajAllCmds( init('mode') ) );
	}
  
	if (init('action') == 'GetSchedulerList') {
		//require_once dirname(__FILE__) . '/../class/heatzy.class.php';
		ajax::success( HttpGizwits::GetSchedulerList( config::byKey('UserToken','heatzy','none'), init('Did'), init('Skip'), init('Limit') ) );
	}
  
    if (init('action') == 'CreateScheduler') {
		//require_once dirname(__FILE__) . '/../class/heatzy.class.php';
		ajax::success( HttpGizwits::CreateScheduler(config::byKey('UserToken','heatzy','none'), init('Did'), json_decode(init('Param')) ) );
	}

	if (init('action') == 'UpdateScheduler') {
		//require_once dirname(__FILE__) . '/../class/heatzy.class.php';
		ajax::success( HttpGizwits::UpdateScheduler(config::byKey('UserToken','heatzy','none'), init('Did'), init('Id'), json_decode(init('Param')) ) );
	}

	if (init('action') == 'DeleteScheduler') {
		//require_once dirname(__FILE__) . '/../class/heatzy.class.php';
		ajax::success( HttpGizwits::DeleteScheduler(config::byKey('UserToken','heatzy','none'), init('Did'), init('Id') ) );
    }

    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
	if (version_compare(jeedom::version(), '4.4', '>=')) {
		ajax::error(displayException($e), $e->getCode());
	} else {
		ajax::error(displayExeption($e), $e->getCode());
	}
}
?>