<?php


if (!isset($gCms)) exit;

$prettyUrlRapport = "statistiques/";

require("inc.header.php");

$prettyUrl = "statistiques/modules/".$returnid."/".$mois;
$smarty->assign('linkListeModule',$this->CreateLink($id, '', '', 'Liste des modules install&eacute;s',array(),'',false,true,'',false, $prettyUrl));

$prettyUrl = "statistiques/".$returnid."/".$mois;
$smarty->assign('linkDefault',$this->CreateLink($id, '', '', 'Sommaire',array(),'',false,true,'',false, $prettyUrl));

$prettyUrl = "statistiques/top/".$returnid."/".$mois;
$smarty->assign('linkTop',$this->CreateLink($id, '', '', 'Autres statistiques Cms',array(),'',false,true,'',false, $prettyUrl));

$db = &$gCms->GetDb();
$smarty = &$gCms->GetSmarty();

$query = 'SELECT count(*) as cpt FROM '.cms_db_prefix().'module_oscs_user';
$cpt = $db->getOne($query);
$smarty->assign('cptUser',$cpt);

$query = 'SELECT count(*) as cpt FROM '.cms_db_prefix().'module_oscs_rapport';
$cpt = $db->getOne($query);
$smarty->assign('cptRapport',$cpt);

$query = 'SELECT min(date_reception) as date_reception FROM '.cms_db_prefix().'module_oscs_rapport';
$cpt = $db->getOne($query);
$smarty->assign('min_date_reception',$cpt);

//echo $this->ProcessTemplate('frontoffice.tpl');

#Display template
echo "<!-- Displaying OSCS Module -->\n";
$template = 'frontoffice'.$this->GetPreference('current_frontoffice_template');
if (isset($params['template']))
  {
    $template = 'frontoffice'.$params['template'];
  }
echo $this->ProcessTemplateFromDatabase($template);
echo "<!-- END OSCS Module -->\n";
?>