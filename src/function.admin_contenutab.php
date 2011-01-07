<?php
if (!isset($gCms)) exit;

// Verification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Server Prefs')) 
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));

// Get the historique
$query =  'SELECT r.id as rapportid, u.cni, r.date_reception, r.reponse, r.rapport FROM '.cms_db_prefix().'module_oscs_rapport r,  '.cms_db_prefix().'module_oscs_user u ';
$query .= ' WHERE u.id = r.user_id ORDER BY r.id DESC limit 0,15';
$result = $db->Execute($query);

if ($result === false)
{
	echo "Database error durant la r&eacute;cup&eacute;ration des rapports!";
	exit;
}

$listeFrontal = array();

$admintheme =& $gCms->variables['admintheme'];
$i=0;

while ($row = $result->FetchRow())
{
	$obj = new stdClass;
	$obj->cni = $row['cni'];
	$obj->date_reception = $db->UnixTimeStamp($row['date_reception']);
	$obj->reponse = $row['reponse'];
	$obj->rowclass = ($i++%2 == 0?'row1':'row2');
	$obj->showlink = $this->CreateLink($id, 'admin_showRapport', $returnid, $admintheme->DisplayImage('icons/system/view.gif', $this->Lang('consulter'),'','','systemicon'), array('rapportid'=>$row['rapportid']));
	
	$listeFrontal[] = $obj;
}

$smarty->assign('listeContenu',$listeFrontal);
?>