<?php

if (!isset($gCms)) exit;

$listeFichier = $this->_getAllCache();

$mois = $this->_getMois($params['mois']);
if($mois == null)
{
	echo "les statiques de ce mois ne sont pas encore g&eacute;n&eacute;r&eacute;e :)";
	return;
}

$prettyUrl = "statistiques/modules/".$returnid."/".$mois;
$smarty->assign('linkListeModule',$this->CreateLink($id, '', '', 'Liste des modules install&eacute;s',array(),'',false,true,'',false, $prettyUrl));

$prettyUrl = "statistiques/".$returnid."/".$mois;
$smarty->assign('linkDefault',$this->CreateLink($id, '', '', 'Sommaire',array(),'',false,true,'',false, $prettyUrl));

$prettyUrl = "statistiques/top/".$returnid."/".$mois;
$smarty->assign('linkTop',$this->CreateLink($id, '', '', 'Autres statistiques Cms',array(),'',false,true,'',false, $prettyUrl));

$listeMois = array();
foreach($listeFichier as $fichier)
{
	$obj = new stdClass;
	$prettyUrl = $prettyUrlRapport.$returnid."/".$fichier;
	$obj->generatelink = $this->CreateLink($id, '', '', $this->_getMoisLiteral($fichier) ,array(),'',false,true,'',false, $prettyUrl);
	$listeMois[] = $obj;
}	
$smarty->assign('listeMois',$listeMois);

$arraySerialise = $this->_getCacheByMonthToArray($mois);

echo $this->_getFrontOffice();
?>