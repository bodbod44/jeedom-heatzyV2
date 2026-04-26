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
            <label class="col-lg-4 control-label">{{Email}}&nbsp;<sup><i class="fas fa-question-circle tooltips" title="{{Email du compte heatzy)}}"></i></sup></label>
            <div class="col-lg-6">
                <input type="text" class="configKey form-control" data-l1key="email" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Mot de passe}}&nbsp;<sup><i class="fas fa-question-circle tooltips" title="{{Mot de passe Heatzy utilisé pour se connecter sur l'application officielle}}"></i></sup></label>
            <div class="col-lg-6">
                <input type="password" class="configKey form-control" data-l1key="password" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{uid}}</label>
            <div class="col-lg-6">
            <?=config::byKey('uid','heatzy','');?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Token}}</label>
            <div class="col-lg-6">
            <?=config::byKey('UserToken','heatzy','');?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Expire}}&nbsp;<sup><i class="fas fa-question-circle tooltips" title="{{Date à laquelle le token expirera (sera renouvellé automatiquement par le plugin)}}"></i></sup></label>
            <div class="col-lg-6">
            <?=config::byKey('ExpireToken','heatzy','');?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Type d API utilisé}}</label>
            <div class="col-lg-6">             
                <select class="configKey form-control" data-l1key="API_Type" title="Type d API utilisé" style="width:100px;">
                    <option value="REST">API REST</option>
                    <option value="WS">WebSocket</option>
                </select>
            </div>
        </div>
        <div class="form-group" id="socketport">
            <label class="col-lg-4 control-label">{{Port demon}}&nbsp;<sup><i class="fas fa-question-circle tooltips" title="{{Port utilisé pour le demon.<br> Si vous le videz, le plugin ira le valoriser avec un port non utilisé}}"></i></sup></label>
            <div class="col-lg-6">
                <input type="text" class="configKey form-control" data-l1key="socketport" style="width:100px;" />
            </div>
        </div>
        <div class="form-group" id="Timeout_value">
            <label class="col-lg-4 control-label">{{Timeout sur les connexions euapi.gizwits.com}}</label>
            <div class="col-lg-6">             
                <select class="configKey form-control" data-l1key="Timeout_value" title="Timeout sur les connexions euapi.gizwits.com (60sec par défaut)" style="width:100px;">
                    <option value="5">5 sec</option>
                    <option value="10">10 sec</option>
                    <option value="30">30 sec</option>
                    <option value="60">60 sec</option>
                    <option value="90">90 sec</option>
                    <option value="120">120 sec</option>
                </select>
            </div>
        </div>
        <div class="form-group" id="Freq_value">
            <label class="col-lg-4 control-label">{{Frequence de rafaichissement des commandes infos}}</label>
            <div class="col-lg-6">             
                <select class="configKey form-control" data-l1key="Freq_value" title="Fréquence de rafaichissement des commandes depuis Heatzy (2 min par défaut)" style="width:100px;">
                    <option value="0">Off</option>
                    <option value="1">1 min</option>
                    <option value="2">2 min</option>
                    <option value="3">3 min</option>
                    <option value="4">4 min</option>
                    <option value="5">5 min</option>
                </select>
            </div>
        </div>
        <div class="form-group" id="Freq_status">
            <label class="col-lg-4 control-label">{{Frequence de rafaichissement du statut}}</label>
            <div class="col-lg-6">
                <select class="configKey form-control" data-l1key="Freq_status" title="Fréquence de rafaichissement des statuts du module depuis Heatzy (30 min par défaut)"  style="width:100px;">
                    <option value="0">Off</option>
                    <option value="5">5 min</option>
                    <option value="10">10 min</option>
                    <option value="15">15 min</option>
                    <option value="20">20 min</option>
                    <option value="30">30 min</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Synchroniser}}&nbsp;<sup><i class="fas fa-question-circle tooltips" title="{{Récupère les modules présents sur le compte Heatzy et créer les commandes associées}}"></i></sup></label>
            <div class="col-lg-2">
                <a class="btn btn-info bt_syncheatzy"><i id='syncheatzy' class="fa fa-refresh"></i>
                Synchroniser les modules avec le compte Heatzy<span id="nbheatzy"></span>
                </a>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Création des commandes}}&nbsp;<sup><i class="fas fa-question-circle tooltips" title="{{Création des commandes par reconnaissance et apprentissage. Attention, cela peut prendre plusieurs minutes !!!}}"></i></sup></label>
            <div class="col-lg-2">
                <a class="btn btn-info bt_syncheatzybylearn"><i id='syncheatzybylearn' class="fa fa-refresh"></i>
                Création des commandes par apprentissage<span id="nbheatzybylearn"></span>
                </a>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Réinitialiser l'ordre des commandes}}&nbsp;<sup><i class="fas fa-question-circle tooltips" title="{{Réinitialiser l'ordre des commandes}}"></i></sup></label>
            <div class="col-lg-2">
                <a class="btn btn-info bt_syncheatzyorder"><i id='syncheatzyorder' class="fa fa-refresh"></i>
                Réinitialiser l''ordre des commandes<span id="nbheatzyorder"></span>
                </a>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Réinitialiser le nom des commandes}}&nbsp;<sup><i class="fas fa-question-circle tooltips" title="{{Réinitialiser le nom des commandes}}"></i></sup></label>
            <div class="col-lg-2">
                <a class="btn btn-info bt_syncheatzyname"><i id='syncheatzyname' class="fa fa-refresh"></i>
                Réinitialiser le nom des commandes<span id="nbheatzyname"></span>
                </a>
            </div>
        </div>
    </fieldset>
