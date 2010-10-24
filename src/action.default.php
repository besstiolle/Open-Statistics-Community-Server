<?php

if (!isset($gCms)) exit;

$prettyUrlRapport = "statistiques/";

require_once("inc.header.php");

/* ############## PARTIE A FAIRE EVOLUER ################ */
$line1 = unserialize($arraySerialise[1]);
$smarty->assign('line1',$line1);
/* ############## PARTIE A FAIRE EVOLUER ################ */



$smarty->assign('mois',$this->_getMoisLiteral($mois));
$smarty->assign_by_ref('module',$this);

echo $this->ProcessTemplate('default.tpl');

?>