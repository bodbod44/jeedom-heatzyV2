<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('heatzy');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
  <div class="col-lg-2">
    <div class="bs-sidebar">
      <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
        <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
        <?php
foreach ($eqLogics as $eqLogic) {
	echo '<li style="text-align: left;" class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
?>
     </ul>
   </div>
 </div>
 <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
   <legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
   <div class="eqLogicThumbnailContainer">
  
	  <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 150px;margin-left : 10px;">
	    <center>
	      <i class="fa fa-wrench" style="font-size : 4em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
	  </div>
  
	  <div class="cursor expertModeVisible" id="bt_healthHeatzy" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 150px;margin-left : 10px;" >
	    <center>
	        <i class="fa fa-medkit" style="font-size : 4em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Santé}}</center></span>
	  </div>
  
	  <div class="cursor expertModeVisible" id="bt_DocPlugin" onclick="window.open('https://bodbod44.github.io/jeedom-heatzyV2/fr_FR/', '_blank');" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 150px;margin-left : 10px;" >
	    <center>
	        <i class="fa fa-book-open" style="font-size : 4em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Documentation<br>plugin}}</center></span>
	  </div>
  
	  <div class="cursor expertModeVisible" id="bt_ManualHeatzy" onclick="window.open('https://drive.google.com/drive/folders/1pbrZ7RRNZf8yzdbH-cd7Fk9ih2j7WZFd', '_blank');" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 150px;margin-left : 10px;" >
	    <center>
	        <i class="fa fa-book-reader" style="font-size : 4em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Manuels utilisateur<br>Heatzy}}</center></span>
	  </div>
  
	  <div class="cursor expertModeVisible" id="bt_AssistanceHeatzy" onclick="window.open('https://community.jeedom.com/tag/plugin-heatzy', '_blank');" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 150px;margin-left : 10px;" >
	    <center>
	        <i class="fa fa-ambulance" style="font-size : 4em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Assistance}}</center></span>
	  </div>
  
	  <div class="cursor expertModeVisible" id="bt_debugHeatzy" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 150px;margin-left : 10px;display:<?php if(log::getLogLevel('heatzy') == 100) echo 'display' ; else echo 'none' ; ?>" >
	    <center>
	        <i class="fa fa-search-location" style="font-size : 4em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Debug}}</center></span>
	  </div>
  
</div>
<legend><i class="fa fa-cube"></i>  {{Mes Heatzy}}
</legend>
<div class="eqLogicThumbnailContainer">
  <?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
	echo "<center>";
	if(file_exists('plugins/heatzy/core/template/images/'.$eqLogic->getConfiguration('product', '').'.png'))
		echo '<img src="plugins/heatzy/core/template/images/'.$eqLogic->getConfiguration('product', '').'.png" width="100" height="100"/>'; // Logo personnalisé
	else if($eqLogic->getConfiguration('product', '')=='Flam_Week2') 	        /// Pour heatzy INEA
		echo '<img src="plugins/heatzy/core/template/images/LOGO_FLAM.png" width="100" height="100"/>';
	else
		echo '<img src="plugins/heatzy/core/template/images/LOGO_PILOTE.png" width="100" height="100"/>';
	echo "</center>";
	echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
	echo '</div>';
}
?>
</div>
</div>

<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
 <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
 <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>

 <ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Équipement}}</a></li>
  <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab" onclick="AffichageTemplateBodbod();"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  <li role="presentation"><a href="#paramtab" aria-controls="param" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> {{Paramètres}}</a></li>
</ul>

