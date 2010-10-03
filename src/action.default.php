<?php

if (!isset($gCms)) exit;

$db = &$gCms->GetDb();

$query = 'SELECT count(*) as cpt FROM '.cms_db_prefix().'module_oscs_user';
$cpt = $db->getOne($query);
$smarty->assign('cptUser',$cpt);

$query = 'SELECT count(*) as cpt FROM '.cms_db_prefix().'module_oscs_rapport';
$cpt = $db->getOne($query);
$smarty->assign('cptRapport',$cpt);

$query = 'SELECT min(date_reception) as date_reception FROM '.cms_db_prefix().'module_oscs_rapport';
$cpt = $db->getOne($query);
$smarty->assign('min_date_reception',$cpt);


echo $this->ProcessTemplate('frontoffice.tpl');

$folder1 = '.'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'OpenStatisticsCommunityServer'. DIRECTORY_SEPARATOR .'stats';
$folder2 = '..'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'OpenStatisticsCommunityServer'. DIRECTORY_SEPARATOR .'stats';
if(is_dir($folder1))
	$folder = $folder1;
else if(is_dir($folder2))
	$folder = $folder2;
else
	die("erreur grave : aucun répertoire ne correspond :/");
		
$dossier = opendir($folder);
$listeFichier = array();
while ($fichier = readdir($dossier)) {
  if ($fichier != "." && $fichier != "..") 
    $listeFichier[] = $fichier;
}
rsort($listeFichier);
closedir($dossier);

//Si aucun mois définit
if(!isset($params['mois']))
{
	foreach($listeFichier as $fichier)
	{	
		$params['mois'] = $fichier;
		break;
	}
} else
{
	$existe = false;
	foreach($listeFichier as $fichier)
	{	
		if($params['mois'] == $fichier)
		{
			$existe = true;
			break;
		}
	}
	if(!$existe)
	{
		echo "les statiques de ce mois ne sont pas encore g&eacute;n&eacute;r&eacute;e :)";
		return;
	}
}

$listeMois = array();
foreach($listeFichier as $fichier)
{
	$obj = new stdClass;
	$obj->generatelink = $this->CreateLink($id, 'default', $returnid, substr($fichier,4,2).'/'.substr($fichier,0,4), array('mois'=>$fichier));
	$listeMois[] = $obj;
}	
	

$filename = $folder.DIRECTORY_SEPARATOR.$params['mois'];
$handle = fopen($filename,'r');
$contentstats = fread($handle, filesize($filename));
fclose($handle);

$arraySerialise = explode('|||',$contentstats);


/* ############## PARTIE A FAIRE EVOLUER ################ */
$syntheseCmsVersion = unserialize($arraySerialise[0]);
$line1 = unserialize($arraySerialise[1]);
$syntheseCmsModule = unserialize($arraySerialise[2]);
$synthesePhp = unserialize($arraySerialise[3]);
$line2 = unserialize($arraySerialise[4]);
$syntheseMemoryLimit = unserialize($arraySerialise[5]);
$line3 = unserialize($arraySerialise[6]);
$syntheseSafeMode = unserialize($arraySerialise[7]);
$line4 = unserialize($arraySerialise[8]);


//On refait une passe sur la liste des modules pour mettre à jour les liens et le paramètre $mois
foreach ($syntheseCmsModule as $module)
{
	$detailLien = $this->CreateLink($id, 'showModule', $returnid, $module->name, array("mois"=>$params['mois'],"mod"=>$module->name));
	$module->detailLien = $detailLien;
}

$smarty->assign('syntheseCmsVersion',$syntheseCmsVersion);
$smarty->assign('syntheseCmsModule',$syntheseCmsModule);
$smarty->assign('synthesePhp',$synthesePhp);
$smarty->assign('syntheseMemoryLimit',$syntheseMemoryLimit);
$smarty->assign('syntheseSafeMode',$syntheseSafeMode);
$smarty->assign('line1',$line1);
$smarty->assign('line2',$line2);
$smarty->assign('line3',$line3);
$smarty->assign('line4',$line4);
/* ############## PARTIE A FAIRE EVOLUER ################ */


$smarty->assign('listeMois',$listeMois);
$smarty->assign('mois',substr($params['mois'],4,2).'/'.substr($params['mois'],0,4));
$smarty->assign_by_ref('module',$this);

echo $this->ProcessTemplate('vueGenerale.tpl');

?>