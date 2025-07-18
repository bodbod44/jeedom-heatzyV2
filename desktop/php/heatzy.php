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
	  <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
	    <center>
	      <i class="fa fa-wrench" style="font-size : 5em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
	  </div>
	  <div class="cursor expertModeVisible" id="bt_healthHeatzy" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
	    <center>
	        <i class="fa fa-medkit" style="font-size : 5em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Santé}}</center></span>
	  </div>
	  <div class="cursor expertModeVisible" id="bt_ManualHeatzy" onclick="window.open('https://drive.google.com/drive/folders/1pbrZ7RRNZf8yzdbH-cd7Fk9ih2j7WZFd', '_blank');" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
	    <center>
	        <i class="fa fa-book-open" style="font-size : 5em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Manuels utilisateur<br>Heatzy}}</center></span>
	  </div>
	  <div class="cursor expertModeVisible" id="bt_AssistanceHeatzy" onclick="window.open('https://community.jeedom.com/tag/plugin-heatzy', '_blank');" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
	    <center>
	        <i class="fa fa-ambulance" style="font-size : 5em;color:#767676;"></i>
	    </center>
	    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Assistance}}</center></span>
	  </div>
	  <div class="cursor expertModeVisible" id="bt_debugHeatzy" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;display:<?php if(log::getLogLevel('heatzy') == 100) echo 'display' ; else echo 'none' ; ?>" >
	    <center>
	        <i class="fa fa-search-location" style="font-size : 5em;color:#767676;"></i>
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
  <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
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
              <label class="col-sm-4 control-label">Template</label>
              <div class="col-sm-6">               
                <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="TypeTemplate" title="bodbod : Nouveau template commun à tous les modules<br>l3flo : Template issu de la version d'origine de l3flo<br>Jeedom : Utilisation du template de base jeedom">
					<option value="0">{{Template heatzy bodbod}}</option>
					<option value="1">{{Template heatzy l3flo (bientôt obselète)}}</option>
					<option value="2">{{Template jeedom}}</option>
				</select>
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

</div>
  <div role="tabpanel" class="tab-pane" id="commandtab">

        <table id="table_cmd" class="table table-bordered table-condensed">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th style="width: 150px;">{{Nom}}</th>
              <th style="width: 110px;">{{Type}}</th>
              <th style="width: 50px;">{{Etat}}</th>
              <th style="width: 200px;">{{Paramètres}}</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
</div>

</div>
</div>

<?php include_file('desktop', 'heatzy', 'js', 'heatzy');?>
<?php include_file('core', 'plugin.template', 'js');?>