</form>

<script>

$('.bt_syncheatzy').on('click',function(){
    //$('#div_alert').showAlert({message: 'Synchronisation en cours...', level: 'info'});
    $.fn.showAlert({message: 'Synchronisation en cours...', level: 'info'});

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
                //$('#div_alert').showAlert({message: data.result, level: 'danger'});
                $.fn.showAlert({message: data.result, level: 'danger'});
                return;
            }
          
            $('#nbheatzy').empty();
            if( typeof data.result === 'object' ){
                ResultMessage = 'Apprentisssage : ' + data.result['new'] + ' module(s) créé(s) + ' + data.result['update'] + ' module(s) actualisé(s) + ' + data.result['delete'] + ' module(s) désactivé(s)' ;
                //$('#div_alert').showAlert({message: ResultMessage, level: 'info'});
                $.fn.showAlert({message: ResultMessage, level: 'info'});
                $('#nbheatzy').append(' : ' + ResultMessage );
            }
            else{
                //$('#div_alert').showAlert({message: 'Synchronisation de ' + data.result + ' module(s)', level: 'info'});
                $.fn.showAlert({message: 'Synchronisation de ' + data.result + ' module(s)', level: 'info'});
                $('#nbheatzy').append(' : ' + data.result + ' module(s)');
            }
        }
    });

    $('#syncheatzy').removeClass('fa-spin');
});

$('.bt_syncheatzybylearn').on('click',function(){
    //$('#div_alert').showAlert({message: 'Lancement de l\'apprentisssage... Veuillez patienter', level: 'info'});
    $.fn.showAlert({message: 'Lancement de l\'apprentisssage... Veuillez patienter', level: 'info'});

    $('#syncheatzybylearn').addClass('fa-spin');

    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/heatzy/core/ajax/heatzy.ajax.php", // url du fichier php
        data: {
            action: "SyncheatzyByLearning",
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                //$('#div_alert').showAlert({message: data.result, level: 'danger'});
                $.fn.showAlert({message: data.result, level: 'danger'});
                return;
            }
            
            $('#nbheatzybylearn').empty();
            if( typeof data.result === 'object' ){
                ResultMessage = data.result['cmd'] + ' commandes créée(s)' ;
                //$('#div_alert').showAlert({message: 'Apprentisssage : ' + ResultMessage , level: 'info'});
                $.fn.showAlert({message: 'Apprentisssage : ' + ResultMessage , level: 'info'});
                $('#nbheatzybylearn').append(' : ' + ResultMessage );
            }
            else{
                //$('#div_alert').showAlert({message: 'Apprentisssage : ' + data.result + ' commandes créée(s)', level: 'info'});
                $.fn.showAlert({message: 'Apprentisssage : ' + data.result + ' commandes créée(s)', level: 'info'});
                $('#nbheatzybylearn').append(' : ' + data.result + ' commandes créée(s)');
            }
        }
    });

    $('#syncheatzybylearn').removeClass('fa-spin');
});

$('.bt_syncheatzyorder').on('click',function(){
    $('#syncheatzyorder').addClass('fa-spin');

    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/heatzy/core/ajax/heatzy.ajax.php", // url du fichier php
        data: {
            action: "SyncheatzyUpdate",
            mode: "order",
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                //$('#div_alert').showAlert({message: data.result, level: 'danger'});
                $.fn.showAlert({message: data.result, level: 'danger'});
                return;
            }            
            //$('#div_alert').showAlert({message: 'Tous les commandes ont été triées', level: 'info'});
            $.fn.showAlert({message: 'Tous les commandes ont été triées', level: 'info'});
        }
    });

    $('#syncheatzyorder').removeClass('fa-spin');
});

