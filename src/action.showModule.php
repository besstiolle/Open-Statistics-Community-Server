<?php

if (!isset($gCms)) exit;

$prettyUrlRapport = "statistiques/modules/";

require_once("inc.header.php");

/* ############## PARTIE A FAIRE EVOLUER ################ */
$syntheseCmsModule = unserialize($arraySerialise[2]);


$module = null;
foreach($syntheseCmsModule as $mod_tmp)
{
	if($mod_tmp->name == $params['mod'])
	{
		$module = $mod_tmp;
		break;
	}
}
if($module == null)
{
	echo "module ".$params['mod']."non g&eacute;r&eacute;";
	return;
}

$smarty->assign('myModule',$module);


$smarty->assign('mois',$this->_getMoisLiteral($mois));
$smarty->assign_by_ref('module',$this);

$backlink = $this->CreateLink($id, 'default', $returnid, 'revenir &agrave; la page pr&eacute;c&eacute;dente', array("mois"=>$params['mois']));
$this->smarty->assign('backlink',$backlink);


echo $this->ProcessTemplate('vueDetail.tpl');

?>