<?php

if (!isset($gCms)) exit;

$prettyUrlRapport = "statistiques/top/";

require_once("inc.header.php");

/* ############## PARTIE A FAIRE EVOLUER ################ */
$syntheseCmsVersion = unserialize($arraySerialise[0]);
$line1 = unserialize($arraySerialise[1]);
$line2 = unserialize($arraySerialise[4]);
$line3 = unserialize($arraySerialise[6]);
$line4 = unserialize($arraySerialise[8]);

$smarty->assign('syntheseCmsVersion',$syntheseCmsVersion);
$smarty->assign('line1',$line1);
$smarty->assign('line2',$line2);
$smarty->assign('line3',$line3);
$smarty->assign('line4',$line4);
/* ############## PARTIE A FAIRE EVOLUER ################ */

$smarty->assign('mois',$this->_getMoisLiteral($mois));
$smarty->assign_by_ref('module',$this);

echo $this->ProcessTemplate('showTop.tpl');

?>