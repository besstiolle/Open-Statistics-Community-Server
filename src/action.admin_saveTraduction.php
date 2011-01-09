<?php

if (!isset($gCms)) exit;

// Verification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Server Prefs')) 
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));

    
$listeLigneTmp = explode('<br />',nl2br($params['area']));
$listeLigne = array();
$tmp = array();
foreach($listeLigneTmp as $ligne)
{
	if(substr($ligne, 0, 8) == "\r\n    * " && $ligne != "\r\n    * --------")
	{

		$ligne = str_replace("\r\n    * ", "",$ligne);
		$ligne = str_replace("Complete)", "",$ligne);
		$ligne = str_replace(" ", "",$ligne);
		list($name, $complement) =  explode("(", $ligne);
		list($done, $total) =  explode("/", $complement);
		
		$module = new stdclass();
		$module->name = $name;
		$module->done = $done;
		$module->total = $total;
		$listeLigne[$module->name] = $module;
	} 
}

$queryInsert = 'INSERT INTO '.cms_db_prefix().'module_oscs_traduction (texte,date_traduction) values (?,?)';
$param = array(serialize($listeLigne), $this->_getTimeForDB($db));
$result = $db->Execute($queryInsert, $param);
if ($result === false){die("Database error durant l'insert de la donn&eacute;e!");}
// redirect back to default admin page
$this->Redirect($id, 'defaultadmin', $returnid, array('tab_message'=> 'traductionupdated', 'active_tab' => 'traduction'));

?>