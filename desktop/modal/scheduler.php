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
?>

<script>
buffer = "";
<?php
    // Créé un tablmeau avec le nom et l'id de tous les modules
    $eqLogics = heatzy::byType('heatzy') ;
    echo '$tab_heatzy = ['."\n" ;
    foreach ($eqLogics as $eqLogic) {
        echo '["'.$eqLogic->getHumanName().'","'.$eqLogic->getLogicalId().'"],'."\n" ;
    }
    echo '];'."\n" ;
?>  

function GetSchedulers( ){
    // Boucle sur tous lse modules pour récupérer la liste des tâches sur le serveur
    $tab_heatzy.forEach(did => {
        GetSchedulersByDid( did ) ;
    });
};

function GetSchedulersByDid( did ){  
    // Va chercher la liste des tâches pour un module donné
    $.ajax({
        type: 'POST',
        url: 'plugins/heatzy/core/ajax/heatzy.ajax.php', // Chemin vers votre fichier AJAX
        data: {
            action: 'GetSchedulerList', // Nom de l'action à exécuter (voir switch en PHP)
            Did: did[1] ,
            Skip: 0,
            Limit: 20
        },
        dataType: 'json',
        success: function(data) {
            if (data.state != 'ok') {
                // L'appel s'est fait, mais le traitement PHP a échoué (par exemple une exception)
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
          if( data.result != ""){
            InsertLignes( data.result , did[1]  ) ;
          }
        },
        error: function(err) {
            alert("Erreur pour " + did[0] + did[1], err);
        }
    });
};

function CreateScheduler( did , Param ){
    $.ajax({
        type: 'POST',
        url: 'plugins/heatzy/core/ajax/heatzy.ajax.php', // Chemin vers votre fichier AJAX
        data: {
            action: 'CreateScheduler', // Nom de l'action à exécuter (voir switch en PHP)
              Did: did ,
              Param: json_encode(Param)
        },
        dataType: 'json',
        error: function(request, status, error) {
          // Gestion des erreurs
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok' || data.result == false || data.result['error_message'] != undefined ) {
                // L'appel s'est fait, mais le traitement PHP a échoué (par exemple une exception)
              if( data.result['error_message'] != undefined ){
                  alert('Création de la tâche KO' + "\n\nGizwits erreur : " + data.result['error_code'] + '-' + data.result['error_message'] + "\n\nDétail : " + json_encode(data.result['detail_message']) );
                }
              else{
                alert('Création de la tâche KO (' + data.result + ')');
              }
                //$('#div_alert').showAlert({message: 'Création de la tâche KO (' + data.result + ')', level: 'danger'});
                return;
            }
            // Succès : La classe a été appelée et a renvoyé une réponse
    		const LaDate = new Date();
    		Param['created_at'] = LaDate.toISOString().substring(0, 19) ;
          	Param["id"] = data.result["id"] ;
          	Param["did"] = did ;
          	InsertLigne( Param , did ) ;
            RazForm() ; // Reinit le formulaire si appel OK
        }
    });  
}

function UpdateScheduler( did , Id , Param ){
    $.ajax({
        type: 'POST',
        url: 'plugins/heatzy/core/ajax/heatzy.ajax.php', // Chemin vers votre fichier AJAX
        data: {
            action: 'UpdateScheduler', // Nom de l'action à exécuter (voir switch en PHP)
              Did: did ,
          	  Id: Id ,
              Param: json_encode(Param)
        },
        dataType: 'json',
        error: function(request, status, error) {
          // Gestion des erreurs
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok' || data.result == false || data.result['error_message'] != undefined ) {
                // L'appel s'est fait, mais le traitement PHP a échoué (par exemple une exception)
              if( data.result['error_message'] != undefined ){
                  alert('Modification de la tâche KO' + "\n\nGizwits erreur : " + data.result['error_code'] + '-' + data.result['error_message'] + "\n\nDétail : " + json_encode(data.result['detail_message']) );
                }
              else{
                alert('Modification de la tâche KO (' + data.result + ')');
              }
              
                //$('#div_alert').showAlert({message: 'Création de la tâche KO (' + data.result + ')', level: 'danger'});
                return;
            }
            // Succès : La classe a été appelée et a renvoyé une réponse            
            var row = document.getElementById( 'row_' + Id );
    		row.parentNode.removeChild(row);
          
    		const LaDate = new Date();
    		Param['created_at'] = LaDate.toISOString().substring(0, 19) ;
          	Param["id"] = data.result["id"] ;
          	Param["did"] = did ;
          	InsertLigne( Param ) ;
          
            // Reinit le formulaire
            RazForm() ;
        }
    });  
}