$('.bt_syncheatzyname').on('click',function(){
    $('#syncheatzyname').addClass('fa-spin');

    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/heatzy/core/ajax/heatzy.ajax.php", // url du fichier php
        data: {
            action: "SyncheatzyUpdate",
            mode: "name",
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                //$('#div_alert').showAlert({message: data.result, level: 'danger'});
                $.fn.showAlert({message: data.result, level: 'danger'});
                return;
            }
            //$('#div_alert').showAlert({message: 'Tous les commandes ont été renommées', level: 'info'});
            $.fn.showAlert({message: 'Tous les commandes ont été renommées', level: 'info'});
        }
    });

    $('#syncheatzyname').removeClass('fa-spin');
});

/*
function sleep(ms) {
  const start = Date.now();
  while (Date.now() - start < ms) {}
}*/


$('a#bt_savePluginLogConfig').on('click', function() { 

  //$('#div_alert').showAlert({message: 'Relance du demon...', level: 'info'});
  $.fn.showAlert({message: 'Relance du demon...', level: 'info'});
  //sleep( 3000 ) ;
  asyncCall() ;
    //$('#div_alert').showAlert({message: 'FIN', level: 'info'});
});

async function asyncCall() {
    await AppelAJAXStart();
}

function AppelAJAXStart() {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve("resolved");

            $.ajax({// fonction permettant de faire de l'ajax
                type: "POST", // méthode de transmission des données au fichier php
                url: "plugins/heatzy/core/ajax/heatzy.ajax.php", // url du fichier php
                data: {
                    action: "deamon_start",
                },
                dataType: 'json',
                global: false,
                error: function (request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function (data) { // si l'appel a bien fonctionné
                    if (data.state != 'ok') {
                        //$('#div_alert').showAlert({message: data.result, level: 'danger'});
                        $.fn.showAlert({message: data.result, level: 'danger'});
                        return;
                    }
                    //$('#div_alert').showAlert({message: 'AJAX OK', level: 'info'});
                }
            });
        }, 1000);
    });
}


function getPort() {
            $.ajax({// fonction permettant de faire de l'ajax
                type: "POST", // méthode de transmission des données au fichier php
                url: "plugins/heatzy/core/ajax/heatzy.ajax.php", // url du fichier php
                data: {
                    action: "getPort",
                },
                dataType: 'json',
                global: false,
                error: function (request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function (data) { // si l'appel a bien fonctionné
                    if (data.state != 'ok') {
                        //$('#div_alert').showAlert({message: data.result, level: 'danger'});
                        $.fn.showAlert({message: data.result, level: 'danger'});
                        return;
                    }
                    //$('#div_alert').showAlert({message: 'AJAX OK', level: 'info'});
                    alert( data.result ) ;
                }
            });
}

function getUsedPort() {
            alert('ok') ;
  				$.ajax({// fonction permettant de faire de l'ajax
                type: "POST", // méthode de transmission des données au fichier php
                url: "plugins/heatzy/core/ajax/heatzy.ajax.php", // url du fichier php
                data: {
                    action: "getUsedPort",
                },
                dataType: 'json',
                global: false,
                error: function (request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function (data) { // si l'appel a bien fonctionné
                    if (data.state != 'ok') {
                        //$('#div_alert').showAlert({message: data.result, level: 'danger'});
                        $.fn.showAlert({message: data.result, level: 'danger'});
                        return;
                    }
                    //$('#div_alert').showAlert({message: 'AJAX OK', level: 'info'});
                    //alert( data.result ) ;
                    
                    $('#UsedPort').empty();
                    $('#UsedPort').append( data.result );
                    
                }
            });
}


$('a#bt_savePluginConfig').on('click', function() { 
  //  val = $('input[data-l1key="socketport"]').setvalue('999') ;
  //  val = $('.eqLogicAttr[data-l1key="socketport"]').value() ;
  //alert('-' + val) ;
});



$('[data-l1key="API_Type"]').on('change',function(){
	//alert( $('select[data-l1key="API_Type"]').value() ) ;
  if( $('select[data-l1key="API_Type"]').value() == "WS" ){
    $('div[id="socketport"]'   ).attr('style', 'display:xxx;') ;
	$('div[id="Timeout_value"]').attr('style', 'display:none;') ;
    $('div[id="Freq_value"]'   ).attr('style', 'display:none;') ;
    $('div[id="Freq_status"]'  ).attr('style', 'display:none;') ;
  }
  else{
    $('div[id="socketport"]'   ).attr('style', 'display:none;') ;
	$('div[id="Timeout_value"]').attr('style', 'display:xxx;') ;
    $('div[id="Freq_value"]'   ).attr('style', 'display:xxx;') ;
    $('div[id="Freq_status"]'  ).attr('style', 'display:xxx;') ;
  }
});

jeedom.config.load({
    configuration: 'log::level::heatzy',
    success: function(level) {
        console.log('Niveau de log sélectionné :', level);
    },
    error: function(error) {
        console.error(error);
    }
});

</script>