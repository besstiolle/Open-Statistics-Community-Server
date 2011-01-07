<?php

if (!isset($gCms)) exit;

$prettyUrlRapport = "statistiques/modules/";

require("inc.header.php");

/* ############## PARTIE A FAIRE EVOLUER ################ */
$syntheseCmsModule = unserialize($arraySerialise[2]);

//On refait une passe sur la liste des modules pour mettre a jour les liens et le parametre $mois
foreach ($syntheseCmsModule as $module)
{
	$prettyUrl = "statistiques/modules/".$returnid."/".$module->name."/".$params['mois'];
	$detailLien = $this->CreateLink($id, 'showModule', $returnid, $module->name, array("mois"=>$params['mois'],"mod"=>$module->name),'',false,true,'',false,$prettyUrl);
	$module->detailLien = $detailLien;
}

$smarty->assign('syntheseCmsModule',$syntheseCmsModule);
/* ############## PARTIE A FAIRE EVOLUER ################ */



$smarty->assign('mois', $this->_getMoisLiteral($mois));
$smarty->assign_by_ref('module',$this);

//echo $this->ProcessTemplate('showListeModule.tpl');

#Display template
echo "<!-- Displaying OSCS Module -->\n";
$template = 'showListeModule'.$this->GetPreference('current_showListeModule_template');
if (isset($params['template']))
  {
    $template = 'showListeModule'.$params['template'];
  }
echo $this->ProcessTemplateFromDatabase($template);
echo "<!-- END OSCS Module -->\n";
?>