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
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
          <legend><i class="fa fa-list-ul"></i>{{Général}}</legend>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Email}}</label>
            <div class="col-lg-6">
                <input type="text" class="configKey form-control" data-l1key="email" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Mot de passe}}</label>
            <div class="col-lg-6">
                <input type="password" class="configKey form-control" data-l1key="password" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Token}}</label>
            <div class="col-lg-6">
            <?=config::byKey('UserToken','heatzy','');?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Expire}}</label>
            <div class="col-lg-6">
            <?=config::byKey('ExpireToken','heatzy','');?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Frequence de rafaichissement des commandes infos}}</label>
            <div class="col-lg-6">             
                <select class="configKey form-control" data-l1key="Freq_value" title="Fréquence de rafaichissement des commandes depuis Heatzy (1 min par défaut)"  style="width:100px;">
                    <option value="1">1 min</option>
                    <option value="2">2 min</option>
                    <option value="3">3 min</option>
                    <option value="4">4 min</option>
                    <option value="5">5 min</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Frequence de rafaichissement du statut}}</label>
            <div class="col-lg-6">
                <select class="configKey form-control" data-l1key="Freq_status" title="Fréquence de rafaichissement des statuts du module depuis Heatzy (5 min par défaut)"  style="width:100px;">
                    <option value="5">5 min</option>
                    <option value="10">10 min</option>
                    <option value="15">15 min</option>
                    <option value="20">20 min</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Synchroniser}}</label>
            <div class="col-lg-2">
                <a class="btn btn-info bt_syncheatzy"><i id='syncheatzy' class="fa fa-refresh"></i>
                Synchroniser<span id="nbheatzy"></span>
                </a>
            </div>
        </div>
    </fieldset>
</form>

<script>

$('.bt_syncheatzy').on('click',function(){
      $('#div_alert').showAlert({message: 'Synchronisation en cours. Ne pas toucher au plugin pendant 2min', level: 'info'});
      
      $('#syncheatzy').addClass('fa-spin');
      
      $.ajax({// fonction permettant de faire de l'ajax
      type: "POST", // méthode de transmission des données au fichier php
      url: "plugins/heatzy/core/ajax/heatzy.ajax.php", // url du fichier php
      data: {
        action: "SyncHeatzy",
      },
      dataType: 'json',
      global: false,
      error: function (request, status, error) {
        handleAjaxError(request, status, error);
      },
      success: function (data) { // si l'appel a bien fonctionné
      if (data.state != 'ok') {
        $('#div_alert').showAlert({message: data.result, level: 'danger'});
        return;
      }
      else
        $('#div_alert').showAlert({message: 'Synchronisation de '+data.result+' module(s)', level: 'info'});
        $('#nbheatzy').empty();
        $('#nbheatzy').append(' : '+data.result+' module(s)');
    }
      });
      
    $('#syncheatzy').removeClass('fa-spin');
});
</script>