function DeleteScheduler( did , Id ){  
    $.ajax({
        type: 'POST',
        url: 'plugins/heatzy/core/ajax/heatzy.ajax.php', // Chemin vers votre fichier AJAX
        data: {
            action: 'DeleteScheduler', // Nom de l'action à exécuter (voir switch en PHP)
              Did: did ,
              Id: Id
        },
        dataType: 'json',
        error: function(request, status, error) {
          // Gestion des erreurs
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                // L'appel s'est fait, mais le traitement PHP a échoué (par exemple une exception)
              alert('suppr ko');
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            // Succès : La classe a été appelée et a renvoyé une réponse
            var row = document.getElementById("row_" + Id);
            row.parentNode.removeChild(row);
            
            if( document.getElementById('myTable_' + did ).rows.length == 1 ){
              document.getElementById('div_' + did ).style.display = "none" ;
            }
        }
    });
}

function InsertLignes( TabScheduler , LogicalId ){
    // Boucle sur la tableau des tâches pour un did
    for (variable of TabScheduler) {
        InsertLigne( variable , LogicalId ) ;
    };
}

function InsertLigne( variable , LogicalId ){
    // Insert les lignes
    document.getElementById('div_' + variable['did']).style.display = "" ;              
    let tbody = document.getElementById('myTable_' + variable["did"]).getElementsByTagName('tbody')[0];              
    let row = tbody.insertRow(); // insère une nouvelle ligne
    row.id = "row_" + variable["id"] ;
    row.insertCell(0).textContent = variable["created_at"] ;
    row.insertCell(1).textContent = variable["date"] ;
    row.insertCell(2).textContent = variable["time"] ;
  	row.insertCell(3).textContent = variable["days"] ;
    row.insertCell(4).textContent = variable["repeat"] ;
    row.insertCell(5).textContent = variable["start_date"] ;
    row.insertCell(6).textContent = variable["end_date"] ;
    row.insertCell(7).textContent = json_encode(variable["attrs"]) ;
    row.insertCell(8).textContent = variable["remark"] ;
    HumanName = 'HumanName' ;
    row.insertCell(9).innerHTML   = "<img src=\"plugins/heatzy/plugin_info/modif.png\" alt=\"xxxx\" width=\"20\" onclick=\"AlimFormUpdate('" + variable["did"] + "' , '" + variable['id'] + "' )\" />&nbsp;<img src=\"plugins/heatzy/plugin_info/delete.png\" alt=\"xxxx\" width=\"25\" onclick=\"DeleteScheduler( '" + variable["did"] + "' , '" + variable["id"] + "' ) ; \" />" ;
}

function VerifFormulaire(){
    // Vérification du contenu du formulaire avant envoi de la requete (Creation ou Update)
    did =  document.getElementById('sel_did').value ;
    Param =  document.getElementById("Param").value ;

    try { // test de la synthaxe du JSON
        JSON.parse(Param);
    } catch (e) {
        alert('Le JSON est vide ou invalide') ;
        return false ;
    }

    // Si textid vide => Création
    if( document.getElementById("text_id").value == "" ){
        if( did == ""){
            alert('Choisir un équipement') ;
            return false ;
        }
        CreateScheduler( did , json_decode(Param) ) ;
    }
    else{ // Sinon modification
        UpdateScheduler( did , document.getElementById("text_id").value , json_decode(Param) )
    }
}

