<?php

if (!isset($gCms)) exit;

$db = &$gCms->GetDb();

if(!isset($params['mod']))
{
	echo "module non g&eacute;r&eacute;";
	return;
}

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


$filename = $folder.DIRECTORY_SEPARATOR.$params['mois'];
$handle = fopen($filename,'r');
$contentstats = fread($handle, filesize($filename));
fclose($handle);
$arraySerialise = explode('|||',$contentstats);


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


$smarty->assign('mois',substr($params['mois'],4,2).'/'.substr($params['mois'],0,4));
$smarty->assign_by_ref('module',$this);

$backlink = $this->CreateLink($id, 'default', $returnid, 'revenir &agrave; la page pr&eacute;c&eacute;dente', array("mois"=>$params['mois']));
$this->smarty->assign('backlink',$backlink);


echo $this->ProcessTemplate('vueDetail.tpl');

?>