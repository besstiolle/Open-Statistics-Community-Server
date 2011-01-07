<?php
if (!isset($gCms)) exit;

// Verification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Server Prefs')) 
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));

// Liste des mois disponible
$query =  'SELECT  distinct DATE_FORMAT(date_reception, \'%Y%m\') as mois FROM '.cms_db_prefix().'module_oscs_rapport';
$query .= ' ORDER BY mois DESC limit 0,15';
$result = $db->Execute($query);

if ($result === false)
{
	//echo $query."<br/>";
	echo "Database error durant la r&eacute;cup&eacute;ration des mois!";
	exit;
}

$listeMois = array();

$admintheme =& $gCms->variables['admintheme'];
$i=0;
while ($row = $result->FetchRow())
{
	
	$obj = new stdClass;
	$obj->mois = $row['mois'];
	$obj->rowclass = ($i++%2 == 0?'row1':'row2');
	$obj->generatelink = $this->CreateLink($id, 'admin_generateStats', $returnid, $admintheme->DisplayImage('icons/system/view.gif', $this->Lang('generer'),'','','systemicon'), array('mois'=>$row['mois']));
	$listeMois[] = $obj;
}

$smarty->assign('listeMois',$listeMois);
?>