function AlimFormUpdate( LogicalId , id ){
    // Permet de remplir le formulaire avec les données de la tâches existante en vue de la modifier
    document.getElementById('sel_did').disabled = true ;
    document.getElementById('sel_did').value = LogicalId ;
    document.getElementById("sel_exemple").value = "" ;

    // ReConstruction du JSON depuis les infos du tableau html
    Json  = '{' + "\n" ;
    Json += '    "attrs":' + document.getElementById('row_' + id ).cells[7].textContent + ',' + "\n" ;
    if(document.getElementById('row_' + id ).cells[1].textContent != '')
        Json += '    "date": "' + document.getElementById('row_' + id ).cells[1].textContent + '",' + "\n" ;
    if(document.getElementById('row_' + id ).cells[2].textContent != '')
        Json += '    "time": "' + document.getElementById('row_' + id ).cells[2].textContent + '",' + "\n" ;
    if(document.getElementById('row_' + id ).cells[3].textContent != '')
        Json += '    "days": [' + document.getElementById('row_' + id ).cells[3].textContent + '],' + "\n" ;
    if(document.getElementById('row_' + id ).cells[4].textContent != '')
        Json += '    "repeat": "' + document.getElementById('row_' + id ).cells[4].textContent + '",' + "\n" ;
    if(document.getElementById('row_' + id ).cells[5].textContent != '')
        Json += '    "start_date": "' + document.getElementById('row_' + id ).cells[5].textContent + '",' + "\n" ;
    if(document.getElementById('row_' + id ).cells[6].textContent != '')
        Json += '    "end_date": "' + document.getElementById('row_' + id ).cells[6].textContent + '",' + "\n" ;
    Json += '    "remark": "' + document.getElementById('row_' + id ).cells[8].textContent + '"' + "\n" ;
    Json += '}' + "\n" ;

    document.getElementById("Param").value = Json ;
    document.getElementById('text_id').value = id ;
    document.getElementById('btn_form').value = 'Modifier la tâche' ;
}

function RazForm(){
    // Reinit le formulaire
    document.getElementById('sel_did').value = "" ;
    //document.getElementById('sel_did').style.display = "" ;
    document.getElementById('sel_did').disabled = false ;
    document.getElementById("sel_exemple").value = "" ;
    document.getElementById("Param").value = "" ;
    document.getElementById("text_id").value = "" ;
    document.getElementById("btn_form").value = "Créer la tâche" ;
}