<div class="tab-content" style="height:calc(100% - 90px);overflow:auto;overflow-x: hidden;">
<!-- <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;"> -->
  <div role="tabpanel" class="tab-pane active" id="eqlogictab">
	<div class="row">
	<div class="col-sm-7">
    <form class="form-horizontal">
      <fieldset>
        <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}  <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
        <div class="form-group">
          <label class="col-sm-4 control-label">{{Nom de l'équipement}}</label>
          <div class="col-sm-6">
            <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
            <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label" >{{Objet parent}}</label>
          <div class="col-sm-6">
            <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
              <option value="">{{Aucun}}</option>
              <?php
foreach (jeeObject::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
           </select>
         </div>
       </div>
       <div class="form-group">
        <label class="col-sm-4 control-label">{{Catégorie}}</label>
        <div class="col-sm-8">
          <?php
foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
	echo '<label class="checkbox-inline">';
	echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
	echo '</label>';
}
?>
       </div>
     </div>
     <div class="form-group">
              <label class="col-sm-4 control-label"></label>
              <div class="col-sm-6">
                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
            </div>
     </div>
	<div class="form-group">
    	<label class="col-sm-4 control-label">{{MAC}}</label>
        <div class="col-sm-6">
        	<input type="text" class="form-control eqLogicAttr" data-l1key="configuration" data-l2key="mac"/>
        </div>
    </div>
	<div class="form-group expertModeVisible">
    	<label class="col-sm-4 control-label">{{DID}}</label>
        <div class="col-sm-6">
			<input type="text" class="eqLogicAttr form-control" data-l1key="logicalId"/>
        </div>
    </div>
	<div class="form-group">
    	<label class="col-sm-4 control-label">{{Commentaire}}</label>
        <div class="col-sm-6">
        	<textarea class="eqLogicAttr form-control" data-l1key="comment" ></textarea>
		</div>
	</div>
  </fieldset>
</form>
</div>
<div class="col-sm-5">
    <form class="form-horizontal">
        <fieldset>
            <legend><i class="fa fa-info-circle"></i> {{Informations}}</legend>
            
			<div class="form-group">
				<label class="col-sm-3 control-label">{{Création}}</label>
				<div class="col-sm-3">
					<span class="eqLogicAttr label label-default" data-l1key="configuration" data-l2key="createtime" title="{{Date de création de l'équipement}}" style="font-size : 1em;cursor : default;"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{{Dernière communication}}</label>
				<div class="col-sm-3">
					<span class="eqLogicAttr label label-default" data-l1key="configuration" data-l2key="lastCommunication" title="{{Date de dernière communication}}" style="font-size : 1em;cursor : default;"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{{Dernière mise à jour}}</label>
				<div class="col-sm-3">
					<span class="eqLogicAttr label label-default" data-l1key="configuration" data-l2key="updatetime" title="{{Date de dernière communication}}" style="font-size : 1em;cursor : default;"></span>
				</div>
			</div>
            <div class="form-group">
				<label class="col-sm-3 control-label">{{Produit}}</label>
				<div class="col-sm-3">
					<span class="eqLogicAttr label label-default" data-l1key="configuration" data-l2key="product" title="{{Type de produit}}" style="font-size : 1em;cursor : default;"></span>
				</div>
			</div>
            <div class="form-group">
				<label class="col-sm-3 control-label">{{Clé du produit}}</label>
				<div class="col-sm-3">
					<span class="eqLogicAttr label label-default" data-l1key="configuration" data-l2key="product_key" title="{{Clé du produit}}" style="font-size : 1em;cursor : default;"></span>
				</div>
			</div>
		</fieldset>
	</form>
</div>
    </div>

  </div><div role="tabpanel" class="tab-pane" id="commandtab">
	<table id="table_cmd" class="table table-bordered table-condensed">
		<thead>
			<tr>
				<th style="width: 25px;">
					#
				</th>
				<th style="width: 50px;">
					{{Nom}}
				</th>
				<th style="width: 150px;">
					{{Type / subType}}
				</th>
				<th style="width: 50px;">
					{{Etat}}
				</th>
				<th style="width: 50px;">
					{{Affichage}}
				</th>
				<th style="width: 50px;">
					{{Historique}}
				</th>
				<th style="width: 50px;">
					{{Paramètres}}
				</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<div role="tabpanel" class="tab-pane" id="paramtab">
	<br>
	<table style="border:1px solid black; width:100%" id="table_param2">
		<thead>
			<tr>
				<th>
					Choix du template d'affichage
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="width:120px;"><i class="fas fa-info"></i>&nbsp;{{Information}}</label>
						<div class="col-sm-2 alert alert-info" style="width:100%;">
							Ce parametre permet de choisir le template pemettant d'affiche les modules dans jeedom (bouton, image ...)
							<ul>
								<li>bodbod : Nouveau template commun à tous les modules</li>
								<li>l3flo : Template issu de la version d'origine de l3flo (Ces templates ne sont plus maintenus. Il seront supprimés dans une prochaine version du plugin)</li>
								<li>Jeedom : Utilisation du template de base jeedom</li>
							</ul>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group">
						<label class="col-sm-4 control-label" style="width:120px;">Template</label>
						<div class="col-sm-6">
							<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="TypeTemplate" title=""  style="width:300px;" onchange="AffichageTemplateBodbod()">
								<option value="0">{{Template heatzy bodbod}}</option>
								<option value="1">{{Template heatzy l3flo (bientôt obselète)}}</option>
								<option value="2">{{Template jeedom}}</option>
							</select>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table style="border:1px solid black; width:100%" id="table_param">
		<thead>
			<tr>
				<th>
					Utilisation de capteurs externes
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<div class="form-group">
						<label class="col-sm-5 control-label" style="width:110px;"><i class="fas fa-info"></i>&nbsp;{{Important}}</label>
						<div class="col-sm-5 alert alert-info" style="width:100%">
							Pour les modules qui ne possèdent pas de capteur de température, il est possible d'utiliser ce parametre pour utilisateur un autre capteur présent dans jeedom.
							<br>
							Pour les modules qui possèdent déjà un capteur de température interne (Pro, Glow, Shine ...), le capteur externe sera pris par le plugin et le capteur interne sera ignoré (le module lui même continuera de fonctionner avec son propre catpeur interne).
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="width:250px;">{{Capteur de température (facultatif)}}</label>
						<div class="input-group col-sm-2">
							<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="CapteurExtTemp" style="width:400px;">
							<span class="input-group-btn">
								<a class="btn btn-default btn-sm listCmdInfo"><i class="fa fa-list-alt"></i></a>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="width:250px;">{{Capteur d'humidité (facultatif)}}</label>
						<div class="input-group col-sm-2">
							<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="CapteurExtHumi" style="width:400px;">
							<span class="input-group-btn">
								<a class="btn btn-default btn-sm listCmdInfo"><i class="fa fa-list-alt"></i></a>
							</span>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table style="border:1px solid black; width:100%" id="table_param2">
		<thead>
			<tr>
				<th>
					Détéction de fenetre ouverte / Tendance température - <span style="color:red;">EXPERIMENTAL</span>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<div class="form-group">
						<label class="col-sm-5 control-label" style="width:110px;"><i class="fas fa-info"></i>&nbsp;{{Important}}</label>
						<div class="col-sm-5 alert alert-info" style="width:100%">
							La detection ne peut fonctionner que si un capteur interne ou externe est parametré.
							<br>
							L'historisation de la commande de température doit impérativement être active pour pouvoir faire les calculs (à cocher dans la liste des commandes sur "température").
							<br>
							Pour les modules qui ont cette fonctionnalité en propre, le reglage est une chute de 2° en moins de 5 min (coef tendance = 2/5 = 0.4)
							<br>
							La commande d'alerte sert a être alerté en cas de chute de température (fenêtre ouverte en hiver)
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="width:200px;">{{Nombre de degré (°c)}}</label>
						<div class="col-sm-2" style="width:70px;">
							<input type="number" class="form-control eqLogicAttr" min="1" max="5" data-l1key="configuration" data-l2key="TendanceDegre"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"  style="width:110px;">{{Durée (min)}}</label>
						<div class="col-sm-2" style="width:70px;">
							<input type="text" class="form-control eqLogicAttr" data-l1key="configuration" data-l2key="TendanceDuree"/>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table style="border:1px solid black; width:100%;display:none;" id="table_param2">
		<thead>
			<tr>
				<th>
					Alertes
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<div class="form-group">
						<label class="col-sm-5 control-label" style="width:110px;"><i class="fas fa-info"></i>&nbsp;{{Important}}</label>
						<div class="col-sm-5 alert alert-info" style="width:100%">
							La detection ne peut fonctionner que si un capteur interne ou externe est parametré.
							<br>
							L'historisation de la commande de température doit impérativement être active pour pouvoir faire les calculs (à cocher dans la liste des commandes sur "température").
							<br>
							Pour les modules qui ont cette fonctionnalité en propre, le reglage est une chute de 2° en moins de 5 min (coef tendance = 2/5 = 0.4)
							<br>
							La commande d'alerte sert a être alerté en cas de chute de température (fenêtre ouverte en hiver)
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="width:280px;">{{Commande d'alerte Fenetre ouverte}}</label>
						<div class="input-group col-sm-2">
							<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="CmdAlerteFenetre" style="width:400px;">
							<span class="input-group-btn">
								<a class="btn btn-default btn-sm listCmdInfo"><i class="fa fa-list-alt"></i></a>
							</span>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="width:280px;">{{Commande d'alerte module offline}}</label>
						<div class="input-group col-sm-2">
							<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="CmdAlerteOffline" style="width:400px;">
							<span class="input-group-btn">
								<a class="btn btn-default btn-sm listCmdInfo"><i class="fa fa-list-alt"></i></a>
							</span>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
 						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-info col-xs-10 col-xs-offset-1">
									{{Décider des actions à entreprendre en cas de défaillance du chauffage/climatisation.}}
									<br>{{Le système de chauffage/climatisation est considéré défaillant quand les températures minimales ou maximales définies sur l'onglet}} <strong>{{Équipement}}</strong> {{sont dépassées ou en fonction des marges de défaillance définies dans l'onglet}} <strong>{{Avancé}}</strong>.
								</div>
								<a class="btn btn-success addFailureActuator col-xs-6 col-xs-offset-3" data-type="failureActuator"><i class="fas fa-plus-circle"></i> {{Ajouter une action de défaillance}}</a>
								<div id="div_failureActuator"></div>
							</fieldset>
						</form>
				</td>
			</tr>
		</tbody>
	</table>
</div>
      
      
    </div>
    
</div>

</div>
</div>

<?php include_file('desktop', 'heatzy', 'js', 'heatzy');?>
<?php include_file('core', 'plugin.template', 'js');?>


<script>

    
    $(".tab-pane").off('click','.listCmdInfo').on('click','.listCmdInfo', function () {
    var el = $(this).closest('.form-group').find('.eqLogicAttr');
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
        if (el.attr('data-concat') == 1) {
        el.atCaret('insert', result.human);
        } else {
        el.value(result.human);
        }
        });
    });

    function AffichageTemplateBodbod() {
      //const selectTypeTemplate = $('[data-l1key="configuration"][data-l2key="TypeTemplate"] option:selected') ;
      if( $('[data-l1key="configuration"][data-l2key="TypeTemplate"] option:selected').val() == 0 )
        $('.class_ExclureBodbod').attr('style', 'display:none;');
      else
        $('.class_ExclureBodbod').attr('style', 'display:xxx;');
      //alert( $('[data-l1key="configuration"][data-l2key="TypeTemplate"] option:selected').length + '-' + $('.class_inutile').length ) ;
      //if( $('[data-l1key="configuration"][data-l2key="TypeTemplate"] option:selected').length == 1 && $('.class_inutile').length == 0 )
      //setTimeout(AffichageTemplateBodbod, 3000);
      
      //console.log( $('.eqLogicAttr[data-l1key="name"]').val() + '-' + $('[data-l1key="configuration"][data-l2key="TypeTemplate"] option:selected').length + '-' + $('.class_inutile').length ) ;
    }
    window.onload = AffichageTemplateBodbod; //note bien l'abscence de ()

</script>