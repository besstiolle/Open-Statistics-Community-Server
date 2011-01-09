<?php

require dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))) . DIRECTORY_SEPARATOR . 'include.php';
	
function getCle($db)
{	
	$cle = getNewCode();
	// Recuperer le User et ses cles
	$query = 'SELECT count(*) as cpt FROM '.cms_db_prefix().'module_oscs_user WHERE cni = ?';

	$result = $db->Execute($query,array($cle));
	if ($result === false){die("Database error durant la recherche de l'utilisateur!");}
	
	while ($row = $result->FetchRow())
	{
		if($row['cpt'] != 0)
		{
			return getCle();
		}
		break;
	}
	return $cle;

}

function getNewCode()
{	
	$cles = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$len = strlen($cles);
	$code = "";
	for($i = 0; $i < 50; $i++)
	{
		$code .= substr($cles, rand(0, $len-1),1);
	}
	return $code;
}

$db =& $gCms->GetDb();
$oscs =& $gCms->modules["OpenStatisticsCommunityServer"]['object'];

//Arrive a ce niveau on enregistre les resultats
$query = 'INSERT INTO '.cms_db_prefix().'module_oscs_user ( id , cni, clepublic, cleprivee, date_creation, date_update) values (?,?,?,?,?,?)';
$sid = $db->GenID(cms_db_prefix().'module_oscs_user_seq');
$time = $oscs->_getTimeForDB($db);
$newCode = getCle($db);
$cles = getNewCode();

$param = array($sid, $newCode, $cles, null, $time, null);
$result = $db->Execute($query, $param);

if (!$result){die("Database error durant la livraison du CNI!");}

//echo strlen($newCode) ."|". strlen($cles);
echo "$newCode|$cles";

?>