function InjecteExemple( selectObject ){
    // Alimente le formulaire avec des exemples
    switch(selectObject.value) {
        case 'unique':
            Json  = "{" + "\n" ;
            Json += '  "attrs": {' + "\n" ;
            Json += '    "mode": 2,' + "\n" ;
            Json += '    "timer_switch": 0,' + "\n" ;
            Json += '    "derog_mode": 2,' + "\n" ;
            Json += '    "derog_time": 180,' + "\n" ;
            Json += '    "cft_temp": 190,' + "\n" ;
            Json += '    "eco_temp": 170' + "\n" ;
            Json += '   },' + "\n" ;
            Json += '  "date": "2026-04-01",' + "\n" ;
            Json += '  "time": "09:00",' + "\n" ;
            Json += '  "repeat": "none",' + "\n" ;
            Json += '  "start_date": "2025-09-30",' + "\n" ;
            Json += '  "end_date": "2026-09-30",' + "\n" ;
            Json += '  "remark": "Éteindre le radiateur a la fin de l hiver"' + "\n" ;
            Json += '}' ;
        	document.getElementById ('Param').value = Json ;
            break;
        case 'hebdo':
            Json  = '{' + "\n" ;
            Json += '	"attrs":{' + "\n" ;
            Json += '		"derog_mode": 3' + "\n" ;
            Json += '    },' + "\n" ;
            Json += '    "repeat": "mon, tue, wed, thu, fri, sat, sun"' + "\n" ;
            Json += '    "time": "09:00",' + "\n" ;
            Json += '    "start_date": "2025-08-30",' + "\n" ;
            Json += '    "end_date": "2025-11-30",' + "\n" ;
            Json += '    "remark": "Passage en mode détection de présence du Pilote Pro en semaine"' + "\n" ;
            Json += '}' + "\n" ;
            document.getElementById ('Param').value = Json ;
            break;
        case 'mens':
            Json  ='{' + "\n" ;
            Json += '	"attrs":{' + "\n" ;
            Json += '		"timer_switch":1,' + "\n" ;
            Json += '		"derog_mode":0' + "\n" ;
            Json += '	},' + "\n" ;
            Json += '	"days":[1, 2],' + "\n" ;
            Json += '	"time": "09:00",' + "\n" ;
            Json += '	"repeat": "day",' + "\n" ;
            Json += '	"start_date": "2025-01-01",' + "\n" ;
            Json += '	"end_date": "2026-01-01",' + "\n" ;
            Json += '	"remark": "Activer la programmation au début de chaque mois"' + "\n" ;
            Json += '}' + "\n" ;
            document.getElementById ('Param').value = Json ;
            break;
        default:
            document.getElementById ('Param').value = '' ;
    }
}
</script>
  
  
<form>
    <ol>
        <li>
            <label style="width: 100px;">Equipement&nbsp;:</label>
            <select name="sel_did" id="sel_did" style="width: 350px;">
                <option value="">--Please choose an option--</option>
                <?php
                    foreach ($eqLogics as $eqLogic) {
                        echo "	<option value=\"".$eqLogic->getLogicalId()."\">".$eqLogic->getHumanName()."</option>" ;
                }
                ?>
            </select>
        </li>
        <li>
            <label style="width: 100px;">Exemple&nbsp;:</label>
            <select name="sel_exemple" id="sel_exemple" style="width: 350px;" OnChange="InjecteExemple(this);">
                <option value="">Vide (je suis un expert)</option>
                <option value="unique">Une tâche unique</option>
                <option value="hebdo">Une tâche hebdomadaire</option>
                <option value="mens">Une tâche mensuelle</option>
            </select>  
            &nbsp;<A href="https://docs.google.com/document/d/1RS8ipOUARmT8Lwxh-avWTm_rJkiWb2MltTRRRTlAvIc/edit?tab=t.0#heading=h.g256wx5fq9jy" target="_blank"><I>Documentation API Heatzy</I></A>
        </li>
        <li>
            <label style="width: 100px;">Parametres&nbsp;:</label>
            <textarea id="Param" name="Param" rows="15" cols="120" style="font-family:Courier New;"></textarea>
        </li>
        <li>
            <label style="width: 100px;">Envoyer&nbsp;:</label>
            <input type="text" id="text_id" value="" style="display:none;" />
            <input type="button" id="btn_form" value="Créer la tâche"  OnClick="VerifFormulaire()"/>&nbsp;&nbsp;&nbsp;
            <input type="button" id="btn_razform" value="RAZ Formulaire"  OnClick="RazForm()"/>
        </li>
    </ol>
</form>
  
<BR>
          
<?php
    echo '<i>Les équipents non affichés ne possèdent pas des tâches</i>' ;
    foreach ($eqLogics as $eqLogic) {
        echo '<div id="div_'.$eqLogic->getLogicalId().'" style="display:none;">' ;
        echo '</br>&nbsp;';
        echo '<h4>'.$eqLogic->getHumanName().'</h4>';
        echo '<table  class="table table-condensed tablesorter" id="myTable_'.$eqLogic->getLogicalId().'">
            <thead>
                <tr>
                    <th>Date création</th>
                    <th>date</th>
                    <th>time</th>
                    <th>days</th>
                    <th>repeat</th>
                    <th>start_date</th>
                    <th>end_date</th>
                    <th>attrs</th>
                    <th>remark</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>' ;
        echo '</div>' ;
    }
?>
  
<script>
	GetSchedulers() ;
</script>